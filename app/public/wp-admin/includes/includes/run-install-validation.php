<?php
/**
 * Standalone Database Installation Validation Runner
 *
 * Run this script to validate database installation.
 * Can be run from command line or browser.
 *
 * @package AidData_LMS
 * @subpackage Validation
 * @since 2.0.0
 */

// Determine if running in WordPress context.
$wp_loaded = false;

// Try to load WordPress.
$wp_load_paths = array(
	__DIR__ . '/../../../../../wp-load.php',  // Standard WordPress structure.
	__DIR__ . '/../../../../../../wp-load.php',
	__DIR__ . '/../../../wp-load.php',
);

foreach ( $wp_load_paths as $wp_load_path ) {
	if ( file_exists( $wp_load_path ) ) {
		require_once $wp_load_path;
		$wp_loaded = true;
		break;
	}
}

if ( ! $wp_loaded ) {
	die( "Error: Could not load WordPress. Please run this script from the plugin directory or ensure WordPress is accessible.\n" );
}

// Load the validation class.
require_once __DIR__ . '/class-aiddata-lms-autoloader.php';
AidData_LMS_Autoloader::register();

// Run validation.
if ( class_exists( 'AidData_LMS_Install_Validation' ) ) {
	$results = AidData_LMS_Install_Validation::run_all_tests();

	// Print detailed table reports.
	echo "\n=== DETAILED TABLE REPORTS ===\n";
	echo AidData_LMS_Install_Validation::get_table_report( 'enrollments' );
	echo AidData_LMS_Install_Validation::get_table_report( 'progress' );
	echo AidData_LMS_Install_Validation::get_table_report( 'video' );

	// Export results for validation report.
	$export_file = __DIR__ . '/validation-results.json';
	file_put_contents( $export_file, json_encode( $results, JSON_PRETTY_PRINT ) );
	echo "\nResults exported to: $export_file\n";
} else {
	die( "Error: AidData_LMS_Install_Validation class not found.\n" );
}

