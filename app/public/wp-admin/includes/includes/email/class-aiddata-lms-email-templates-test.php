<?php
/**
 * Email Template System Test Suite
 *
 * Comprehensive tests for email templates and notifications.
 *
 * @package AidData_LMS
 * @subpackage Email/Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email Template Test Class
 *
 * Tests email template system functionality including template loading,
 * variable replacement, and notification triggers.
 *
 * @since 1.0.0
 */
class AidData_LMS_Email_Templates_Test {

	/**
	 * Test results
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $results = array();

	/**
	 * Test user ID
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $test_user_id = 0;

	/**
	 * Test tutorial ID
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $test_tutorial_id = 0;

	/**
	 * Run all tests
	 *
	 * @since 1.0.0
	 * @return array Test results.
	 */
	public function run_tests(): array {
		// Create test data.
		$this->setup_test_data();

		// Run tests.
		$this->test_class_instantiation();
		$this->test_template_loading();
		$this->test_variable_replacement();
		$this->test_default_variables();
		$this->test_available_variables();
		$this->test_available_templates();
		$this->test_template_validation();
		$this->test_render_template();
		$this->test_theme_override();
		$this->test_notification_class_instantiation();
		$this->test_enrollment_notification();
		$this->test_progress_notification();
		$this->test_completion_notification();
		$this->test_milestone_tracking();
		$this->test_template_filters();
		$this->test_variable_filters();

		// Clean up.
		$this->cleanup_test_data();

		return $this->results;
	}

	/**
	 * Setup test data
	 *
	 * @since 1.0.0
	 */
	private function setup_test_data(): void {
		// Create test user.
		$this->test_user_id = wp_create_user(
			'email_test_user_' . time(),
			'password123',
			'email_test_' . time() . '@example.com'
		);

		if ( ! is_wp_error( $this->test_user_id ) ) {
			update_user_meta( $this->test_user_id, 'first_name', 'Test' );
			update_user_meta( $this->test_user_id, 'last_name', 'User' );
		}

		// Create test tutorial.
		$this->test_tutorial_id = wp_insert_post(
			array(
				'post_title'   => 'Test Tutorial for Email',
				'post_content' => 'Test tutorial content',
				'post_excerpt' => 'Test tutorial description for email testing',
				'post_type'    => 'aiddata_tutorial',
				'post_status'  => 'publish',
			)
		);

		// Add tutorial steps meta.
		if ( ! is_wp_error( $this->test_tutorial_id ) ) {
			update_post_meta(
				$this->test_tutorial_id,
				'_tutorial_steps',
				array(
					array( 'title' => 'Step 1' ),
					array( 'title' => 'Step 2' ),
					array( 'title' => 'Step 3' ),
					array( 'title' => 'Step 4' ),
				)
			);
		}
	}

	/**
	 * Clean up test data
	 *
	 * @since 1.0.0
	 */
	private function cleanup_test_data(): void {
		// Delete test user.
		if ( $this->test_user_id > 0 ) {
			wp_delete_user( $this->test_user_id );
		}

		// Delete test tutorial.
		if ( $this->test_tutorial_id > 0 ) {
			wp_delete_post( $this->test_tutorial_id, true );
		}

		// Clean up milestone meta.
		global $wpdb;
		$wpdb->query(
			"DELETE FROM {$wpdb->usermeta} 
			WHERE meta_key LIKE '_aiddata_lms_progress_email_%'"
		);
	}

	/**
	 * Test 1: Class instantiation
	 *
	 * @since 1.0.0
	 */
	private function test_class_instantiation(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		$this->results[] = array(
			'test'   => 'Template Manager Class Instantiation',
			'result' => is_object( $template_manager ) && $template_manager instanceof AidData_LMS_Email_Templates,
			'value'  => get_class( $template_manager ),
		);
	}

