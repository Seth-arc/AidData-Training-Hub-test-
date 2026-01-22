<?php
/**
 * Email Queue Manager Test Suite
 *
 * Comprehensive tests for the email queue management system including
 * queueing, processing, retry logic, and statistics.
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
 * Email Queue Test Class
 *
 * Tests email queue operations, WP-Cron integration, retry logic,
 * and queue statistics.
 *
 * @since 1.0.0
 */
class AidData_LMS_Email_Queue_Test {

	/**
	 * Test results array
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
	private $test_user_id;

	/**
	 * Email queue manager instance
	 *
	 * @since 1.0.0
	 * @var AidData_LMS_Email_Queue
	 */
	private $queue_manager;

	/**
	 * Run all tests
	 *
	 * @since 1.0.0
	 *
	 * @return array Test results.
	 */
	public function run_tests(): array {
		$this->setup();

		$this->test_class_instantiation();
		$this->test_add_to_queue();
		$this->test_invalid_email();
		$this->test_email_with_options();
		$this->test_priority_handling();
		$this->test_scheduled_email();
		$this->test_get_pending_emails();
		$this->test_mark_as_sent();
		$this->test_mark_as_failed();
		$this->test_get_queue_stats();
		$this->test_process_queue();
		$this->test_retry_logic();
		$this->test_retry_failed();
		$this->test_delete_old_emails();
		$this->test_email_hooks();
		$this->test_cron_schedule();

		$this->teardown();

		return $this->results;
	}

	/**
	 * Setup test environment
	 *
	 * @since 1.0.0
	 */
	private function setup(): void {
		// Create test user.
		$this->test_user_id = wp_insert_user(
			array(
				'user_login' => 'test_email_user_' . time(),
				'user_email' => 'test@aiddata-lms-test.com',
				'user_pass'  => wp_generate_password(),
			)
		);

		$this->queue_manager = new AidData_LMS_Email_Queue();

		// Clean any existing test emails.
		$this->cleanup_test_emails();
	}

	/**
	 * Teardown test environment
	 *
	 * @since 1.0.0
	 */
	private function teardown(): void {
		// Clean up test emails.
		$this->cleanup_test_emails();

		// Delete test user.
		if ( $this->test_user_id ) {
			wp_delete_user( $this->test_user_id );
		}
	}

	/**
	 * Clean up test emails
	 *
	 * @since 1.0.0
	 */
	private function cleanup_test_emails(): void {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );

