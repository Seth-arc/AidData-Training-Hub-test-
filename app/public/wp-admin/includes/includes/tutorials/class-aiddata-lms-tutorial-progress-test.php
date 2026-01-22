<?php
/**
 * Tutorial Progress Manager Test Suite
 *
 * Comprehensive tests for the AidData_LMS_Tutorial_Progress class.
 *
 * @package AidData_LMS
 * @subpackage Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Tutorial_Progress_Test
 *
 * Test suite for progress management functionality.
 *
 * @since 1.0.0
 */
class AidData_LMS_Tutorial_Progress_Test {

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
	 * Test enrollment ID.
	 *
	 * @var int
	 */
	private $test_enrollment_id;

	/**
	 * Progress manager instance.
	 *
	 * @var AidData_LMS_Tutorial_Progress
	 */
	private $progress_manager;

	/**
	 * Enrollment manager instance.
	 *
	 * @var AidData_LMS_Tutorial_Enrollment
	 */
	private $enrollment_manager;

	/**
	 * Run all tests.
	 *
	 * @return array Test results.
	 */
	public function run_all_tests(): array {
		$this->results = array();

		// Setup test data.
		$this->setup_test_data();

		// Run tests.
		$this->test_class_instantiation();
		$this->test_table_name_initialization();
		$this->test_initialize_progress();
		$this->test_get_progress();
		$this->test_update_progress_single_step();
		$this->test_update_progress_multiple_steps();
		$this->test_get_completed_steps();
		$this->test_is_step_completed();
		$this->test_get_last_step();
		$this->test_calculate_progress_percent();
		$this->test_mark_tutorial_complete();
		$this->test_update_time_spent();
		$this->test_format_time_spent();
		$this->test_get_tutorial_progress_stats();
		$this->test_get_user_all_progress();
		$this->test_reset_progress();
		$this->test_progress_without_enrollment();
		$this->test_invalid_step_index();
		$this->test_progress_hooks_fire();
		$this->test_automatic_completion();

		// Cleanup test data.
		$this->cleanup_test_data();

		return $this->results;
	}

	/**
	 * Setup test data.
	 *
	 * @return void
	 */
	private function setup_test_data(): void {
		// Create test user.
		$this->test_user_id = wp_create_user(
			'progress_test_user_' . time(),
			wp_generate_password(),
			'progresstest' . time() . '@example.com'
		);

		// Create test tutorial.
		$this->test_tutorial_id = wp_insert_post(
			array(
				'post_title'   => 'Test Progress Tutorial ' . time(),
				'post_type'    => 'aiddata_tutorial',
				'post_status'  => 'publish',
				'post_content' => 'Test tutorial for progress tracking.',
			)
		);

		// Add tutorial steps metadata (5 steps).
		$test_steps = array(
			array( 'title' => 'Step 1', 'content' => 'First step' ),
			array( 'title' => 'Step 2', 'content' => 'Second step' ),
			array( 'title' => 'Step 3', 'content' => 'Third step' ),
			array( 'title' => 'Step 4', 'content' => 'Fourth step' ),
			array( 'title' => 'Step 5', 'content' => 'Fifth step' ),
		);
		update_post_meta( $this->test_tutorial_id, '_tutorial_steps', $test_steps );

		// Initialize managers.
		$this->progress_manager   = new AidData_LMS_Tutorial_Progress();
		$this->enrollment_manager = new AidData_LMS_Tutorial_Enrollment();

		// Enroll user (this should auto-initialize progress).
		$enrollment_result = $this->enrollment_manager->enroll_user(
			$this->test_user_id,
			$this->test_tutorial_id,
			'test'
		);

		if ( ! is_wp_error( $enrollment_result ) ) {
			$enrollment = $this->enrollment_manager->get_enrollment(
				$this->test_user_id,
				$this->test_tutorial_id
			);
			$this->test_enrollment_id = $enrollment ? $enrollment->id : 0;
		}
	}

