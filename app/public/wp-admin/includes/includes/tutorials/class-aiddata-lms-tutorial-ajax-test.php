<?php
/**
 * Tutorial AJAX Handlers Test Suite
 *
 * Tests AJAX functionality for enrollment, unenrollment, progress updates,
 * and time tracking.
 *
 * @package AidData_LMS
 * @subpackage Tutorials/Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Tutorial_AJAX_Test
 *
 * Comprehensive test suite for tutorial AJAX handlers.
 *
 * @since 1.0.0
 */
class AidData_LMS_Tutorial_AJAX_Test {

	/**
	 * Test results storage.
	 *
	 * @var array
	 */
	private $results = array();

	/**
	 * Test user ID.
	 *
	 * @var int
	 */
	private $test_user_id;

	/**
	 * Test tutorial ID.
	 *
	 * @var int
	 */
	private $test_tutorial_id;

	/**
	 * AJAX handler instance.
	 *
	 * @var AidData_LMS_Tutorial_AJAX
	 */
	private $ajax_handler;

	/**
	 * Enrollment manager instance.
	 *
	 * @var AidData_LMS_Tutorial_Enrollment
	 */
	private $enrollment_manager;

	/**
	 * Progress manager instance.
	 *
	 * @var AidData_LMS_Tutorial_Progress
	 */
	private $progress_manager;

	/**
	 * Run all tests.
	 *
	 * @return array Test results
	 */
	public function run_all_tests(): array {
		$this->results = array();

		// Setup
		$this->setup_test_data();

		// Run tests
		$this->test_ajax_class_instantiation();
		$this->test_enrollment_ajax_validation();
		$this->test_enrollment_ajax_success();
		$this->test_enrollment_ajax_duplicate();
		$this->test_unenrollment_ajax_validation();
		$this->test_unenrollment_ajax_success();
		$this->test_enrollment_status_check_not_enrolled();
		$this->test_enrollment_status_check_enrolled();
		$this->test_progress_update_ajax_validation();
		$this->test_progress_update_ajax_not_enrolled();
		$this->test_progress_update_ajax_success();
		$this->test_time_update_ajax_validation();
		$this->test_time_update_ajax_success();
		$this->test_nonce_verification();
		$this->test_user_authentication();

		// Cleanup
		$this->cleanup_test_data();

		return $this->results;
	}

	/**
	 * Setup test data.
	 *
	 * @return void
	 */
	private function setup_test_data(): void {
		// Create test user
		$this->test_user_id = wp_create_user(
			'ajax_test_user_' . time(),
			'password123',
			'ajaxtest' . time() . '@example.com'
		);

		// Create test tutorial
		$this->test_tutorial_id = wp_insert_post(
			array(
				'post_title'   => 'AJAX Test Tutorial',
				'post_content' => 'Test content for AJAX',
				'post_status'  => 'publish',
				'post_type'    => 'aiddata_tutorial',
			)
		);

		// Add tutorial steps
		update_post_meta(
			$this->test_tutorial_id,
			'_tutorial_steps',
			array(
				array( 'title' => 'Step 1' ),
				array( 'title' => 'Step 2' ),
				array( 'title' => 'Step 3' ),
			)
		);

		// Initialize managers
		$this->ajax_handler       = new AidData_LMS_Tutorial_AJAX();
		$this->enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		$this->progress_manager   = new AidData_LMS_Tutorial_Progress();
	}

	/**
	 * Cleanup test data.
	 *
	 * @return void
	 */
	private function cleanup_test_data(): void {
		global $wpdb;

		// Delete test enrollments
		$enrollments_table = AidData_LMS_Database::get_table_name( 'enrollments' );
		$wpdb->delete(
			$enrollments_table,
			array( 'user_id' => $this->test_user_id ),
			array( '%d' )
		);

		// Delete test progress
		$progress_table = AidData_LMS_Database::get_table_name( 'progress' );
		$wpdb->delete(
			$progress_table,
			array( 'user_id' => $this->test_user_id ),
			array( '%d' )
		);

		// Delete test tutorial
		wp_delete_post( $this->test_tutorial_id, true );

		// Delete test user
		wp_delete_user( $this->test_user_id );
	}

	/**
	 * Test: AJAX class instantiation.
	 */
	private function test_ajax_class_instantiation(): void {
		$passed = $this->ajax_handler instanceof AidData_LMS_Tutorial_AJAX;

		$this->results[] = array(
			'test'    => 'AJAX Class Instantiation',
			'passed'  => $passed,
			'message' => $passed ? 'AJAX handler instantiated successfully' : 'Failed to instantiate AJAX handler',
		);
	}

