<?php
/**
 * Standalone Database Test Runner
 *
 * Run database tests from command line or browser without WordPress admin.
 *
 * @package    AidData_LMS
 * @subpackage Database
 * @since      2.0.0
 */

// Load WordPress
$wp_load_paths = array(
	__DIR__ . '/../../../../../../wp-load.php', // Standard WordPress structure
	__DIR__ . '/../../../wp-load.php',          // Alternative structure
);

$wp_loaded = false;
foreach ( $wp_load_paths as $path ) {
	if ( file_exists( $path ) ) {
		require_once $path;
		$wp_loaded = true;
		break;
	}
}

if ( ! $wp_loaded ) {
	die( 'Error: Could not find wp-load.php. Please run this script from within WordPress.' );
}

// Load required classes
require_once AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-database-test.php';

// Run tests
echo "=== AIDDATA LMS DATABASE TESTS ===\n\n";
echo "Running comprehensive database validation...\n\n";

$start_time = microtime( true );

// Run all tests
$results = AidData_LMS_Database_Test::run_tests();

$end_time = microtime( true );
$execution_time = round( $end_time - $start_time, 2 );

// Display results
echo "=== TEST SUMMARY ===\n";
echo "Total Tests: " . $results['summary']['total'] . "\n";
echo "Passed: " . $results['summary']['passed'] . "\n";
echo "Failed: " . $results['summary']['failed'] . "\n";
echo "Warnings: " . $results['summary']['warnings'] . "\n";
echo "Pass Rate: " . $results['summary']['pass_rate'] . "%\n";
echo "Execution Time: " . $execution_time . "s\n\n";

// Display environment
echo "=== ENVIRONMENT ===\n";
foreach ( $results['environment'] as $key => $env ) {
	if ( is_array( $env ) && isset( $env['pass'] ) ) {
		$status = $env['pass'] ? '[PASS]' : '[FAIL]';
		echo sprintf( "%s %s: %s (Required: %s)\n", 
			$status, 
			ucwords( str_replace( '_', ' ', $key ) ), 
			$env['value'] ?? 'N/A', 
			$env['required'] ?? 'N/A' 
		);
	}
}
echo "\n";

// Display failed tests
if ( $results['summary']['failed'] > 0 ) {
	echo "=== FAILED TESTS ===\n";
	foreach ( $results['tests'] as $test ) {
		if ( ! $test['pass'] ) {
			echo sprintf( "[FAIL] %s - %s: %s\n", 
				$test['category'], 
				$test['test'], 
				$test['message'] 
			);
		}
	}
	echo "\n";
}

// Display table status
echo "=== TABLE STATUS ===\n";
foreach ( $results['tables'] as $table => $info ) {
	$status = $info['exists'] ? '[EXISTS]' : '[MISSING]';
	echo sprintf( "%s %s\n", $status, $table );
}
echo "\n";

// Display foreign keys
echo "=== FOREIGN KEYS ===\n";
foreach ( $results['foreign_keys'] as $table => $fk_info ) {
	echo sprintf( "%s: %d foreign key(s)\n", $table, $fk_info['count'] );
}
echo "\n";

// Display indexes
echo "=== INDEXES ===\n";
foreach ( $results['indexes'] as $table => $idx_info ) {
	echo sprintf( "%s: %d index(es)\n", $table, $idx_info['count'] );
}
echo "\n";

// Display data integrity
if ( ! empty( $results['data_integrity'] ) ) {
	echo "=== DATA INTEGRITY ===\n";
	foreach ( $results['data_integrity'] as $key => $integrity ) {
		$status = $integrity['pass'] ? '[OK]' : '[ISSUE]';
		echo sprintf( "%s %s: %s\n", 
			$status, 
			$integrity['test'], 
			$integrity['message'] 
		);
	}
	echo "\n";
}

// Final conclusion
echo "=== CONCLUSION ===\n";
if ( $results['summary']['failed'] === 0 ) {
	echo "✓ ALL TESTS PASSED\n";
	echo "Database schema is correctly implemented and validated.\n";
	exit( 0 );
} else {
	echo "✗ SOME TESTS FAILED\n";
	echo "Please review the errors above and take corrective action.\n";
	exit( 1 );
}