	/**
	 * Cleanup test data.
	 *
	 * @return void
	 */
	private function cleanup_test_data(): void {
		global $wpdb;

		// Delete progress records.
		$progress_table = AidData_LMS_Database::get_table_name( 'progress' );
		$wpdb->delete( $progress_table, array( 'user_id' => $this->test_user_id ), array( '%d' ) );

		// Delete enrollment records.
		$enrollment_table = AidData_LMS_Database::get_table_name( 'enrollments' );
		$wpdb->delete( $enrollment_table, array( 'user_id' => $this->test_user_id ), array( '%d' ) );

		// Delete test post.
		if ( $this->test_tutorial_id ) {
			wp_delete_post( $this->test_tutorial_id, true );
		}

		// Delete test user.
		if ( $this->test_user_id ) {
			wp_delete_user( $this->test_user_id );
		}
	}

	/**
	 * Add test result.
	 *
	 * @param string $test_name Test name.
	 * @param bool   $passed    Whether test passed.
	 * @param string $message   Test message.
	 *
	 * @return void
	 */
	private function add_result( string $test_name, bool $passed, string $message = '' ): void {
		$this->results[] = array(
			'test'    => $test_name,
			'passed'  => $passed,
			'message' => $message,
		);
	}

	/**
	 * Test 1: Class instantiation.
	 *
	 * @return void
	 */
	private function test_class_instantiation(): void {
		$manager = new AidData_LMS_Tutorial_Progress();
		$this->add_result(
			'Class Instantiation',
			is_object( $manager ) && $manager instanceof AidData_LMS_Tutorial_Progress,
			'Progress manager class instantiates correctly'
		);
	}

	/**
	 * Test 2: Table name initialization.
	 *
	 * @return void
	 */
	private function test_table_name_initialization(): void {
		$expected = AidData_LMS_Database::get_table_name( 'progress' );
		$this->add_result(
			'Table Name Initialization',
			$this->progress_manager->table_name === $expected,
			'Table name: ' . $this->progress_manager->table_name
		);
	}

	/**
	 * Test 3: Initialize progress.
	 *
	 * @return void
	 */
	private function test_initialize_progress(): void {
		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = ! is_null( $progress ) &&
			0 === (int) $progress->current_step &&
			'not_started' === $progress->status &&
			0.00 === (float) $progress->progress_percent;

		$this->add_result(
			'Initialize Progress',
			$passed,
			$passed ? 'Progress initialized automatically on enrollment' : 'Failed to initialize progress'
		);
	}

	/**
	 * Test 4: Get progress.
	 *
	 * @return void
	 */
	private function test_get_progress(): void {
		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = is_object( $progress ) &&
			property_exists( $progress, 'user_id' ) &&
			property_exists( $progress, 'tutorial_id' ) &&
			property_exists( $progress, 'progress_percent' );

		$this->add_result(
			'Get Progress',
			$passed,
			$passed ? 'Progress retrieved successfully' : 'Failed to get progress'
		);
	}

	/**
	 * Test 5: Update progress - single step.
	 *
	 * @return void
	 */
	private function test_update_progress_single_step(): void {
		$result = $this->progress_manager->update_progress(
			$this->test_user_id,
			$this->test_tutorial_id,
			0 // First step
		);

		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = true === $result &&
			! is_null( $progress ) &&
			'in_progress' === $progress->status &&
			20.00 === (float) $progress->progress_percent; // 1 of 5 steps = 20%

		$this->add_result(
			'Update Progress - Single Step',
			$passed,
			$passed ? "Step 0 completed, 20% progress" : 'Failed to update single step'
		);
	}

