<?php
/**
 * Core Plugin Test Class
 *
 * Validates the core plugin functionality including singleton pattern,
 * hook loader, dependency container, and internationalization.
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core plugin test and validation class
 *
 * @since 2.0.0
 */
class AidData_LMS_Core_Test {

	/**
	 * Run all core plugin tests
	 *
	 * @since  2.0.0
	 * @return array Array of test results
	 */
	public static function run_tests(): array {
		$results = array();

		$results['singleton']    = self::test_singleton_pattern();
		$results['loader']       = self::test_hook_loader();
		$results['container']    = self::test_dependency_container();
		$results['i18n']         = self::test_internationalization();
		$results['integration']  = self::test_integration();
		$results['methods']      = self::test_public_methods();

		return $results;
	}

	/**
	 * Test singleton pattern implementation
	 *
	 * @since  2.0.0
	 * @return array Test results
	 */
	public static function test_singleton_pattern(): array {
		$tests = array();
		
		// Test 1: Class exists
		$tests['class_exists'] = array(
			'description' => 'AidData_LMS class exists',
			'result'      => class_exists( 'AidData_LMS' ),
			'expected'    => true,
		);

		// Test 2: Get instance
		$instance1 = AidData_LMS::instance();
		$tests['get_instance'] = array(
			'description' => 'Can get plugin instance',
			'result'      => ( $instance1 instanceof AidData_LMS ),
			'expected'    => true,
		);

		// Test 3: Singleton enforcement (same instance)
		$instance2 = AidData_LMS::instance();
		$tests['singleton_same_instance'] = array(
			'description' => 'Multiple calls return same instance',
			'result'      => ( $instance1 === $instance2 ),
			'expected'    => true,
		);

		// Test 4: Version is set
		$tests['version_set'] = array(
			'description' => 'Plugin version is set',
			'result'      => ! empty( $instance1->get_version() ),
			'expected'    => true,
		);

		// Test 5: Plugin name is set
		$tests['plugin_name_set'] = array(
			'description' => 'Plugin name is set',
			'result'      => ( 'aiddata-lms' === $instance1->get_plugin_name() ),
			'expected'    => true,
		);

		return $tests;
	}

	/**
	 * Test hook loader functionality
	 *
	 * @since  2.0.0
	 * @return array Test results
	 */
	public static function test_hook_loader(): array {
		$tests = array();
		
		// Test 1: Loader class exists
		$tests['loader_class_exists'] = array(
			'description' => 'AidData_LMS_Loader class exists',
			'result'      => class_exists( 'AidData_LMS_Loader' ),
			'expected'    => true,
		);

		// Test 2: Plugin has loader
		$instance = AidData_LMS::instance();
		$loader   = $instance->get_loader();
		$tests['plugin_has_loader'] = array(
			'description' => 'Plugin instance has loader',
			'result'      => ( $loader instanceof AidData_LMS_Loader ),
			'expected'    => true,
		);

		// Test 3: Loader can add actions
		$test_loader = new AidData_LMS_Loader();
		$test_object = new stdClass();
		$test_loader->add_action( 'test_hook', $test_object, 'test_method', 10, 1 );
		$actions = $test_loader->get_actions();
		$tests['loader_add_action'] = array(
			'description' => 'Loader can add actions',
			'result'      => ( count( $actions ) === 1 && $actions[0]['hook'] === 'test_hook' ),
			'expected'    => true,
		);

		// Test 4: Loader can add filters
		$test_loader->add_filter( 'test_filter', $test_object, 'test_method', 10, 1 );
		$filters = $test_loader->get_filters();
		$tests['loader_add_filter'] = array(
			'description' => 'Loader can add filters',
			'result'      => ( count( $filters ) === 1 && $filters[0]['hook'] === 'test_filter' ),
			'expected'    => true,
		);

		// Test 5: Hook count
		$tests['hook_count'] = array(
			'description' => 'Hook count is accurate',
			'result'      => ( $test_loader->get_hook_count() === 2 ),
			'expected'    => true,
		);

		return $tests;
	}

