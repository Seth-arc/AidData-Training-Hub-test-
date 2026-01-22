<?php
/**
 * AJAX Handlers Test Runner
 *
 * Provides an admin interface to run AJAX handler tests.
 *
 * @package AidData_LMS
 * @subpackage Tutorials/Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check user capabilities.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to access this page.', 'aiddata-lms' ) );
}

// Load required classes.
require_once AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-database.php';
require_once AIDDATA_LMS_PATH . 'includes/tutorials/class-aiddata-lms-tutorial-enrollment.php';
require_once AIDDATA_LMS_PATH . 'includes/tutorials/class-aiddata-lms-tutorial-progress.php';
require_once AIDDATA_LMS_PATH . 'includes/tutorials/class-aiddata-lms-tutorial-ajax.php';
require_once AIDDATA_LMS_PATH . 'includes/tutorials/class-aiddata-lms-tutorial-ajax-test.php';

// Run tests if requested.
if ( isset( $_GET['run_tests'] ) && $_GET['run_tests'] === '1' ) {
	// Verify nonce.
	check_admin_referer( 'aiddata_lms_run_ajax_tests' );

	// Create test instance.
	$test_suite = new AidData_LMS_Tutorial_AJAX_Test();

	// Run tests.
	$test_suite->run_all_tests();

	// Display results.
	$test_suite->display_results();

	// Add back button.
	echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=aiddata-lms-ajax-tests' ) ) . '" class="button">' . esc_html__( 'Back to Test Runner', 'aiddata-lms' ) . '</a></p>';
} else {
	// Display test runner page.
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'AJAX Handlers Test Suite', 'aiddata-lms' ); ?></h1>

		<div class="card">
			<h2><?php esc_html_e( 'About These Tests', 'aiddata-lms' ); ?></h2>
			<p><?php esc_html_e( 'This test suite validates the AJAX handlers for enrollment and progress operations.', 'aiddata-lms' ); ?></p>

			<h3><?php esc_html_e( 'Test Coverage:', 'aiddata-lms' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'AJAX class instantiation', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Enrollment via AJAX', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Unenrollment via AJAX', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Enrollment status checking', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Progress update via AJAX', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Time tracking via AJAX', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Nonce verification', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'User authentication', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Input validation', 'aiddata-lms' ); ?></li>
				<li><?php esc_html_e( 'Error handling', 'aiddata-lms' ); ?></li>
			</ul>

			<p><strong><?php esc_html_e( 'Note:', 'aiddata-lms' ); ?></strong> <?php esc_html_e( 'Tests create temporary data that is automatically cleaned up.', 'aiddata-lms' ); ?></p>
		</div>

		<p>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=aiddata-lms-ajax-tests&run_tests=1' ), 'aiddata_lms_run_ajax_tests' ) ); ?>" class="button button-primary button-large">
				<?php esc_html_e( 'Run AJAX Tests', 'aiddata-lms' ); ?>
			</a>
		</p>
	</div>
	<?php
}