	/**
	 * Test 6: Update progress - multiple steps.
	 *
	 * @return void
	 */
	private function test_update_progress_multiple_steps(): void {
		// Complete steps 1 and 2.
		$this->progress_manager->update_progress( $this->test_user_id, $this->test_tutorial_id, 1 );
		$result = $this->progress_manager->update_progress( $this->test_user_id, $this->test_tutorial_id, 2 );

		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = true === $result &&
			! is_null( $progress ) &&
			60.00 === (float) $progress->progress_percent; // 3 of 5 steps = 60%

		$this->add_result(
			'Update Progress - Multiple Steps',
			$passed,
			$passed ? "Steps 0,1,2 completed, 60% progress" : 'Failed to update multiple steps'
		);
	}

	/**
	 * Test 7: Get completed steps.
	 *
	 * @return void
	 */
	private function test_get_completed_steps(): void {
		$completed = $this->progress_manager->get_completed_steps(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = is_array( $completed ) &&
			count( $completed ) === 3 &&
			in_array( 0, $completed, true ) &&
			in_array( 1, $completed, true ) &&
			in_array( 2, $completed, true );

		$this->add_result(
			'Get Completed Steps',
			$passed,
			$passed ? 'Completed steps: ' . implode( ',', $completed ) : 'Failed to get completed steps'
		);
	}

	/**
	 * Test 8: Is step completed.
	 *
	 * @return void
	 */
	private function test_is_step_completed(): void {
		$is_completed = $this->progress_manager->is_step_completed(
			$this->test_user_id,
			$this->test_tutorial_id,
			1
		);

		$is_not_completed = $this->progress_manager->is_step_completed(
			$this->test_user_id,
			$this->test_tutorial_id,
			4
		);

		$passed = true === $is_completed && false === $is_not_completed;

		$this->add_result(
			'Is Step Completed',
			$passed,
			$passed ? 'Step completion check works correctly' : 'Step completion check failed'
		);
	}

	/**
	 * Test 9: Get last step.
	 *
	 * @return void
	 */
	private function test_get_last_step(): void {
		$last_step = $this->progress_manager->get_last_step(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = 2 === $last_step; // Last step we updated was step 2

		$this->add_result(
			'Get Last Step',
			$passed,
			$passed ? "Last step: $last_step" : 'Failed to get last step'
		);
	}

	/**
	 * Test 10: Calculate progress percent.
	 *
	 * @return void
	 */
	private function test_calculate_progress_percent(): void {
		$percent = $this->progress_manager->calculate_progress_percent(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = 60.00 === $percent;

		$this->add_result(
			'Calculate Progress Percent',
			$passed,
			$passed ? "Progress: $percent%" : 'Failed to calculate progress'
		);
	}

	/**
	 * Test 11: Mark tutorial complete.
	 *
	 * @return void
	 */
	private function test_mark_tutorial_complete(): void {
		// Complete remaining steps first.
		$this->progress_manager->update_progress( $this->test_user_id, $this->test_tutorial_id, 3 );
		$this->progress_manager->update_progress( $this->test_user_id, $this->test_tutorial_id, 4 );

		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = ! is_null( $progress ) &&
			'completed' === $progress->status &&
			! is_null( $progress->completed_at ) &&
			100.00 === (float) $progress->progress_percent;

		$this->add_result(
			'Mark Tutorial Complete',
			$passed,
			$passed ? 'Tutorial marked complete automatically at 100%' : 'Failed to mark complete'
		);
	}

	/**
	 * Test 12: Update time spent.
	 *
	 * @return void
	 */
	private function test_update_time_spent(): void {
		$result1 = $this->progress_manager->update_time_spent(
			$this->test_user_id,
			$this->test_tutorial_id,
			120 // 2 minutes
		);

		$result2 = $this->progress_manager->update_time_spent(
			$this->test_user_id,
			$this->test_tutorial_id,
			180 // 3 minutes
		);

		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = true === $result1 &&
			true === $result2 &&
			! is_null( $progress ) &&
			300 === (int) $progress->time_spent; // 120 + 180 = 300 seconds

		$this->add_result(
			'Update Time Spent',
			$passed,
			$passed ? 'Time accumulated: 300 seconds (5 minutes)' : 'Failed to update time'
		);
	}

	/**
	 * Test 13: Format time spent.
	 *
	 * @return void
	 */
	private function test_format_time_spent(): void {
		$formatted1 = $this->progress_manager->format_time_spent( 300 );  // 5 minutes
		$formatted2 = $this->progress_manager->format_time_spent( 3660 ); // 1 hour, 1 minute

		$passed = false !== strpos( $formatted1, '5' ) &&
			false !== strpos( $formatted1, 'minute' ) &&
			false !== strpos( $formatted2, 'hour' );

		$this->add_result(
			'Format Time Spent',
			$passed,
			$passed ? "Formats: '$formatted1' and '$formatted2'" : 'Failed to format time'
		);
	}

	/**
	 * Test 14: Get tutorial progress stats.
	 *
	 * @return void
	 */
	private function test_get_tutorial_progress_stats(): void {
		$stats = $this->progress_manager->get_tutorial_progress_stats( $this->test_tutorial_id );

		$passed = is_array( $stats ) &&
			isset( $stats['total_learners'] ) &&
			isset( $stats['completed'] ) &&
			isset( $stats['avg_progress'] ) &&
			$stats['total_learners'] >= 1;

		$this->add_result(
			'Get Tutorial Progress Stats',
			$passed,
			$passed ? sprintf(
				'Stats: %d learners, %d completed, %.2f%% avg progress',
				$stats['total_learners'],
				$stats['completed'],
				$stats['avg_progress']
			) : 'Failed to get stats'
		);
	}

	/**
	 * Test 15: Get user all progress.
	 *
	 * @return void
	 */
	private function test_get_user_all_progress(): void {
		$all_progress = $this->progress_manager->get_user_all_progress( $this->test_user_id );

		$passed = is_array( $all_progress ) && count( $all_progress ) >= 1;

		$this->add_result(
			'Get User All Progress',
			$passed,
			$passed ? 'Found ' . count( $all_progress ) . ' progress record(s)' : 'Failed to get all progress'
		);
	}

	/**
	 * Test 16: Reset progress.
	 *
	 * @return void
	 */
	private function test_reset_progress(): void {
		$result = $this->progress_manager->reset_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = true === $result &&
			! is_null( $progress ) &&
			'not_started' === $progress->status &&
			0.00 === (float) $progress->progress_percent &&
			empty( $progress->completed_steps );

		$this->add_result(
			'Reset Progress',
			$passed,
			$passed ? 'Progress reset successfully' : 'Failed to reset progress'
		);
	}

	/**
	 * Test 17: Progress without enrollment.
	 *
	 * @return void
	 */
	private function test_progress_without_enrollment(): void {
		// Create a new user without enrollment.
		$temp_user_id = wp_create_user(
			'temp_progress_user_' . time(),
			wp_generate_password(),
			'tempprogress' . time() . '@example.com'
		);

		$result = $this->progress_manager->update_progress(
			$temp_user_id,
			$this->test_tutorial_id,
			0
		);

		$passed = is_wp_error( $result ) && 'not_enrolled' === $result->get_error_code();

		// Cleanup.
		wp_delete_user( $temp_user_id );

		$this->add_result(
			'Progress Without Enrollment',
			$passed,
			$passed ? 'Correctly prevents progress for non-enrolled users' : 'Failed to validate enrollment'
		);
	}

	/**
	 * Test 18: Invalid step index.
	 *
	 * @return void
	 */
	private function test_invalid_step_index(): void {
		// Re-enroll user for this test.
		$this->enrollment_manager->enroll_user(
			$this->test_user_id,
			$this->test_tutorial_id,
			'test'
		);

		$result = $this->progress_manager->update_progress(
			$this->test_user_id,
			$this->test_tutorial_id,
			-1 // Invalid negative step
		);

		$passed = is_wp_error( $result ) && 'invalid_step' === $result->get_error_code();

		$this->add_result(
			'Invalid Step Index',
			$passed,
			$passed ? 'Correctly rejects invalid step indices' : 'Failed to validate step index'
		);
	}

	/**
	 * Test 19: Progress hooks fire.
	 *
	 * @return void
	 */
	private function test_progress_hooks_fire(): void {
		$hooks_fired = array();

		// Add temporary hooks to track firing.
		add_action(
			'aiddata_lms_step_completed',
			function() use ( &$hooks_fired ) {
				$hooks_fired[] = 'step_completed';
			}
		);

		add_action(
			'aiddata_lms_progress_updated',
			function() use ( &$hooks_fired ) {
				$hooks_fired[] = 'progress_updated';
			}
		);

		// Update progress.
		$this->progress_manager->update_progress(
			$this->test_user_id,
			$this->test_tutorial_id,
			0
		);

		$passed = in_array( 'step_completed', $hooks_fired, true ) &&
			in_array( 'progress_updated', $hooks_fired, true );

		$this->add_result(
			'Progress Hooks Fire',
			$passed,
			$passed ? 'Hooks fired: ' . implode( ', ', $hooks_fired ) : 'Hooks did not fire correctly'
		);
	}

	/**
	 * Test 20: Automatic completion.
	 *
	 * @return void
	 */
	private function test_automatic_completion(): void {
		// Reset progress first.
		$this->progress_manager->reset_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		// Complete all 5 steps.
		for ( $i = 0; $i < 5; $i++ ) {
			$this->progress_manager->update_progress(
				$this->test_user_id,
				$this->test_tutorial_id,
				$i
			);
		}

		$progress = $this->progress_manager->get_progress(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$enrollment = $this->enrollment_manager->get_enrollment(
			$this->test_user_id,
			$this->test_tutorial_id
		);

		$passed = ! is_null( $progress ) &&
			'completed' === $progress->status &&
			! is_null( $progress->completed_at ) &&
			! is_null( $enrollment ) &&
			'completed' === $enrollment->status;

		$this->add_result(
			'Automatic Completion',
			$passed,
			$passed ? 'Tutorial auto-completed at 100% progress' : 'Automatic completion failed'
		);
	}

	/**
	 * Display test results.
	 *
	 * @return void
	 */
	public function display_results(): void {
		$total  = count( $this->results );
		$passed = count( array_filter( $this->results, fn( $r ) => $r['passed'] ) );
		$failed = $total - $passed;

		echo '<div class="wrap">';
		echo '<h1>Progress Manager Test Results</h1>';

		echo '<div class="notice notice-' . ( $failed === 0 ? 'success' : 'warning' ) . '">';
		echo '<p><strong>Summary:</strong> ' . $passed . ' of ' . $total . ' tests passed';
		if ( $failed > 0 ) {
			echo ' (' . $failed . ' failed)';
		}
		echo '</p>';
		echo '</div>';

		echo '<table class="wp-list-table widefat fixed striped">';
		echo '<thead><tr>';
		echo '<th style="width: 50px;">Status</th>';
		echo '<th style="width: 300px;">Test</th>';
		echo '<th>Result</th>';
		echo '</tr></thead>';
		echo '<tbody>';

		foreach ( $this->results as $result ) {
			$status_icon  = $result['passed'] ? '✅' : '❌';
			$status_class = $result['passed'] ? 'success' : 'failed';

			echo '<tr class="' . esc_attr( $status_class ) . '">';
			echo '<td style="text-align: center; font-size: 20px;">' . $status_icon . '</td>';
			echo '<td><strong>' . esc_html( $result['test'] ) . '</strong></td>';
			echo '<td>' . esc_html( $result['message'] ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody>';
		echo '</table>';

		echo '</div>';
	}
}

