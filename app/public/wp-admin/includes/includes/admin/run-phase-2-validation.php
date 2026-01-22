<?php
/**
 * CLI Script to Run Phase 2 Validation Tests
 *
 * Usage: php run-phase-2-validation.php
 * Or with WP-CLI: wp eval-file run-phase-2-validation.php
 *
 * @package AidData_LMS
 * @subpackage Admin
 * @since 2.0.0
 */

// Load WordPress if not already loaded
if ( ! defined( 'ABSPATH' ) ) {
	// Try to find wp-load.php
	$wp_load_paths = array(
		__DIR__ . '/../../../../../wp-load.php', // Standard WordPress structure
		__DIR__ . '/../../../../../../wp-load.php',
		__DIR__ . '/../../../wp-load.php',
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
		die( "Error: Could not find WordPress installation. Please run this script from within WordPress.\n" );
	}
}

// Ensure validation class is loaded
if ( ! class_exists( 'AidData_LMS_Phase_2_Validation' ) ) {
	require_once __DIR__ . '/class-aiddata-lms-phase-2-validation.php';
}

/**
 * CLI Validation Runner
 */
class AidData_LMS_CLI_Validation_Runner {

	/**
	 * Run validation and output results
	 */
	public static function run(): void {
		self::print_header();

		$start_time = microtime( true );

		// Run all tests
		$results = AidData_LMS_Phase_2_Validation::run_all_tests();

		$end_time = microtime( true );
		$execution_time = round( $end_time - $start_time, 2 );

		// Calculate statistics
		$stats = self::calculate_statistics( $results );

		// Display results
		self::print_results( $results, $stats );
		self::print_summary( $stats, $execution_time );
		self::print_footer();
	}

	/**
	 * Print header
	 */
	private static function print_header(): void {
		echo "\n";
		echo "================================================================================\n";
		echo "              AIDDATA LMS - PHASE 2 VALIDATION TESTS\n";
		echo "================================================================================\n";
		echo "Date: " . date( 'Y-m-d H:i:s' ) . "\n";
		echo "================================================================================\n\n";
	}

	/**
	 * Calculate statistics from results
	 *
	 * @param array $results Test results.
	 * @return array Statistics.
	 */
	private static function calculate_statistics( array $results ): array {
		$total_tests = 0;
		$passed_tests = 0;
		$failed_tests = 0;

		foreach ( $results as $category => $tests ) {
			foreach ( $tests as $test ) {
				$total_tests++;
				if ( $test['status'] ) {
					$passed_tests++;
				} else {
					$failed_tests++;
				}
			}
		}

		$pass_rate = $total_tests > 0 ? round( ( $passed_tests / $total_tests ) * 100, 2 ) : 0;

		return array(
			'total'     => $total_tests,
			'passed'    => $passed_tests,
			'failed'    => $failed_tests,
			'pass_rate' => $pass_rate,
		);
	}

	/**
	 * Print test results by category
	 *
	 * @param array $results Test results.
	 * @param array $stats Statistics.
	 */
	private static function print_results( array $results, array $stats ): void {
		foreach ( $results as $category => $tests ) {
			$category_name = ucwords( str_replace( '_', ' ', $category ) );
			echo "\n";
			echo "────────────────────────────────────────────────────────────────────────────────\n";
			echo "  {$category_name}\n";
			echo "────────────────────────────────────────────────────────────────────────────────\n";

			foreach ( $tests as $test_key => $test ) {
				$status_icon = $test['status'] ? '✓' : '✗';
				$status_text = $test['status'] ? 'PASS' : 'FAIL';
				$status_color = $test['status'] ? "\033[32m" : "\033[31m"; // Green or Red
				$reset_color = "\033[0m";

				printf(
					"  %s%s%s  %-40s  %s\n",
					$status_color,
					$status_icon,
					$reset_color,
					$test['name'],
					$test['message']
				);
			}
		}
	}

	/**
	 * Print summary
	 *
	 * @param array $stats Statistics.
	 * @param float $execution_time Execution time in seconds.
	 */
	private static function print_summary( array $stats, float $execution_time ): void {
		echo "\n";
		echo "================================================================================\n";
		echo "                              SUMMARY\n";
		echo "================================================================================\n";

		$pass_color = $stats['pass_rate'] >= 90 ? "\033[32m" : ( $stats['pass_rate'] >= 75 ? "\033[33m" : "\033[31m" );
		$reset_color = "\033[0m";

		printf( "  Total Tests:      %d\n", $stats['total'] );
		printf( "  Tests Passed:     \033[32m%d\033[0m\n", $stats['passed'] );
		printf( "  Tests Failed:     \033[31m%d\033[0m\n", $stats['failed'] );
		printf( "  Pass Rate:        %s%.2f%%%s\n", $pass_color, $stats['pass_rate'], $reset_color );
		printf( "  Execution Time:   %.2f seconds\n", $execution_time );

		echo "================================================================================\n";

		// Status message
		if ( $stats['pass_rate'] >= 90 ) {
			echo "\n\033[32m✓ EXCELLENT! Phase 2 is ready for Phase 3 advancement.\033[0m\n";
		} elseif ( $stats['pass_rate'] >= 75 ) {
			echo "\n\033[33m⚠ GOOD PROGRESS. Address failing tests before Phase 3.\033[0m\n";
		} else {
			echo "\n\033[31m✗ ACTION REQUIRED. Several critical features are missing.\033[0m\n";
		}
	}

	/**
	 * Print footer
	 */
	private static function print_footer(): void {
		echo "\n";
		echo "For detailed HTML report, visit:\n";
		echo "WordPress Admin → Tutorials → Phase 2 Validation\n";
		echo "\n";
	}
}

// Run validation if executed directly
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	// Check if running from command line
	if ( php_sapi_name() === 'cli' ) {
		AidData_LMS_CLI_Validation_Runner::run();
	} else {
		// Running via web - redirect to admin page
		if ( is_admin() && current_user_can( 'manage_options' ) ) {
			AidData_LMS_CLI_Validation_Runner::run();
		} else {
			die( 'This script should be run from the command line or by an administrator.' );
		}
	}
}