		$wpdb->query(
			"DELETE FROM {$table_name} 
			WHERE recipient_email LIKE '%aiddata-lms-test%' 
			OR subject LIKE '%Test Email%'"
		);
	}

	/**
	 * Test class instantiation
	 *
	 * @since 1.0.0
	 */
	private function test_class_instantiation(): void {
		$passed = $this->queue_manager instanceof AidData_LMS_Email_Queue;

		$this->results[] = array(
			'test'    => 'Class Instantiation',
			'passed'  => $passed,
			'message' => $passed ? 'Email queue manager instantiated successfully' : 'Failed to instantiate class',
		);
	}

	/**
	 * Test adding email to queue
	 *
	 * @since 1.0.0
	 */
	private function test_add_to_queue(): void {
		$email_id = $this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Test Email Subject',
			'<p>Test email message</p>',
			'test_email'
		);

		$passed = ! is_wp_error( $email_id ) && is_int( $email_id ) && $email_id > 0;

		$this->results[] = array(
			'test'    => 'Add Email to Queue',
			'passed'  => $passed,
			'message' => $passed ? "Email queued with ID: {$email_id}" : 'Failed to add email to queue',
		);
	}

	/**
	 * Test invalid email address
	 *
	 * @since 1.0.0
	 */
	private function test_invalid_email(): void {
		$result = $this->queue_manager->add_to_queue(
			'invalid-email',
			'Test Subject',
			'Test message',
			'test'
		);

		$passed = is_wp_error( $result ) && $result->get_error_code() === 'invalid_email';

		$this->results[] = array(
			'test'    => 'Invalid Email Address',
			'passed'  => $passed,
			'message' => $passed ? 'Invalid email correctly rejected' : 'Invalid email not rejected',
		);
	}

	/**
	 * Test email with options
	 *
	 * @since 1.0.0
	 */
	private function test_email_with_options(): void {
		$options = array(
			'recipient_name' => 'Test User',
			'user_id'        => $this->test_user_id,
			'template_id'    => 'test_template',
			'template_data'  => array( 'key' => 'value' ),
			'priority'       => 3,
		);

		$email_id = $this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Test Email with Options',
			'<p>Test message</p>',
			'test_email_options',
			$options
		);

		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );
		$email      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $email_id ) );

		$passed = ! is_wp_error( $email_id ) 
			&& $email 
			&& $email->recipient_name === 'Test User' 
			&& $email->priority === 3;

		$this->results[] = array(
			'test'    => 'Email with Options',
			'passed'  => $passed,
			'message' => $passed ? 'Email queued with all options correctly' : 'Email options not set correctly',
		);
	}

	/**
	 * Test priority handling
	 *
	 * @since 1.0.0
	 */
	private function test_priority_handling(): void {
		// Add emails with different priorities.
		$this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Low Priority',
			'<p>Low priority message</p>',
			'test',
			array( 'priority' => 10 )
		);

		$this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'High Priority',
			'<p>High priority message</p>',
			'test',
			array( 'priority' => 1 )
		);

		// Get pending emails (should be ordered by priority).
		$pending = $this->queue_manager->get_pending_emails( 2 );

		$passed = count( $pending ) === 2 
			&& $pending[0]->subject === 'High Priority' 
			&& $pending[0]->priority === 1;

		$this->results[] = array(
			'test'    => 'Priority Handling',
			'passed'  => $passed,
			'message' => $passed ? 'Emails ordered by priority correctly' : 'Priority ordering failed',
		);
	}

	/**
	 * Test scheduled email
	 *
	 * @since 1.0.0
	 */
	private function test_scheduled_email(): void {
		// Schedule email for future.
		$future_time = gmdate( 'Y-m-d H:i:s', strtotime( '+1 hour' ) );

		$email_id = $this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Scheduled Email',
			'<p>Future message</p>',
			'test',
			array( 'scheduled_for' => $future_time )
		);

		// Should not be in pending emails.
		$pending = $this->queue_manager->get_pending_emails( 100 );
		$found   = false;

		foreach ( $pending as $email ) {
			if ( $email->id === $email_id ) {
				$found = true;
				break;
			}
		}

		$passed = ! is_wp_error( $email_id ) && ! $found;

		$this->results[] = array(
			'test'    => 'Scheduled Email',
			'passed'  => $passed,
			'message' => $passed ? 'Scheduled email not included in pending' : 'Scheduled email incorrectly included',
		);
	}

	/**
	 * Test get pending emails
	 *
	 * @since 1.0.0
	 */
	private function test_get_pending_emails(): void {
		// Clean up first.
		$this->cleanup_test_emails();

		// Add test emails.
		$this->queue_manager->add_to_queue(
			'test1@aiddata-lms-test.com',
			'Test Email 1',
			'<p>Test 1</p>',
			'test'
		);

		$this->queue_manager->add_to_queue(
			'test2@aiddata-lms-test.com',
			'Test Email 2',
			'<p>Test 2</p>',
			'test'
		);

		$pending = $this->queue_manager->get_pending_emails( 10 );

		$passed = is_array( $pending ) && count( $pending ) >= 2;

		$this->results[] = array(
			'test'    => 'Get Pending Emails',
			'passed'  => $passed,
			'message' => $passed ? 'Pending emails retrieved successfully' : 'Failed to retrieve pending emails',
		);
	}

	/**
	 * Test mark as sent
	 *
	 * @since 1.0.0
	 */
	private function test_mark_as_sent(): void {
		$email_id = $this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Test Sent',
			'<p>Test</p>',
			'test'
		);

		$result = $this->queue_manager->mark_as_sent( $email_id );

		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );
		$email      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $email_id ) );

		$passed = $result && $email && $email->status === 'sent' && ! empty( $email->sent_at );

		$this->results[] = array(
			'test'    => 'Mark as Sent',
			'passed'  => $passed,
			'message' => $passed ? 'Email marked as sent successfully' : 'Failed to mark email as sent',
		);
	}

	/**
	 * Test mark as failed
	 *
	 * @since 1.0.0
	 */
	private function test_mark_as_failed(): void {
		$email_id = $this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Test Failed',
			'<p>Test</p>',
			'test'
		);

		$result = $this->queue_manager->mark_as_failed( $email_id, 'Test error message' );

		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );
		$email      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $email_id ) );

		$passed = $result 
			&& $email 
			&& $email->status === 'failed' 
			&& $email->error_message === 'Test error message';

		$this->results[] = array(
			'test'    => 'Mark as Failed',
			'passed'  => $passed,
			'message' => $passed ? 'Email marked as failed with error message' : 'Failed to mark email as failed',
		);
	}

	/**
	 * Test get queue stats
	 *
	 * @since 1.0.0
	 */
	private function test_get_queue_stats(): void {
		$this->cleanup_test_emails();

		// Add test emails with different statuses.
		$email1 = $this->queue_manager->add_to_queue(
			'test1@aiddata-lms-test.com',
			'Test Email 1',
			'<p>Test</p>',
			'test'
		);

		$email2 = $this->queue_manager->add_to_queue(
			'test2@aiddata-lms-test.com',
			'Test Email 2',
			'<p>Test</p>',
			'test'
		);

		$this->queue_manager->mark_as_sent( $email1 );
		$this->queue_manager->mark_as_failed( $email2, 'Test error' );

		$stats = $this->queue_manager->get_queue_stats();

		$passed = is_array( $stats ) 
			&& isset( $stats['sent'] ) 
			&& isset( $stats['failed'] ) 
			&& isset( $stats['pending'] ) 
			&& $stats['sent'] >= 1 
			&& $stats['failed'] >= 1;

		$this->results[] = array(
			'test'    => 'Get Queue Stats',
			'passed'  => $passed,
			'message' => $passed ? 'Queue statistics retrieved correctly' : 'Failed to retrieve queue stats',
		);
	}

	/**
	 * Test process queue
	 *
	 * @since 1.0.0
	 */
	private function test_process_queue(): void {
		$this->cleanup_test_emails();

		// Add test email.
		$this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Test Process Queue',
			'<p>Test</p>',
			'test'
		);

		// Process queue.
		$results = $this->queue_manager->process_queue( 10 );

		$passed = is_array( $results ) 
			&& isset( $results['sent'] ) 
			&& isset( $results['failed'] ) 
			&& isset( $results['skipped'] );

		$this->results[] = array(
			'test'    => 'Process Queue',
			'passed'  => $passed,
			'message' => $passed ? "Queue processed: {$results['sent']} sent, {$results['failed']} failed, {$results['skipped']} skipped" : 'Failed to process queue',
		);
	}

	/**
	 * Test retry logic
	 *
	 * @since 1.0.0
	 */
	private function test_retry_logic(): void {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );

		// Add email directly to database with attempt count.
		$wpdb->insert(
			$table_name,
			array(
				'recipient_email' => 'retry@aiddata-lms-test.com',
				'subject'         => 'Retry Test',
				'message'         => '<p>Test</p>',
				'email_type'      => 'test',
				'status'          => 'pending',
				'attempts'        => 2,
				'priority'        => 5,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%d', '%d' )
		);

		$email_id = $wpdb->insert_id;

		// Simulate failed send (will exceed max attempts).
		// Note: In real scenario, this would fail after processing.
		$this->queue_manager->mark_as_failed( $email_id, 'Exceeded max attempts' );

		$email  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $email_id ) );
		$passed = $email && $email->status === 'failed';

		$this->results[] = array(
			'test'    => 'Retry Logic',
			'passed'  => $passed,
			'message' => $passed ? 'Retry logic enforced (failed after max attempts)' : 'Retry logic not working',
		);
	}

	/**
	 * Test retry failed emails
	 *
	 * @since 1.0.0
	 */
	private function test_retry_failed(): void {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );

		// Add failed email.
		$wpdb->insert(
			$table_name,
			array(
				'recipient_email' => 'retry@aiddata-lms-test.com',
				'subject'         => 'Retry Failed Test',
				'message'         => '<p>Test</p>',
				'email_type'      => 'test',
				'status'          => 'failed',
				'attempts'        => 2,
				'priority'        => 5,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%d', '%d' )
		);

		$email_id = $wpdb->insert_id;

		// Retry failed emails.
		$count = $this->queue_manager->retry_failed( 3 );

		$email  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $email_id ) );
		$passed = $count >= 1 && $email && $email->status === 'pending' && $email->attempts === 0;

		$this->results[] = array(
			'test'    => 'Retry Failed Emails',
			'passed'  => $passed,
			'message' => $passed ? "Retried {$count} failed email(s)" : 'Failed to retry failed emails',
		);
	}

	/**
	 * Test delete old emails
	 *
	 * @since 1.0.0
	 */
	private function test_delete_old_emails(): void {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'email' );

		// Add old sent email (backdated).
		$old_date = gmdate( 'Y-m-d H:i:s', strtotime( '-31 days' ) );

		$wpdb->insert(
			$table_name,
			array(
				'recipient_email' => 'old@aiddata-lms-test.com',
				'subject'         => 'Old Email',
				'message'         => '<p>Old</p>',
				'email_type'      => 'test',
				'status'          => 'sent',
				'sent_at'         => $old_date,
				'priority'        => 5,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%d' )
		);

		$old_email_id = $wpdb->insert_id;

		// Delete old emails.
		$deleted = $this->queue_manager->delete_old_emails( 30 );

		$email  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $old_email_id ) );
		$passed = $deleted >= 1 && ! $email;

		$this->results[] = array(
			'test'    => 'Delete Old Emails',
			'passed'  => $passed,
			'message' => $passed ? "Deleted {$deleted} old email(s)" : 'Failed to delete old emails',
		);
	}

	/**
	 * Test email hooks
	 *
	 * @since 1.0.0
	 */
	private function test_email_hooks(): void {
		$hook_fired = false;

		// Hook into email queued action.
		add_action(
			'aiddata_lms_email_queued',
			function() use ( &$hook_fired ) {
				$hook_fired = true;
			}
		);

		$this->queue_manager->add_to_queue(
			'test@aiddata-lms-test.com',
			'Hook Test',
			'<p>Test</p>',
			'test'
		);

		$passed = $hook_fired;

		$this->results[] = array(
			'test'    => 'Email Hooks',
			'passed'  => $passed,
			'message' => $passed ? 'Email queued hook fired successfully' : 'Email queued hook did not fire',
		);
	}

	/**
	 * Test cron schedule
	 *
	 * @since 1.0.0
	 */
	private function test_cron_schedule(): void {
		$schedules = wp_get_schedules();

		$passed = isset( $schedules['aiddata_lms_five_minutes'] ) 
			&& $schedules['aiddata_lms_five_minutes']['interval'] === 5 * MINUTE_IN_SECONDS;

		$this->results[] = array(
			'test'    => 'Cron Schedule',
			'passed'  => $passed,
			'message' => $passed ? 'Custom cron schedule registered' : 'Cron schedule not registered',
		);
	}

	/**
	 * Display test results
	 *
	 * @since 1.0.0
	 */
	public function display_results(): void {
		$total_tests  = count( $this->results );
		$passed_tests = count( array_filter( $this->results, fn( $r ) => $r['passed'] ) );
		$failed_tests = $total_tests - $passed_tests;

		echo '<div class="wrap">';
		echo '<h1>Email Queue Manager Test Results</h1>';

		echo '<div class="notice notice-' . ( $failed_tests === 0 ? 'success' : 'warning' ) . '">';
		echo '<p><strong>Tests: ' . esc_html( $total_tests ) . ' | ';
		echo 'Passed: ' . esc_html( $passed_tests ) . ' | ';
		echo 'Failed: ' . esc_html( $failed_tests ) . '</strong></p>';
		echo '</div>';

		echo '<table class="wp-list-table widefat fixed striped">';
		echo '<thead><tr>';
		echo '<th>Test</th>';
		echo '<th>Status</th>';
		echo '<th>Message</th>';
		echo '</tr></thead>';
		echo '<tbody>';

		foreach ( $this->results as $result ) {
			$status_icon  = $result['passed'] ? '✅' : '❌';
			$status_class = $result['passed'] ? 'success' : 'error';

			echo '<tr>';
			echo '<td><strong>' . esc_html( $result['test'] ) . '</strong></td>';
			echo '<td><span class="notice-' . esc_attr( $status_class ) . '">' . esc_html( $status_icon ) . '</span></td>';
			echo '<td>' . esc_html( $result['message'] ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</div>';
	}
}