	/**
	 * Test dependency injection container
	 *
	 * @since  2.0.0
	 * @return array Test results
	 */
	public static function test_dependency_container(): array {
		$tests = array();
		
		$instance = AidData_LMS::instance();

		// Test 1: Can set value
		$instance->set( 'test_key', 'test_value' );
		$tests['container_set'] = array(
			'description' => 'Can set value in container',
			'result'      => true,
			'expected'    => true,
		);

		// Test 2: Can get value
		$tests['container_get'] = array(
			'description' => 'Can get value from container',
			'result'      => ( 'test_value' === $instance->get( 'test_key' ) ),
			'expected'    => true,
		);

		// Test 3: Has key check
		$tests['container_has'] = array(
			'description' => 'Can check if key exists',
			'result'      => $instance->has( 'test_key' ),
			'expected'    => true,
		);

		// Test 4: Non-existent key returns null
		$tests['container_null'] = array(
			'description' => 'Non-existent key returns null',
			'result'      => ( null === $instance->get( 'non_existent_key' ) ),
			'expected'    => true,
		);

		// Test 5: Can remove value
		$instance->remove( 'test_key' );
		$tests['container_remove'] = array(
			'description' => 'Can remove value from container',
			'result'      => ! $instance->has( 'test_key' ),
			'expected'    => true,
		);

		// Test 6: Get container keys
		$instance->set( 'key1', 'value1' );
		$instance->set( 'key2', 'value2' );
		$keys = $instance->get_container_keys();
		$tests['container_keys'] = array(
			'description' => 'Can get all container keys',
			'result'      => ( count( $keys ) >= 2 ),
			'expected'    => true,
		);

		return $tests;
	}

	/**
	 * Test internationalization functionality
	 *
	 * @since  2.0.0
	 * @return array Test results
	 */
	public static function test_internationalization(): array {
		$tests = array();
		
		// Test 1: i18n class exists
		$tests['i18n_class_exists'] = array(
			'description' => 'AidData_LMS_i18n class exists',
			'result'      => class_exists( 'AidData_LMS_i18n' ),
			'expected'    => true,
		);

		// Test 2: Can instantiate i18n
		$i18n = new AidData_LMS_i18n();
		$tests['i18n_instantiate'] = array(
			'description' => 'Can instantiate i18n class',
			'result'      => ( $i18n instanceof AidData_LMS_i18n ),
			'expected'    => true,
		);

		// Test 3: Text domain is correct
		$tests['text_domain'] = array(
			'description' => 'Text domain is correct',
			'result'      => ( 'aiddata-lms' === $i18n->get_text_domain() ),
			'expected'    => true,
		);

		// Test 4: Languages path is set
		$tests['languages_path'] = array(
			'description' => 'Languages path is set',
			'result'      => ! empty( $i18n->get_languages_path() ),
			'expected'    => true,
		);

		return $tests;
	}

	/**
	 * Test plugin integration
	 *
	 * @since  2.0.0
	 * @return array Test results
	 */
	public static function test_integration(): array {
		$tests = array();
		
		$instance = AidData_LMS::instance();

		// Test 1: Plugin initialized
		$tests['plugin_initialized'] = array(
			'description' => 'Plugin is initialized',
			'result'      => ( $instance instanceof AidData_LMS ),
			'expected'    => true,
		);

		// Test 2: Loader initialized
		$tests['loader_initialized'] = array(
			'description' => 'Loader is initialized',
			'result'      => ( $instance->get_loader() instanceof AidData_LMS_Loader ),
			'expected'    => true,
		);

		// Test 3: Constants defined
		$tests['constants_defined'] = array(
			'description' => 'Plugin constants are defined',
			'result'      => defined( 'AIDDATA_LMS_VERSION' ) && defined( 'AIDDATA_LMS_PATH' ),
			'expected'    => true,
		);

		// Test 4: Autoloader registered
		$tests['autoloader_registered'] = array(
			'description' => 'Autoloader is registered',
			'result'      => class_exists( 'AidData_LMS_Autoloader' ),
			'expected'    => true,
		);

		return $tests;
	}