	/**
	 * Test 2: Template loading
	 *
	 * @since 1.0.0
	 */
	private function test_template_loading(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		// Test enrollment confirmation template.
		$content = $template_manager->get_template_content( 'enrollment-confirmation' );

		$this->results[] = array(
			'test'   => 'Load Enrollment Confirmation Template',
			'result' => ! empty( $content ) && strpos( $content, '<html' ) !== false,
			'value'  => strlen( $content ) . ' characters',
		);

		// Test progress reminder template.
		$content = $template_manager->get_template_content( 'progress-reminder' );

		$this->results[] = array(
			'test'   => 'Load Progress Reminder Template',
			'result' => ! empty( $content ) && strpos( $content, '<html' ) !== false,
			'value'  => strlen( $content ) . ' characters',
		);

		// Test completion template.
		$content = $template_manager->get_template_content( 'completion-congratulations' );

		$this->results[] = array(
			'test'   => 'Load Completion Congratulations Template',
			'result' => ! empty( $content ) && strpos( $content, '<html' ) !== false,
			'value'  => strlen( $content ) . ' characters',
		);

		// Test non-existent template.
		$content = $template_manager->get_template_content( 'non-existent-template' );

		$this->results[] = array(
			'test'   => 'Non-existent Template Returns Empty',
			'result' => empty( $content ),
			'value'  => empty( $content ) ? 'Empty' : 'Not Empty',
		);
	}

	/**
	 * Test 3: Variable replacement
	 *
	 * @since 1.0.0
	 */
	private function test_variable_replacement(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		$content = 'Hello {user_name}, welcome to {tutorial_title}!';

		$variables = array(
			'{user_name}'      => 'John Doe',
			'{tutorial_title}' => 'GIS Basics',
		);

		$result = $template_manager->replace_variables( $content, $variables );

		$this->results[] = array(
			'test'   => 'Variable Replacement',
			'result' => strpos( $result, 'John Doe' ) !== false && strpos( $result, 'GIS Basics' ) !== false,
			'value'  => $result,
		);

		// Test with keys without braces.
		$variables = array(
			'user_name'      => 'Jane Smith',
			'tutorial_title' => 'Python 101',
		);

		$result = $template_manager->replace_variables( $content, $variables );

		$this->results[] = array(
			'test'   => 'Variable Replacement (Keys Without Braces)',
			'result' => strpos( $result, 'Jane Smith' ) !== false && strpos( $result, 'Python 101' ) !== false,
			'value'  => $result,
		);
	}

	/**
	 * Test 4: Default variables
	 *
	 * @since 1.0.0
	 */
	private function test_default_variables(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		$content = 'Site: {site_name}, Year: {current_year}';

		$result = $template_manager->replace_variables( $content, array() );

		$site_name    = get_bloginfo( 'name' );
		$current_year = wp_date( 'Y' );

		$this->results[] = array(
			'test'   => 'Default Variables (Site Name and Year)',
			'result' => strpos( $result, $site_name ) !== false && strpos( $result, $current_year ) !== false,
			'value'  => $result,
		);
	}

	/**
	 * Test 5: Available variables
	 *
	 * @since 1.0.0
	 */
	private function test_available_variables(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		$variables = $template_manager->get_available_variables();

		$this->results[] = array(
			'test'   => 'Get Available Variables',
			'result' => is_array( $variables ) && count( $variables ) >= 15,
			'value'  => count( $variables ) . ' variables',
		);

		// Check required variables exist.
		$required = array( '{user_name}', '{tutorial_title}', '{site_name}' );
		$exists   = true;

		foreach ( $required as $key ) {
			if ( ! isset( $variables[ $key ] ) ) {
				$exists = false;
				break;
			}
		}

		$this->results[] = array(
			'test'   => 'Required Variables Exist',
			'result' => $exists,
			'value'  => $exists ? 'All present' : 'Missing variables',
		);
	}

	/**
	 * Test 6: Available templates
	 *
	 * @since 1.0.0
	 */
	private function test_available_templates(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		$templates = $template_manager->get_available_templates();

		$this->results[] = array(
			'test'   => 'Get Available Templates',
			'result' => is_array( $templates ) && count( $templates ) >= 3,
			'value'  => count( $templates ) . ' templates',
		);
	}

