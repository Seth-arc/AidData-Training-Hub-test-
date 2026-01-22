<?php
/**
 * Autoloader Validation Test
 *
 * Tests all aspects of the autoloader implementation to ensure PSR-4 compliance
 * and proper functionality before moving to next phase.
 *
 * @package    AidData_LMS
 * @subpackage Tests
 * @since      2.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Autoloader_Validation
 *
 * Comprehensive validation of autoloader implementation.
 *
 * @since 2.0.0
 */
class AidData_LMS_Autoloader_Validation {

	/**
	 * Test results
	 *
	 * @since 2.0.0
	 * @var array<string, mixed>
	 */
	private static $results = array();

	/**
	 * Run all validation tests
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Test results.
	 */
	public static function run_all_tests(): array {
		self::$results = array(
			'timestamp'    => current_time( 'mysql' ),
			'php_version'  => PHP_VERSION,
			'tests'        => array(),
			'summary'      => array(
				'total'   => 0,
				'passed'  => 0,
				'failed'  => 0,
			),
		);

		// Run all tests
		self::test_autoloader_registered();
		self::test_base_class_loading();
		self::test_admin_class_loading();
		self::test_tutorial_class_loading();
		self::test_nested_namespace();
		self::test_naming_conventions();
		self::test_error_handling();
		self::test_psr4_compliance();

		// Calculate summary
		self::calculate_summary();

		return self::$results;
	}

	/**
	 * Test: Autoloader is registered
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_autoloader_registered(): void {
		$test_name = 'Autoloader Registration';
		$passed    = false;
		$message   = '';

		try {
			// Check if autoloader class exists
			if ( ! class_exists( 'AidData_LMS_Autoloader' ) ) {
				$message = 'Autoloader class does not exist';
			} else {
				// Check if autoloader is in registered autoloaders
				$autoloaders = spl_autoload_functions();
				$found       = false;

				foreach ( $autoloaders as $autoloader ) {
					if ( is_array( $autoloader ) && 
						 'AidData_LMS_Autoloader' === $autoloader[0] && 
						 'autoload' === $autoloader[1] ) {
						$found = true;
						break;
					}
				}

				if ( $found ) {
					$passed  = true;
					$message = 'Autoloader successfully registered with SPL';
				} else {
					$message = 'Autoloader class exists but not registered with SPL';
				}
			}
		} catch ( Exception $e ) {
			$message = 'Exception: ' . $e->getMessage();
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Test: Base class loading
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_base_class_loading(): void {
		$test_name = 'Base Class Loading';
		$passed    = false;
		$message   = '';

		try {
			// Try to load base test class
			if ( class_exists( 'AidData_LMS_Test' ) ) {
				$test = new AidData_LMS_Test();
				if ( method_exists( $test, 'get_message' ) && ! empty( $test->get_message() ) ) {
					$passed  = true;
					$message = 'Base class loaded successfully from /includes/';
				} else {
					$message = 'Base class loaded but methods not working';
				}
			} else {
				$message = 'Failed to load base class AidData_LMS_Test';
			}
		} catch ( Exception $e ) {
			$message = 'Exception: ' . $e->getMessage();
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Test: Admin subdirectory class loading
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_admin_class_loading(): void {
		$test_name = 'Admin Subdirectory Loading';
		$passed    = false;
		$message   = '';

		try {
			// Try to load admin test class
			if ( class_exists( 'AidData_LMS_Admin_Test' ) ) {
				$test = new AidData_LMS_Admin_Test();
				if ( method_exists( $test, 'get_type' ) && 'admin' === $test->get_type() ) {
					$passed  = true;
					$message = 'Admin class loaded successfully from /includes/admin/';
				} else {
					$message = 'Admin class loaded but methods not working correctly';
				}
			} else {
				$message = 'Failed to load admin class AidData_LMS_Admin_Test';
			}
		} catch ( Exception $e ) {
			$message = 'Exception: ' . $e->getMessage();
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Test: Tutorial subdirectory class loading
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_tutorial_class_loading(): void {
		$test_name = 'Tutorial Subdirectory Loading';
		$passed    = false;
		$message   = '';

		try {
			// Try to load tutorial test class
			if ( class_exists( 'AidData_LMS_Tutorial_Test' ) ) {
				$test = new AidData_LMS_Tutorial_Test();
				if ( method_exists( $test, 'get_type' ) && 'tutorial' === $test->get_type() ) {
					$passed  = true;
					$message = 'Tutorial class loaded successfully from /includes/tutorials/';
				} else {
					$message = 'Tutorial class loaded but methods not working correctly';
				}
			} else {
				$message = 'Failed to load tutorial class AidData_LMS_Tutorial_Test';
			}
		} catch ( Exception $e ) {
			$message = 'Exception: ' . $e->getMessage();
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Test: Nested namespace handling
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_nested_namespace(): void {
		$test_name = 'Nested Namespace Handling';
		$passed    = true;
		$message   = 'Nested namespace mapping validated';

		// Check subdirectory map
		$map = AidData_LMS_Autoloader::get_subdir_map();

		$expected_mappings = array(
			'Admin'       => 'admin',
			'Tutorial'    => 'tutorials',
			'Video'       => 'video',
			'Quiz'        => 'quiz',
			'Certificate' => 'certificates',
			'Email'       => 'email',
			'Analytics'   => 'analytics',
			'API'         => 'api',
			'REST'        => 'api',
		);

		foreach ( $expected_mappings as $prefix => $subdir ) {
			if ( ! isset( $map[ $prefix ] ) || $map[ $prefix ] !== $subdir ) {
				$passed  = false;
				$message = "Missing or incorrect mapping for $prefix";
				break;
			}
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Test: Naming convention compliance
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_naming_conventions(): void {
		$test_name = 'Naming Convention Compliance';
		$passed    = true;
		$message   = 'All classes follow WordPress naming conventions';

		$test_classes = array(
			'AidData_LMS_Test'          => 'class-aiddata-lms-test.php',
			'AidData_LMS_Admin_Test'    => 'admin/class-aiddata-lms-admin-test.php',
			'AidData_LMS_Tutorial_Test' => 'tutorials/class-aiddata-lms-tutorial-test.php',
		);

		$base_dir = AidData_LMS_Autoloader::get_base_dir();

		foreach ( $test_classes as $class_name => $expected_file ) {
			$expected_path = $base_dir . $expected_file;
			if ( ! file_exists( $expected_path ) ) {
				$passed  = false;
				$message = "File not found at expected path: $expected_file";
				break;
			}
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Test: Error handling for non-existent classes
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_error_handling(): void {
		$test_name = 'Error Handling';
		$passed    = false;
		$message   = '';

		try {
			// Try to load a non-existent class
			$exists = class_exists( 'AidData_LMS_Nonexistent_Class_Name' );
			
			// Should return false, not throw an error
			if ( false === $exists ) {
				$passed  = true;
				$message = 'Autoloader handles non-existent classes gracefully';
			} else {
				$message = 'Non-existent class unexpectedly exists';
			}
		} catch ( Exception $e ) {
			$message = 'Exception thrown for non-existent class: ' . $e->getMessage();
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Test: PSR-4 compliance
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_psr4_compliance(): void {
		$test_name = 'PSR-4 Compliance';
		$passed    = true;
		$message   = 'Autoloader follows PSR-4 standards';

		// Check namespace mapping
		$base_dir = AidData_LMS_Autoloader::get_base_dir();

		if ( empty( $base_dir ) || ! is_dir( $base_dir ) ) {
			$passed  = false;
			$message = 'Base directory not properly set';
		} elseif ( ! method_exists( 'AidData_LMS_Autoloader', 'register' ) ) {
			$passed  = false;
			$message = 'Missing register() method';
		} elseif ( ! method_exists( 'AidData_LMS_Autoloader', 'autoload' ) ) {
			$passed  = false;
			$message = 'Missing autoload() method';
		}

		self::add_test_result( $test_name, $passed, $message );
	}

	/**
	 * Add test result
	 *
	 * @since 2.0.0
	 * @param string $test_name Test name.
	 * @param bool   $passed    Whether test passed.
	 * @param string $message   Test message.
	 * @return void
	 */
	private static function add_test_result( string $test_name, bool $passed, string $message ): void {
		self::$results['tests'][] = array(
			'name'    => $test_name,
			'status'  => $passed ? 'passed' : 'failed',
			'message' => $message,
		);
	}