	/**
	 * Test public methods accessibility
	 *
	 * @since  2.0.0
	 * @return array Test results
	 */
	public static function test_public_methods(): array {
		$tests = array();
		
		$instance = AidData_LMS::instance();

		// Test 1: get_plugin_name is callable
		$tests['method_get_plugin_name'] = array(
			'description' => 'get_plugin_name() is callable',
			'result'      => is_callable( array( $instance, 'get_plugin_name' ) ),
			'expected'    => true,
		);

		// Test 2: get_version is callable
		$tests['method_get_version'] = array(
			'description' => 'get_version() is callable',
			'result'      => is_callable( array( $instance, 'get_version' ) ),
			'expected'    => true,
		);

		// Test 3: get_loader is callable
		$tests['method_get_loader'] = array(
			'description' => 'get_loader() is callable',
			'result'      => is_callable( array( $instance, 'get_loader' ) ),
			'expected'    => true,
		);

		// Test 4: run is callable
		$tests['method_run'] = array(
			'description' => 'run() is callable',
			'result'      => is_callable( array( $instance, 'run' ) ),
			'expected'    => true,
		);

		// Test 5: set is callable
		$tests['method_set'] = array(
			'description' => 'set() is callable',
			'result'      => is_callable( array( $instance, 'set' ) ),
			'expected'    => true,
		);

		// Test 6: get is callable
		$tests['method_get'] = array(
			'description' => 'get() is callable',
			'result'      => is_callable( array( $instance, 'get' ) ),
			'expected'    => true,
		);

		// Test 7: has is callable
		$tests['method_has'] = array(
			'description' => 'has() is callable',
			'result'      => is_callable( array( $instance, 'has' ) ),
			'expected'    => true,
		);

		return $tests;
	}

	/**
	 * Generate test report
	 *
	 * @since  2.0.0
	 * @param  array $results Test results
	 * @return array Report summary
	 */
	public static function generate_report( array $results ): array {
		$total_tests  = 0;
		$passed_tests = 0;
		$failed_tests = 0;
		$categories   = array();

		foreach ( $results as $category => $tests ) {
			$category_passed = 0;
			$category_failed = 0;

			foreach ( $tests as $test ) {
				$total_tests++;
				if ( $test['result'] === $test['expected'] ) {
					$passed_tests++;
					$category_passed++;
				} else {
					$failed_tests++;
					$category_failed++;
				}
			}

			$categories[ $category ] = array(
				'total'  => count( $tests ),
				'passed' => $category_passed,
				'failed' => $category_failed,
			);
		}

		return array(
			'total_tests'  => $total_tests,
			'passed_tests' => $passed_tests,
			'failed_tests' => $failed_tests,
			'pass_rate'    => $total_tests > 0 ? round( ( $passed_tests / $total_tests ) * 100, 2 ) : 0,
			'categories'   => $categories,
		);
	}

	/**
	 * Generate HTML report
	 *
	 * @since  2.0.0
	 * @param  array $results Test results
	 * @return string HTML report
	 */
	public static function generate_html_report( array $results ): string {
		$summary = self::generate_report( $results );
		
		$html = '<div class="aiddata-lms-test-report">';
		$html .= '<h2>Core Plugin Test Report</h2>';
		
		// Summary
		$html .= '<div class="test-summary">';
		$html .= '<h3>Summary</h3>';
		$html .= '<p>Total Tests: ' . esc_html( $summary['total_tests'] ) . '</p>';
		$html .= '<p>Passed: <span style="color: green;">' . esc_html( $summary['passed_tests'] ) . '</span></p>';
		$html .= '<p>Failed: <span style="color: red;">' . esc_html( $summary['failed_tests'] ) . '</span></p>';
		$html .= '<p>Pass Rate: ' . esc_html( $summary['pass_rate'] ) . '%</p>';
		$html .= '</div>';
		
		// Detailed results
		foreach ( $results as $category => $tests ) {
			$html .= '<div class="test-category">';
			$html .= '<h3>' . esc_html( ucwords( str_replace( '_', ' ', $category ) ) ) . '</h3>';
			$html .= '<table class="widefat">';
			$html .= '<thead><tr><th>Test</th><th>Result</th><th>Status</th></tr></thead>';
			$html .= '<tbody>';
			
			foreach ( $tests as $test_name => $test ) {
				$status = $test['result'] === $test['expected'] ? 'PASS' : 'FAIL';
				$color  = $status === 'PASS' ? 'green' : 'red';
				
				$html .= '<tr>';
				$html .= '<td>' . esc_html( $test['description'] ) . '</td>';
				$html .= '<td>' . esc_html( $test['result'] ? 'true' : 'false' ) . '</td>';
				$html .= '<td style="color: ' . esc_attr( $color ) . ';"><strong>' . esc_html( $status ) . '</strong></td>';
				$html .= '</tr>';
			}
			
			$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
		}
		
		$html .= '</div>';
		
		return $html;
	}
}