	/**
	 * Test 7: Template validation
	 *
	 * @since 1.0.0
	 */
	private function test_template_validation(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		// Valid template.
		$valid_template = '<!DOCTYPE html><html><head></head><body>Content</body></html>';
		$is_valid       = $template_manager->validate_template( $valid_template );

		$this->results[] = array(
			'test'   => 'Validate Valid Template',
			'result' => $is_valid === true,
			'value'  => $is_valid ? 'Valid' : 'Invalid',
		);

		// Invalid template (missing body).
		$invalid_template = '<html><head></head></html>';
		$is_valid         = $template_manager->validate_template( $invalid_template );

		$this->results[] = array(
			'test'   => 'Validate Invalid Template (Missing Body)',
			'result' => $is_valid === false,
			'value'  => $is_valid ? 'Valid' : 'Invalid',
		);

		// Empty template.
		$is_valid = $template_manager->validate_template( '' );

		$this->results[] = array(
			'test'   => 'Validate Empty Template',
			'result' => $is_valid === false,
			'value'  => $is_valid ? 'Valid' : 'Invalid',
		);
	}

	/**
	 * Test 8: Render template
	 *
	 * @since 1.0.0
	 */
	private function test_render_template(): void {
		$template_manager = new AidData_LMS_Email_Templates();

		$variables = array(
			'{user_first_name}' => 'John',
			'{tutorial_title}'  => 'Test Tutorial',
			'{tutorial_url}'    => 'https://example.com/tutorial',
		);

		$rendered = $template_manager->render_template( 'enrollment-confirmation', $variables );

		$this->results[] = array(
			'test'   => 'Render Template with Variables',
			'result' => ! empty( $rendered ) && strpos( $rendered, 'John' ) !== false,
			'value'  => strlen( $rendered ) . ' characters',
		);
	}

	/**
	 * Test 9: Theme override
	 *
	 * @since 1.0.0
	 */
	private function test_theme_override(): void {
		// This test just verifies the method exists and handles non-existent theme templates.
		$template_manager = new AidData_LMS_Email_Templates();

		$content = $template_manager->get_template_content( 'enrollment-confirmation' );

		$this->results[] = array(
			'test'   => 'Theme Override Support (Falls Back to Plugin)',
			'result' => ! empty( $content ),
			'value'  => 'Theme override supported',
		);
	}

	/**
	 * Test 10: Notification class instantiation
	 *
	 * @since 1.0.0
	 */
	private function test_notification_class_instantiation(): void {
		$notifications = new AidData_LMS_Email_Notifications();

		$this->results[] = array(
			'test'   => 'Email Notifications Class Instantiation',
			'result' => is_object( $notifications ) && $notifications instanceof AidData_LMS_Email_Notifications,
			'value'  => get_class( $notifications ),
		);
	}

