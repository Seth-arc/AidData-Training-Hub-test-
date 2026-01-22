<?php
/**
 * Analytics Test Runner
 *
 * Admin interface for running analytics tests.
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if user has permission to run tests.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to access this page.', 'aiddata-lms' ) );
}

// Load required files.
require_once plugin_dir_path( __FILE__ ) . 'class-aiddata-lms-analytics.php';
require_once plugin_dir_path( __FILE__ ) . 'class-aiddata-lms-analytics-test.php';

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Analytics System Tests', 'aiddata-lms' ); ?></h1>

	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Test Suite:', 'aiddata-lms' ); ?></strong>
			<?php esc_html_e( 'Analytics Tracking System', 'aiddata-lms' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Tests:', 'aiddata-lms' ); ?></strong>
			<?php esc_html_e( '20 comprehensive test scenarios', 'aiddata-lms' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Coverage:', 'aiddata-lms' ); ?></strong>
			<?php esc_html_e( 'Event tracking, session management, analytics queries, privacy compliance, hook integration', 'aiddata-lms' ); ?>
		</p>
	</div>

	<?php if ( isset( $_GET['run_tests'] ) && 'true' === $_GET['run_tests'] ) : ?>
		<?php
		// Run tests.
		$test_suite = new AidData_LMS_Analytics_Test();
		$test_suite->run_tests();
		$test_suite->display_results();
		?>

		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=aiddata-lms-analytics-tests' ) ); ?>" class="button">
				<?php esc_html_e( 'Back', 'aiddata-lms' ); ?>
			</a>
		</p>
	<?php else : ?>
		<p><?php esc_html_e( 'Click the button below to run the analytics test suite.', 'aiddata-lms' ); ?></p>

		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=aiddata-lms-analytics-tests&run_tests=true' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Run Analytics Tests', 'aiddata-lms' ); ?>
			</a>
		</p>

		<h2><?php esc_html_e( 'Test Scenarios', 'aiddata-lms' ); ?></h2>
		<ol>
			<li><?php esc_html_e( 'Class instantiation', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Table name initialization', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Track event - Success', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Track event - Invalid tutorial', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Track event with user ID', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Track event with event data', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Track event - Guest user', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Get tutorial analytics', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Get user analytics', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Get platform analytics', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Get event count', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Get unique users', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Tutorial analytics with date range', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Session ID generation', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'IP address hashing', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Enrollment tracking hook', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Step completion tracking hook', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Tutorial view tracking', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Delete old records', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Event data JSON storage', 'aiddata-lms' ); ?></li>
		</ol>
	<?php endif; ?>
</div>

