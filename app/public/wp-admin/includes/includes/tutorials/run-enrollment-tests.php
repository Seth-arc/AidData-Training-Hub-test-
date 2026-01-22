<?php
/**
 * Test Runner for Enrollment Manager
 *
 * Run this file directly to execute enrollment manager tests.
 *
 * Usage: Navigate to /wp-admin/ and add this to URL:
 * ?page=run-enrollment-tests
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
 * Run Enrollment Manager Tests
 *
 * @since 1.0.0
 */
function aiddata_lms_run_enrollment_tests() {
	// Check if user has permission.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to run tests.', 'aiddata-lms' ) );
	}

	// Load the enrollment manager.
	require_once AIDDATA_LMS_PATH . 'includes/tutorials/class-aiddata-lms-tutorial-enrollment.php';
	require_once AIDDATA_LMS_PATH . 'includes/tutorials/class-aiddata-lms-tutorial-enrollment-test.php';

	// Create test instance.
	$test = new AidData_LMS_Tutorial_Enrollment_Test();

	// Run tests.
	$results = $test->run_all_tests();

	// Display results.
	$test->display_results();
}

// Hook into admin if accessed via admin.
if ( is_admin() && isset( $_GET['page'] ) && 'run-enrollment-tests' === $_GET['page'] ) {
	add_action( 'admin_menu', 'aiddata_lms_add_test_page' );
}

/**
 * Add test page to admin menu.
 *
 * @since 1.0.0
 */
function aiddata_lms_add_test_page() {
	add_submenu_page(
		null, // No parent menu (hidden).
		__( 'Enrollment Tests', 'aiddata-lms' ),
		__( 'Enrollment Tests', 'aiddata-lms' ),
		'manage_options',
		'run-enrollment-tests',
		'aiddata_lms_run_enrollment_tests'
	);
}