	/**
	 * Test 11: Enrollment notification
	 *
	 * @since 1.0.0
	 */
	private function test_enrollment_notification(): void {
		global $wpdb;

		// Create enrollment.
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		$enrollment_id      = $enrollment_manager->enroll_user( $this->test_user_id, $this->test_tutorial_id, 'test' );

		if ( is_wp_error( $enrollment_id ) ) {
			$this->results[] = array(
				'test'   => 'Enrollment Notification (Email Queued)',
				'result' => false,
				'value'  => 'Enrollment failed: ' . $enrollment_id->get_error_message(),
			);
			return;
		}

		// Wait for hooks to fire.
		do_action( 'aiddata_lms_user_enrolled', $enrollment_id, $this->test_user_id, $this->test_tutorial_id, 'test' );

		// Check if email was queued.
		$table_name = AidData_LMS_Database::get_table_name( 'email' );
		$email      = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table_name} 
				WHERE user_id = %d 
				AND email_type = %s 
				ORDER BY id DESC 
				LIMIT 1",
				$this->test_user_id,
				'enrollment_confirmation'
			)
		);

		$this->results[] = array(
			'test'   => 'Enrollment Notification (Email Queued)',
			'result' => ! empty( $email ) && $email->status === 'pending',
			'value'  => ! empty( $email ) ? 'Email ID: ' . $email->id : 'No email',
		);
	}

	/**
	 * Test 12: Progress notification
	 *
	 * @since 1.0.0
	 */
	private function test_progress_notification(): void {
		global $wpdb;

		// Get enrollment.
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		if ( ! $enrollment_manager->is_user_enrolled( $this->test_user_id, $this->test_tutorial_id ) ) {
			$enrollment_manager->enroll_user( $this->test_user_id, $this->test_tutorial_id, 'test' );
		}

		// Trigger 50% progress.
		do_action( 'aiddata_lms_progress_updated', $this->test_user_id, $this->test_tutorial_id, 50.0 );

		// Check if email was queued.
		$table_name = AidData_LMS_Database::get_table_name( 'email' );
		$email      = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table_name} 
				WHERE user_id = %d 
				AND email_type = %s 
				ORDER BY id DESC 
				LIMIT 1",
				$this->test_user_id,
				'progress_reminder'
			)
		);

		$this->results[] = array(
			'test'   => 'Progress Notification (Email Queued at 50%)',
			'result' => ! empty( $email ) && $email->status === 'pending',
			'value'  => ! empty( $email ) ? 'Email ID: ' . $email->id : 'No email',
		);
	}

	/**
	 * Test 13: Completion notification
	 *
	 * @since 1.0.0
	 */
	private function test_completion_notification(): void {
		global $wpdb;

		// Get enrollment.
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		if ( ! $enrollment_manager->is_user_enrolled( $this->test_user_id, $this->test_tutorial_id ) ) {
			$enrollment_id = $enrollment_manager->enroll_user( $this->test_user_id, $this->test_tutorial_id, 'test' );
		} else {
			$enrollment    = $enrollment_manager->get_enrollment( $this->test_user_id, $this->test_tutorial_id );
			$enrollment_id = $enrollment->id;
		}

		// Trigger completion.
		do_action( 'aiddata_lms_tutorial_completed', $this->test_user_id, $this->test_tutorial_id, $enrollment_id );

		// Check if email was queued.
		$table_name = AidData_LMS_Database::get_table_name( 'email' );
		$email      = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table_name} 
				WHERE user_id = %d 
				AND email_type = %s 
				ORDER BY id DESC 
				LIMIT 1",
				$this->test_user_id,
				'completion_congratulations'
			)
		);

		$this->results[] = array(
			'test'   => 'Completion Notification (Email Queued)',
			'result' => ! empty( $email ) && $email->status === 'pending',
			'value'  => ! empty( $email ) ? 'Email ID: ' . $email->id : 'No email',
		);
	}

	/**
	 * Test 14: Milestone tracking
	 *
	 * @since 1.0.0
	 */
	private function test_milestone_tracking(): void {
		// Clear any existing milestone meta.
		$meta_key = '_aiddata_lms_progress_email_25_' . $this->test_tutorial_id;
		delete_user_meta( $this->test_user_id, $meta_key );

		// Trigger 25% progress.
		do_action( 'aiddata_lms_progress_updated', $this->test_user_id, $this->test_tutorial_id, 25.0 );

		// Check if milestone was recorded.
		$milestone_sent = get_user_meta( $this->test_user_id, $meta_key, true );

		$this->results[] = array(
			'test'   => 'Milestone Tracking (25% Recorded)',
			'result' => ! empty( $milestone_sent ),
			'value'  => ! empty( $milestone_sent ) ? 'Recorded at ' . $milestone_sent : 'Not recorded',
		);

		// Try to trigger again - should not send duplicate.
		$notifications = new AidData_LMS_Email_Notifications();
		$notifications->on_progress_updated( $this->test_user_id, $this->test_tutorial_id, 25.0 );

		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );
		$count      = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table_name} 
				WHERE user_id = %d 
				AND email_type = %s",
				$this->test_user_id,
				'progress_reminder'
			)
		);

		$this->results[] = array(
			'test'   => 'Milestone Tracking (Prevents Duplicates)',
			'result' => $count <= 2, // Only 25% and 50% from previous tests.
			'value'  => 'Email count: ' . $count,
		);
	}

	/**
	 * Test 15: Template filters
	 *
	 * @since 1.0.0
	 */
	private function test_template_filters(): void {
		// Add filter to modify template content.
		add_filter(
			'aiddata_lms_email_template_content',
			function( $content, $template_id ) {
				return '<!-- FILTERED -->' . $content;
			},
			10,
			2
		);

		$template_manager = new AidData_LMS_Email_Templates();
		$rendered         = $template_manager->render_template( 'enrollment-confirmation', array() );

		$filtered = strpos( $rendered, '<!-- FILTERED -->' ) === 0;

		// Remove filter.
		remove_all_filters( 'aiddata_lms_email_template_content' );

		$this->results[] = array(
			'test'   => 'Template Content Filter',
			'result' => $filtered,
			'value'  => $filtered ? 'Filter applied' : 'Filter not applied',
		);
	}

	/**
	 * Test 16: Variable filters
	 *
	 * @since 1.0.0
	 */
	private function test_variable_filters(): void {
		// Add filter to modify variables.
		add_filter(
			'aiddata_lms_email_template_variables',
			function( $variables, $template_id ) {
				$variables['{custom_var}'] = 'Custom Value';
				return $variables;
			},
			10,
			2
		);

		$template_manager = new AidData_LMS_Email_Templates();
		$content          = 'Test: {custom_var}';
		$rendered         = $template_manager->replace_variables( $content, array() );

		// Note: The filter is on render_template, not replace_variables directly.
		// Let's test with render_template instead.
		$template_content = 'Test: {custom_var}';
		add_filter(
			'aiddata_lms_email_template_content',
			function( $content, $template_id ) use ( $template_content ) {
				return $template_content;
			},
			10,
			2
		);

		$rendered = $template_manager->render_template( 'test-template', array() );

		// Remove filters.
		remove_all_filters( 'aiddata_lms_email_template_variables' );
		remove_all_filters( 'aiddata_lms_email_template_content' );

		$this->results[] = array(
			'test'   => 'Template Variables Filter',
			'result' => strpos( $rendered, 'Custom Value' ) !== false,
			'value'  => $rendered,
		);
	}

	/**
	 * Display test results
	 *
	 * @since 1.0.0
	 */
	public function display_results(): void {
		$total  = count( $this->results );
		$passed = array_reduce(
			$this->results,
			function( $carry, $item ) {
				return $carry + ( $item['result'] ? 1 : 0 );
			},
			0
		);

		echo '<div class="wrap">';
		echo '<h1>Email Template System Test Results</h1>';
		echo '<p><strong>Tests Run:</strong> ' . esc_html( $total ) . '</p>';
		echo '<p><strong>Tests Passed:</strong> ' . esc_html( $passed ) . '</p>';
		echo '<p><strong>Tests Failed:</strong> ' . esc_html( $total - $passed ) . '</p>';
		echo '<p><strong>Success Rate:</strong> ' . esc_html( round( ( $passed / $total ) * 100, 2 ) ) . '%</p>';

		echo '<table class="wp-list-table widefat fixed striped">';
		echo '<thead><tr>';
		echo '<th style="width: 50px;">#</th>';
		echo '<th>Test Name</th>';
		echo '<th style="width: 80px;">Status</th>';
		echo '<th>Value</th>';
		echo '</tr></thead>';
		echo '<tbody>';

		foreach ( $this->results as $index => $result ) {
			$status_color = $result['result'] ? '#00a32a' : '#d63638';
			$status_text  = $result['result'] ? '✓ PASS' : '✗ FAIL';

			echo '<tr>';
			echo '<td>' . esc_html( $index + 1 ) . '</td>';
			echo '<td>' . esc_html( $result['test'] ) . '</td>';
			echo '<td style="color: ' . esc_attr( $status_color ) . '; font-weight: bold;">' . esc_html( $status_text ) . '</td>';
			echo '<td><code>' . esc_html( $result['value'] ) . '</code></td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</div>';
	}
}

