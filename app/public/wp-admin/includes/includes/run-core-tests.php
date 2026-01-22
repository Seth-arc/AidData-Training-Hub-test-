<?php
/**
 * Core Plugin Test Runner
 *
 * Standalone script to test core plugin functionality
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 * @since      2.0.0
 */

// Find WordPress
$wp_load_paths = array(
	__DIR__ . '/../../../../../wp-load.php',  // Standard WordPress structure
	__DIR__ . '/../../../../../../wp-load.php',  // Alternative structure
	__DIR__ . '/../../../wp-load.php',  // Local by Flywheel structure
	__DIR__ . '/../../../../wp-load.php',  // Current path
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
	die( "Error: Could not find wp-load.php. Please run this script from the plugin directory.\n" );
}

// Load test class
if ( ! class_exists( 'AidData_LMS_Core_Test' ) ) {
	require_once __DIR__ . '/class-aiddata-lms-core-test.php';
}

echo "\n";
echo "=== AIDDATA LMS CORE PLUGIN TEST ===\n";
echo "Date: " . date( 'Y-m-d H:i:s' ) . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "WordPress Version: " . get_bloginfo( 'version' ) . "\n";
echo "\n";

$start_time = microtime( true );

// Run tests
$results = AidData_LMS_Core_Test::run_tests();
$summary = AidData_LMS_Core_Test::generate_report( $results );

// Display results
foreach ( $results as $category => $tests ) {
	echo str_pad( strtoupper( str_replace( '_', ' ', $category ) ), 50, '=' ) . "\n\n";
	
	foreach ( $tests as $test_name => $test ) {
		$status = $test['result'] === $test['expected'] ? 'PASS' : 'FAIL';
		$icon   = $status === 'PASS' ? '✅' : '❌';
		
		echo sprintf(
			"  %s %s - %s\n",
			$icon,
			$status,
			$test['description']
		);
	}
	
	echo "\n";
}

// Display summary
echo str_pad( 'SUMMARY', 50, '=' ) . "\n\n";
echo "Total Tests: " . $summary['total_tests'] . "\n";
echo "Passed: " . $summary['passed_tests'] . "\n";
echo "Failed: " . $summary['failed_tests'] . "\n";
echo "Pass Rate: " . $summary['pass_rate'] . "%\n";
echo "\n";

// Category breakdown
echo "Category Breakdown:\n";
foreach ( $summary['categories'] as $category => $stats ) {
	echo sprintf(
		"  %s: %d/%d passed\n",
		ucwords( str_replace( '_', ' ', $category ) ),
		$stats['passed'],
		$stats['total']
	);
}
echo "\n";

$execution_time = round( ( microtime( true ) - $start_time ) * 1000, 2 );
echo "Execution Time: {$execution_time}ms\n";
echo "\n";

// Final status
if ( $summary['failed_tests'] === 0 ) {
	echo "✅ ALL TESTS PASSED\n\n";
	exit( 0 );
} else {
	echo "❌ SOME TESTS FAILED\n\n";
	exit( 1 );
}