	/**
	 * Test: Enrollment AJAX validation.
	 */
	private function test_enrollment_ajax_validation(): void {
		// Simulate AJAX request with invalid tutorial ID
		$_POST['tutorial_id'] = 0;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-enrollment' );

		// Set current user
		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->enroll_tutorial();
		} catch ( WPDieException $e ) {
			// Expected exception from wp_send_json_error
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = ! $response['success'] && strpos( $response['data']['message'], 'Invalid tutorial ID' ) !== false;

		$this->results[] = array(
			'test'    => 'Enrollment AJAX - Invalid Tutorial Validation',
			'passed'  => $passed,
			'message' => $passed ? 'Invalid tutorial ID properly rejected' : 'Validation failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['nonce'] );
	}

	/**
	 * Test: Successful enrollment via AJAX.
	 */
	private function test_enrollment_ajax_success(): void {
		// Simulate AJAX request
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-enrollment' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->enroll_tutorial();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = $response['success'] === true && isset( $response['data']['enrollment'] );

		$this->results[] = array(
			'test'    => 'Enrollment AJAX - Success',
			'passed'  => $passed,
			'message' => $passed ? 'User enrolled successfully via AJAX' : 'Enrollment failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['nonce'] );
	}

	/**
	 * Test: Duplicate enrollment prevention via AJAX.
	 */
	private function test_enrollment_ajax_duplicate(): void {
		// Enroll first (already enrolled from previous test)
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-enrollment' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->enroll_tutorial();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = ! $response['success'] && strpos( $response['data']['message'], 'already enrolled' ) !== false;

		$this->results[] = array(
			'test'    => 'Enrollment AJAX - Duplicate Prevention',
			'passed'  => $passed,
			'message' => $passed ? 'Duplicate enrollment properly prevented' : 'Duplicate check failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['nonce'] );
	}

	/**
	 * Test: Unenrollment AJAX validation.
	 */
	private function test_unenrollment_ajax_validation(): void {
		// Simulate AJAX request without confirm
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-enrollment' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->unenroll_tutorial();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = ! $response['success'] && strpos( $response['data']['message'], 'confirm' ) !== false;

		$this->results[] = array(
			'test'    => 'Unenrollment AJAX - Confirmation Required',
			'passed'  => $passed,
			'message' => $passed ? 'Confirmation required for unenrollment' : 'Validation failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['nonce'] );
	}

	/**
	 * Test: Successful unenrollment via AJAX.
	 */
	private function test_unenrollment_ajax_success(): void {
		// Simulate AJAX request with confirmation
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['confirm']     = 'yes';
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-enrollment' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->unenroll_tutorial();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = $response['success'] === true;

		$this->results[] = array(
			'test'    => 'Unenrollment AJAX - Success',
			'passed'  => $passed,
			'message' => $passed ? 'User unenrolled successfully via AJAX' : 'Unenrollment failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['confirm'], $_POST['nonce'] );
	}

	/**
	 * Test: Check enrollment status - not enrolled.
	 */
	private function test_enrollment_status_check_not_enrolled(): void {
		// Simulate AJAX request
		$_GET['tutorial_id'] = $this->test_tutorial_id;

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->check_enrollment_status();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = $response['success'] === true && $response['data']['enrolled'] === false;

		$this->results[] = array(
			'test'    => 'Check Enrollment Status - Not Enrolled',
			'passed'  => $passed,
			'message' => $passed ? 'Enrollment status check returned not enrolled' : 'Status check failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_GET['tutorial_id'] );
	}

	/**
	 * Test: Check enrollment status - enrolled.
	 */
	private function test_enrollment_status_check_enrolled(): void {
		// Enroll user first
		$this->enrollment_manager->enroll_user( $this->test_user_id, $this->test_tutorial_id, 'test' );

		// Simulate AJAX request
		$_GET['tutorial_id'] = $this->test_tutorial_id;

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->check_enrollment_status();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = $response['success'] === true && 
		           $response['data']['enrolled'] === true &&
		           isset( $response['data']['enrollment'] ) &&
		           isset( $response['data']['progress'] );

		$this->results[] = array(
			'test'    => 'Check Enrollment Status - Enrolled',
			'passed'  => $passed,
			'message' => $passed ? 'Enrollment status check returned enrolled with progress' : 'Status check failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_GET['tutorial_id'] );
	}

	/**
	 * Test: Progress update AJAX validation.
	 */
	private function test_progress_update_ajax_validation(): void {
		// Simulate AJAX request with invalid step
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['step_index']  = -1;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-progress' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->update_step_progress();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = ! $response['success'] && strpos( $response['data']['message'], 'Invalid' ) !== false;

		$this->results[] = array(
			'test'    => 'Progress Update AJAX - Invalid Parameters',
			'passed'  => $passed,
			'message' => $passed ? 'Invalid parameters properly rejected' : 'Validation failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['step_index'], $_POST['nonce'] );
	}

	/**
	 * Test: Progress update AJAX - not enrolled.
	 */
	private function test_progress_update_ajax_not_enrolled(): void {
		// Unenroll first
		$this->enrollment_manager->unenroll_user( $this->test_user_id, $this->test_tutorial_id );

		// Simulate AJAX request
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['step_index']  = 0;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-progress' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->update_step_progress();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = ! $response['success'] && strpos( $response['data']['message'], 'not enrolled' ) !== false;

		$this->results[] = array(
			'test'    => 'Progress Update AJAX - Not Enrolled',
			'passed'  => $passed,
			'message' => $passed ? 'Non-enrolled user properly prevented from updating progress' : 'Enrollment check failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['step_index'], $_POST['nonce'] );
	}

	/**
	 * Test: Successful progress update via AJAX.
	 */
	private function test_progress_update_ajax_success(): void {
		// Enroll user first
		$this->enrollment_manager->enroll_user( $this->test_user_id, $this->test_tutorial_id, 'test' );

		// Simulate AJAX request
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['step_index']  = 0;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-progress' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->update_step_progress();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = $response['success'] === true && 
		           isset( $response['data']['progress'] ) &&
		           $response['data']['progress']['current_step'] === 0;

		$this->results[] = array(
			'test'    => 'Progress Update AJAX - Success',
			'passed'  => $passed,
			'message' => $passed ? 'Progress updated successfully via AJAX' : 'Progress update failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['step_index'], $_POST['nonce'] );
	}

	/**
	 * Test: Time update AJAX validation.
	 */
	private function test_time_update_ajax_validation(): void {
		// Simulate AJAX request with invalid time
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['seconds']     = 0;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-progress' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->update_time_spent();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = ! $response['success'] && strpos( $response['data']['message'], 'Invalid' ) !== false;

		$this->results[] = array(
			'test'    => 'Time Update AJAX - Invalid Parameters',
			'passed'  => $passed,
			'message' => $passed ? 'Invalid time properly rejected' : 'Validation failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['seconds'], $_POST['nonce'] );
	}

	/**
	 * Test: Successful time update via AJAX.
	 */
	private function test_time_update_ajax_success(): void {
		// Simulate AJAX request
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['seconds']     = 30;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-progress' );

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->update_time_spent();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = $response['success'] === true;

		$this->results[] = array(
			'test'    => 'Time Update AJAX - Success',
			'passed'  => $passed,
			'message' => $passed ? 'Time updated successfully via AJAX' : 'Time update failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['seconds'], $_POST['nonce'] );
	}

	/**
	 * Test: Nonce verification.
	 */
	private function test_nonce_verification(): void {
		// Simulate AJAX request with invalid nonce
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['nonce']       = 'invalid_nonce';

		wp_set_current_user( $this->test_user_id );

		// Capture output
		ob_start();
		$exception_caught = false;
		try {
			$this->ajax_handler->enroll_tutorial();
		} catch ( Exception $e ) {
			$exception_caught = true;
		}
		ob_get_clean();

		$passed = $exception_caught;

		$this->results[] = array(
			'test'    => 'Nonce Verification',
			'passed'  => $passed,
			'message' => $passed ? 'Invalid nonce properly rejected' : 'Nonce verification failed',
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['nonce'] );
	}

	/**
	 * Test: User authentication.
	 */
	private function test_user_authentication(): void {
		// Log out user
		wp_set_current_user( 0 );

		// Simulate AJAX request
		$_POST['tutorial_id'] = $this->test_tutorial_id;
		$_POST['nonce']       = wp_create_nonce( 'aiddata-lms-enrollment' );

		// Capture output
		ob_start();
		try {
			$this->ajax_handler->enroll_tutorial();
		} catch ( WPDieException $e ) {
			// Expected
		}
		$output = ob_get_clean();

		$response = json_decode( $output, true );
		$passed   = ! $response['success'] && strpos( $response['data']['message'], 'logged in' ) !== false;

		$this->results[] = array(
			'test'    => 'User Authentication',
			'passed'  => $passed,
			'message' => $passed ? 'Unauthenticated user properly rejected' : 'Authentication check failed: ' . print_r( $response, true ),
		);

		// Clean up
		unset( $_POST['tutorial_id'], $_POST['nonce'] );
	}

	/**
	 * Display test results.
	 *
	 * @return void
	 */
	public function display_results(): void {
		$total_tests  = count( $this->results );
		$passed_tests = count( array_filter( $this->results, fn( $r ) => $r['passed'] ) );
		$failed_tests = $total_tests - $passed_tests;

		echo '<div class="wrap">';
		echo '<h1>Tutorial AJAX Handlers Test Results</h1>';

		// Summary
		echo '<div class="notice notice-' . ( $failed_tests === 0 ? 'success' : 'warning' ) . '">';
		echo '<p><strong>Summary:</strong> ' . $passed_tests . '/' . $total_tests . ' tests passed</p>';
		echo '</div>';

		// Results table
		echo '<table class="wp-list-table widefat striped">';
		echo '<thead><tr>';
		echo '<th>Test</th>';
		echo '<th>Status</th>';
		echo '<th>Message</th>';
		echo '</tr></thead>';
		echo '<tbody>';

		foreach ( $this->results as $result ) {
			$status_class = $result['passed'] ? 'notice-success' : 'notice-error';
			$status_text  = $result['passed'] ? '✅ PASS' : '❌ FAIL';

			echo '<tr>';
			echo '<td><strong>' . esc_html( $result['test'] ) . '</strong></td>';
			echo '<td><span class="notice ' . $status_class . ' inline">' . $status_text . '</span></td>';
			echo '<td>' . esc_html( $result['message'] ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</div>';
	}
}

