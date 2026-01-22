<?php
/**
 * Progress Manager Test Runner
 *
 * Admin interface for running progress manager tests.
 *
 * @package AidData_LMS
 * @subpackage Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check user permissions.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to access this page.', 'aiddata-lms' ) );
}

// Load test class if not already loaded.
if ( ! class_exists( 'AidData_LMS_Tutorial_Progress_Test' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'class-aiddata-lms-tutorial-progress-test.php';
}

// Load progress manager if not already loaded.
if ( ! class_exists( 'AidData_LMS_Tutorial_Progress' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'class-aiddata-lms-tutorial-progress.php';
}

// Load enrollment manager if not already loaded.
if ( ! class_exists( 'AidData_LMS_Tutorial_Enrollment' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'class-aiddata-lms-tutorial-enrollment.php';
}

// Run tests if requested.
if ( isset( $_GET['run_tests'] ) && '1' === $_GET['run_tests'] ) {
	check_admin_referer( 'run_progress_tests' );

	$test_suite = new AidData_LMS_Tutorial_Progress_Test();
	$test_suite->run_all_tests();
	$test_suite->display_results();
} else {
	// Display test runner interface.
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Progress Manager Test Suite', 'aiddata-lms' ); ?></h1>
		
		<div class="card">
			<h2><?php esc_html_e( 'Run Tests', 'aiddata-lms' ); ?></h2>
			<p><?php esc_html_e( 'This test suite validates the Progress Manager functionality including:', 'aiddata-lms' ); ?></p>
			
			<ul style="list-style: disc; margin-left: 20px;">
				<li><?php esc_html_e( 'Progress initialization', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Step completion tracking', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Progress percentage calculation', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Tutorial completion', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Time tracking', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Progress statistics', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Progress reset', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Hook integration', 'aiddata-lms' ); ?></li>
			</ul>
			
			<p>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'run_tests', '1' ), 'run_progress_tests' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Run Progress Tests', 'aiddata-lms' ); ?>
				</a>
			</p>
		</div>
	</div>
	<?php
}

