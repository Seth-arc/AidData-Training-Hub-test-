<?php
/**
 * Email Queue Test Runner
 *
 * Admin interface for running email queue manager tests.
 *
 * @package AidData_LMS
 * @subpackage Email/Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check user permissions.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'aiddata-lms' ) );
}

// Load dependencies.
require_once AIDDATA_LMS_PATH . 'includes/email/class-aiddata-lms-email-queue.php';
require_once AIDDATA_LMS_PATH . 'includes/email/class-aiddata-lms-email-queue-test.php';

// Handle test execution.
$run_tests = isset( $_POST['run_email_queue_tests'] ) && check_admin_referer( 'aiddata_lms_email_queue_tests', 'email_queue_test_nonce' );

if ( $run_tests ) {
	$test_suite = new AidData_LMS_Email_Queue_Test();
	$results    = $test_suite->run_tests();

	// Display results.
	$test_suite->display_results();
} else {
	// Display test form.
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Email Queue Manager Tests', 'aiddata-lms' ); ?></h1>

		<div class="card">
			<h2><?php esc_html_e( 'Test Suite Information', 'aiddata-lms' ); ?></h2>
			<p><?php esc_html_e( 'This test suite validates the email queue management system including:', 'aiddata-lms' ); ?></p>
			<ul style="list-style: disc; margin-left: 20px;">
				<li><?php esc_html_e( 'Email queueing with validation', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Priority handling', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Email scheduling', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Queue processing', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Retry logic', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Status management', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Queue statistics', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Old email cleanup', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'WordPress hooks', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'WP-Cron integration', 'aiddata-lms' ); ?></li>
			</ul>

			<p><strong><?php esc_html_e( 'Total Tests:', 'aiddata-lms' ); ?></strong> 16</p>

			<form method="post">
				<?php wp_nonce_field( 'aiddata_lms_email_queue_tests', 'email_queue_test_nonce' ); ?>
				<p>
					<button type="submit" name="run_email_queue_tests" class="button button-primary">
						<?php esc_html_e( 'Run Email Queue Tests', 'aiddata-lms' ); ?>
					</button>
				</p>
			</form>
		</div>

		<div class="card">
			<h2><?php esc_html_e( 'Notes', 'aiddata-lms' ); ?></h2>
			<ul style="list-style: disc; margin-left: 20px;">
				<li><?php esc_html_e( 'Tests will create temporary email records in the database', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'All test data will be cleaned up automatically', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'No actual emails will be sent during testing', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Tests are safe to run on production sites', 'aiddata-lms' ); ?></li>
			</ul>
		</div>
	</div>
	<?php
}