	/**
	 * Calculate summary statistics
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function calculate_summary(): void {
		$total  = count( self::$results['tests'] );
		$passed = 0;
		$failed = 0;

		foreach ( self::$results['tests'] as $test ) {
			if ( 'passed' === $test['status'] ) {
				$passed++;
			} else {
				$failed++;
			}
		}

		self::$results['summary'] = array(
			'total'        => $total,
			'passed'       => $passed,
			'failed'       => $failed,
			'pass_rate'    => $total > 0 ? round( ( $passed / $total ) * 100, 2 ) : 0,
			'overall'      => $failed === 0 ? 'PASS' : 'FAIL',
		);
	}

	/**
	 * Get formatted report
	 *
	 * @since 2.0.0
	 * @return string Formatted report.
	 */
	public static function get_formatted_report(): string {
		$results = self::run_all_tests();
		$output  = '';

		$output .= "=== AUTOLOADER VALIDATION REPORT ===\n\n";
		$output .= "Timestamp: {$results['timestamp']}\n";
		$output .= "PHP Version: {$results['php_version']}\n\n";

		$output .= "=== TEST RESULTS ===\n\n";
		foreach ( $results['tests'] as $test ) {
			$status = 'passed' === $test['status'] ? '✅ PASS' : '❌ FAIL';
			$output .= "{$status}: {$test['name']}\n";
			$output .= "   {$test['message']}\n\n";
		}

		$output .= "=== SUMMARY ===\n\n";
		$output .= "Total Tests: {$results['summary']['total']}\n";
		$output .= "Passed: {$results['summary']['passed']}\n";
		$output .= "Failed: {$results['summary']['failed']}\n";
		$output .= "Pass Rate: {$results['summary']['pass_rate']}%\n";
		$output .= "Overall: {$results['summary']['overall']}\n";

		return $output;
	}
}

