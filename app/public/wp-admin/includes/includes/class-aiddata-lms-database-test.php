<?php
/**
 * Database Testing Class
 *
 * Comprehensive testing and validation for database schema, integrity, and operations.
 *
 * @package    AidData_LMS
 * @subpackage Database
 * @since      2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Database_Test
 *
 * Provides comprehensive testing and validation for the database schema,
 * foreign key constraints, indexes, and data integrity.
 *
 * @since 2.0.0
 */
class AidData_LMS_Database_Test {

	/**
	 * Test results storage
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private static $test_results = array();

	/**
	 * Run all database tests
	 *
	 * Executes comprehensive test suite covering all aspects of database validation.
	 *
	 * @since 2.0.0
	 * @return array Test results with pass/fail status for each test
	 */
	public static function run_tests(): array {
		self::$test_results = array(
			'timestamp'     => current_time( 'mysql' ),
			'summary'       => array(
				'total'    => 0,
				'passed'   => 0,
				'failed'   => 0,
				'warnings' => 0,
			),
			'tests'         => array(),
			'environment'   => self::test_environment(),
			'tables'        => array(),
			'foreign_keys'  => array(),
			'indexes'       => array(),
			'data_integrity' => array(),
		);

		// Run test categories
		self::run_table_existence_tests();
		self::run_schema_validation_tests();
		self::run_foreign_key_tests();
		self::run_index_validation_tests();
		self::run_data_integrity_tests();

		// Calculate summary
		self::calculate_summary();

		return self::$test_results;
	}

	/**
	 * Test environment configuration
	 *
	 * Validates PHP, WordPress, MySQL versions and required extensions.
	 *
	 * @since 2.0.0
	 * @return array Environment test results
	 */
	public static function test_environment(): array {
		global $wpdb;

		$environment = array();

		// PHP Version
		$php_version = phpversion();
		$environment['php_version'] = array(
			'value'    => $php_version,
			'required' => '8.1',
			'pass'     => version_compare( $php_version, '8.1', '>=' ),
			'message'  => version_compare( $php_version, '8.1', '>=' ) ? 
				'PHP version meets requirements' : 
				'PHP version below 8.1',
		);

		// WordPress Version
		$wp_version = get_bloginfo( 'version' );
		$environment['wp_version'] = array(
			'value'    => $wp_version,
			'required' => '6.4',
			'pass'     => version_compare( $wp_version, '6.4', '>=' ),
			'message'  => version_compare( $wp_version, '6.4', '>=' ) ? 
				'WordPress version meets requirements' : 
				'WordPress version below 6.4',
		);

		// MySQL Version
		$mysql_version = $wpdb->db_version();
		$environment['mysql_version'] = array(
			'value'    => $mysql_version,
			'required' => '8.0',
			'pass'     => version_compare( $mysql_version, '8.0', '>=' ),
			'message'  => version_compare( $mysql_version, '8.0', '>=' ) ? 
				'MySQL version meets requirements' : 
				'MySQL version below 8.0 (recommended)',
		);

		// Required PHP Extensions
		$required_extensions = array( 'mbstring', 'mysqli', 'json', 'openssl' );
		$environment['php_extensions'] = array(
			'required' => $required_extensions,
			'missing'  => array(),
			'pass'     => true,
		);

		foreach ( $required_extensions as $ext ) {
			if ( ! extension_loaded( $ext ) ) {
				$environment['php_extensions']['missing'][] = $ext;
				$environment['php_extensions']['pass']      = false;
			}
		}

		$environment['php_extensions']['message'] = empty( $environment['php_extensions']['missing'] ) ? 
			'All required PHP extensions loaded' : 
			'Missing extensions: ' . implode( ', ', $environment['php_extensions']['missing'] );

		// Memory Limit
		$memory_limit = ini_get( 'memory_limit' );
		$memory_bytes = self::convert_to_bytes( $memory_limit );
		$environment['memory_limit'] = array(
			'value'    => $memory_limit,
			'required' => '128M',
			'pass'     => $memory_bytes >= 134217728, // 128MB
			'message'  => $memory_bytes >= 134217728 ? 
				'Memory limit adequate' : 
				'Memory limit below 128MB',
		);

		// Max Execution Time
		$max_execution_time = ini_get( 'max_execution_time' );
		$environment['max_execution_time'] = array(
			'value'    => $max_execution_time,
			'required' => '30',
			'pass'     => $max_execution_time >= 30 || $max_execution_time == 0,
			'message'  => ( $max_execution_time >= 30 || $max_execution_time == 0 ) ? 
				'Execution time adequate' : 
				'Execution time below 30 seconds',
		);

		return $environment;
	}

	/**
	 * Test if a specific table exists
	 *
	 * @since 2.0.0
	 * @param string $table_name Table name without prefix
	 * @return bool True if table exists, false otherwise
	 */
	public static function test_table_exists( string $table_name ): bool {
		global $wpdb;

		$full_table_name = $wpdb->prefix . $table_name;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->get_var( 
			$wpdb->prepare( 
				'SHOW TABLES LIKE %s', 
				$full_table_name 
			) 
		);

		return $result === $full_table_name;
	}

	/**
	 * Test foreign keys for a specific table
	 *
	 * @since 2.0.0
	 * @param string $table_name Table name without prefix
	 * @return array Foreign key test results
	 */
	public static function test_foreign_keys( string $table_name ): array {
		global $wpdb;

		$full_table_name = $wpdb->prefix . $table_name;
		
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$foreign_keys = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					CONSTRAINT_NAME,
					COLUMN_NAME,
					REFERENCED_TABLE_NAME,
					REFERENCED_COLUMN_NAME,
					DELETE_RULE,
					UPDATE_RULE
				FROM information_schema.KEY_COLUMN_USAGE
				WHERE TABLE_SCHEMA = %s
					AND TABLE_NAME = %s
					AND REFERENCED_TABLE_NAME IS NOT NULL",
				DB_NAME,
				$full_table_name
			),
			ARRAY_A
		);

		return array(
			'table'   => $table_name,
			'count'   => count( $foreign_keys ),
			'keys'    => $foreign_keys,
			'pass'    => ! empty( $foreign_keys ),
			'message' => count( $foreign_keys ) > 0 ? 
				sprintf( 'Found %d foreign key(s)', count( $foreign_keys ) ) : 
				'No foreign keys found',
		);
	}

	/**
	 * Test indexes for a specific table
	 *
	 * @since 2.0.0
	 * @param string $table_name Table name without prefix
	 * @return array Index test results
	 */
	public static function test_indexes( string $table_name ): array {
		global $wpdb;

		$full_table_name = $wpdb->prefix . $table_name;
		
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$indexes = $wpdb->get_results(
			$wpdb->prepare(
				'SHOW INDEX FROM %i',
				$full_table_name
			),
			ARRAY_A
		);

		$index_info = array();
		foreach ( $indexes as $index ) {
			$key_name = $index['Key_name'];
			if ( ! isset( $index_info[ $key_name ] ) ) {
				$index_info[ $key_name ] = array(
					'name'    => $key_name,
					'unique'  => $index['Non_unique'] == 0,
					'columns' => array(),
				);
			}
			$index_info[ $key_name ]['columns'][] = $index['Column_name'];
		}

		return array(
			'table'   => $table_name,
			'count'   => count( $index_info ),
			'indexes' => array_values( $index_info ),
			'pass'    => count( $index_info ) > 0,
			'message' => count( $index_info ) > 0 ? 
				sprintf( 'Found %d index(es)', count( $index_info ) ) : 
				'No indexes found',
		);
	}

	/**
	 * Test data integrity
	 *
	 * Runs validation queries to ensure referential integrity and data consistency.
	 *
	 * @since 2.0.0
	 * @return array Data integrity test results
	 */
	public static function test_data_integrity(): array {
		global $wpdb;

		$results = array();

		// Test 1: Orphaned enrollments (user_id references deleted users)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$orphaned_enrollments = $wpdb->get_var(
			"SELECT COUNT(*) 
			FROM {$wpdb->prefix}aiddata_lms_tutorial_enrollments e
			LEFT JOIN {$wpdb->users} u ON e.user_id = u.ID
			WHERE u.ID IS NULL"
		);

		$results['orphaned_enrollments_users'] = array(
			'test'    => 'Enrollments with deleted users',
			'count'   => (int) $orphaned_enrollments,
			'pass'    => $orphaned_enrollments == 0,
			'message' => $orphaned_enrollments == 0 ? 
				'No orphaned enrollment records (users)' : 
				sprintf( 'Found %d orphaned enrollment(s) referencing deleted users', $orphaned_enrollments ),
		);

		// Test 2: Orphaned enrollments (tutorial_id references deleted posts)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$orphaned_enrollments_posts = $wpdb->get_var(
			"SELECT COUNT(*) 
			FROM {$wpdb->prefix}aiddata_lms_tutorial_enrollments e
			LEFT JOIN {$wpdb->posts} p ON e.tutorial_id = p.ID
			WHERE p.ID IS NULL"
		);

		$results['orphaned_enrollments_posts'] = array(
			'test'    => 'Enrollments with deleted tutorials',
			'count'   => (int) $orphaned_enrollments_posts,
			'pass'    => $orphaned_enrollments_posts == 0,
			'message' => $orphaned_enrollments_posts == 0 ? 
				'No orphaned enrollment records (tutorials)' : 
				sprintf( 'Found %d orphaned enrollment(s) referencing deleted tutorials', $orphaned_enrollments_posts ),
		);

		// Test 3: Orphaned progress records (user_id)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$orphaned_progress_users = $wpdb->get_var(
			"SELECT COUNT(*) 
			FROM {$wpdb->prefix}aiddata_lms_tutorial_progress p
			LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
			WHERE u.ID IS NULL"
		);

		$results['orphaned_progress_users'] = array(
			'test'    => 'Progress with deleted users',
			'count'   => (int) $orphaned_progress_users,
			'pass'    => $orphaned_progress_users == 0,
			'message' => $orphaned_progress_users == 0 ? 
				'No orphaned progress records (users)' : 
				sprintf( 'Found %d orphaned progress record(s) referencing deleted users', $orphaned_progress_users ),
		);

		// Test 4: Orphaned progress records (tutorial_id)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$orphaned_progress_posts = $wpdb->get_var(
			"SELECT COUNT(*) 
			FROM {$wpdb->prefix}aiddata_lms_tutorial_progress p
			LEFT JOIN {$wpdb->posts} t ON p.tutorial_id = t.ID
			WHERE t.ID IS NULL"
		);

		$results['orphaned_progress_posts'] = array(
			'test'    => 'Progress with deleted tutorials',
			'count'   => (int) $orphaned_progress_posts,
			'pass'    => $orphaned_progress_posts == 0,
			'message' => $orphaned_progress_posts == 0 ? 
				'No orphaned progress records (tutorials)' : 
				sprintf( 'Found %d orphaned progress record(s) referencing deleted tutorials', $orphaned_progress_posts ),
		);

		// Test 5: Orphaned progress records (enrollment_id)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$orphaned_progress_enrollment = $wpdb->get_var(
			"SELECT COUNT(*) 
			FROM {$wpdb->prefix}aiddata_lms_tutorial_progress p
			LEFT JOIN {$wpdb->prefix}aiddata_lms_tutorial_enrollments e ON p.enrollment_id = e.id
			WHERE p.enrollment_id IS NOT NULL AND e.id IS NULL"
		);

		$results['orphaned_progress_enrollments'] = array(
			'test'    => 'Progress with deleted enrollments',
			'count'   => (int) $orphaned_progress_enrollment,
			'pass'    => $orphaned_progress_enrollment == 0,
			'message' => $orphaned_progress_enrollment == 0 ? 
				'No orphaned progress records (enrollments)' : 
				sprintf( 'Found %d orphaned progress record(s) referencing deleted enrollments', $orphaned_progress_enrollment ),
		);

		return $results;
	}

	/**
	 * Run table existence tests
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function run_table_existence_tests(): void {
		$tables = array(
			'aiddata_lms_tutorial_enrollments',
			'aiddata_lms_tutorial_progress',
			'aiddata_lms_video_progress',
			'aiddata_lms_certificates',
			'aiddata_lms_tutorial_analytics',
			'aiddata_lms_email_queue',
		);

		foreach ( $tables as $table ) {
			$exists = self::test_table_exists( $table );
			
			self::$test_results['tables'][ $table ] = array(
				'name'    => $table,
				'exists'  => $exists,
				'pass'    => $exists,
				'message' => $exists ? 'Table exists' : 'Table not found',
			);

			self::$test_results['tests'][] = array(
				'category' => 'Table Existence',
				'test'     => $table,
				'pass'     => $exists,
				'message'  => $exists ? 'Table exists' : 'Table not found',
			);
		}
	}

	/**
	 * Run schema validation tests
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function run_schema_validation_tests(): void {
		global $wpdb;

		$schema_tests = array(
			'aiddata_lms_tutorial_enrollments' => array(
				'columns' => 7,
				'engine'  => 'InnoDB',
				'charset' => 'utf8mb4',
			),
			'aiddata_lms_tutorial_progress'    => array(
				'columns' => 15,
				'engine'  => 'InnoDB',
				'charset' => 'utf8mb4',
			),
			'aiddata_lms_video_progress'       => array(
				'columns' => 16,
				'engine'  => 'InnoDB',
				'charset' => 'utf8mb4',
			),
			'aiddata_lms_certificates'         => array(
				'columns' => 17,
				'engine'  => 'InnoDB',
				'charset' => 'utf8mb4',
			),
			'aiddata_lms_tutorial_analytics'   => array(
				'columns' => 10,
				'engine'  => 'InnoDB',
				'charset' => 'utf8mb4',
			),
			'aiddata_lms_email_queue'          => array(
				'columns' => 17,
				'engine'  => 'InnoDB',
				'charset' => 'utf8mb4',
			),
		);

		foreach ( $schema_tests as $table => $expected ) {
			$full_table_name = $wpdb->prefix . $table;

			// Test column count
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$columns = $wpdb->get_results(
				$wpdb->prepare(
					'DESCRIBE %i',
					$full_table_name
				),
				ARRAY_A
			);

			$column_count = count( $columns );
			$column_pass  = $column_count === $expected['columns'];

			self::$test_results['tests'][] = array(
				'category' => 'Schema Validation',
				'test'     => $table . ' - Column Count',
				'pass'     => $column_pass,
				'message'  => sprintf( 
					'Expected %d columns, found %d', 
					$expected['columns'], 
					$column_count 
				),
			);

			// Test table engine and charset
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$table_status = $wpdb->get_row(
				$wpdb->prepare(
					'SHOW TABLE STATUS WHERE Name = %s',
					$full_table_name
				),
				ARRAY_A
			);

			if ( $table_status ) {
				$engine_pass = $table_status['Engine'] === $expected['engine'];
				self::$test_results['tests'][] = array(
					'category' => 'Schema Validation',
					'test'     => $table . ' - Engine',
					'pass'     => $engine_pass,
					'message'  => sprintf( 
						'Expected %s, found %s', 
						$expected['engine'], 
						$table_status['Engine'] 
					),
				);

				$charset_pass = strpos( $table_status['Collation'], $expected['charset'] ) === 0;
				self::$test_results['tests'][] = array(
					'category' => 'Schema Validation',
					'test'     => $table . ' - Charset',
					'pass'     => $charset_pass,
					'message'  => sprintf( 
						'Expected %s, found %s', 
						$expected['charset'], 
						$table_status['Collation'] 
					),
				);
			}
		}
	}

	/**
	 * Run foreign key tests
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function run_foreign_key_tests(): void {
		$tables_with_fks = array(
			'aiddata_lms_tutorial_enrollments' => 2, // user_id, tutorial_id
			'aiddata_lms_tutorial_progress'    => 3, // user_id, tutorial_id, enrollment_id
			'aiddata_lms_video_progress'       => 2, // user_id, tutorial_id
			'aiddata_lms_certificates'         => 2, // user_id, tutorial_id
			'aiddata_lms_tutorial_analytics'   => 2, // user_id, tutorial_id
			'aiddata_lms_email_queue'          => 1, // user_id
		);

		foreach ( $tables_with_fks as $table => $expected_count ) {
			$fk_results = self::test_foreign_keys( $table );
			
			self::$test_results['foreign_keys'][ $table ] = $fk_results;

			$pass = $fk_results['count'] >= $expected_count;

			self::$test_results['tests'][] = array(
				'category' => 'Foreign Keys',
				'test'     => $table,
				'pass'     => $pass,
				'message'  => sprintf( 
					'Expected at least %d FK(s), found %d', 
					$expected_count, 
					$fk_results['count'] 
				),
			);
		}
	}

	/**
	 * Run index validation tests
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function run_index_validation_tests(): void {
		$tables = array(
			'aiddata_lms_tutorial_enrollments',
			'aiddata_lms_tutorial_progress',
			'aiddata_lms_video_progress',
			'aiddata_lms_certificates',
			'aiddata_lms_tutorial_analytics',
			'aiddata_lms_email_queue',
		);

		foreach ( $tables as $table ) {
			$index_results = self::test_indexes( $table );
			
			self::$test_results['indexes'][ $table ] = $index_results;

			self::$test_results['tests'][] = array(
				'category' => 'Indexes',
				'test'     => $table,
				'pass'     => $index_results['pass'],
				'message'  => $index_results['message'],
			);
		}
	}

	/**
	 * Run data integrity tests
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function run_data_integrity_tests(): void {
		$integrity_results = self::test_data_integrity();

		foreach ( $integrity_results as $key => $result ) {
			self::$test_results['data_integrity'][ $key ] = $result;

			self::$test_results['tests'][] = array(
				'category' => 'Data Integrity',
				'test'     => $result['test'],
				'pass'     => $result['pass'],
				'message'  => $result['message'],
			);
		}
	}

	/**
	 * Calculate test summary
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function calculate_summary(): void {
		$total   = count( self::$test_results['tests'] );
		$passed  = 0;
		$failed  = 0;
		$warnings = 0;

		foreach ( self::$test_results['tests'] as $test ) {
			if ( $test['pass'] ) {
				$passed++;
			} else {
				// Environment tests are warnings, others are failures
				if ( isset( $test['category'] ) && $test['category'] === 'Environment' ) {
					$warnings++;
				} else {
					$failed++;
				}
			}
		}

		self::$test_results['summary'] = array(
			'total'    => $total,
			'passed'   => $passed,
			'failed'   => $failed,
			'warnings' => $warnings,
			'pass_rate' => $total > 0 ? round( ( $passed / $total ) * 100, 2 ) : 0,
		);
	}

	/**
	 * Convert PHP memory notation to bytes
	 *
	 * @since 2.0.0
	 * @param string $value Memory value (e.g., '128M', '1G')
	 * @return int Bytes
	 */
	private static function convert_to_bytes( string $value ): int {
		$value = trim( $value );
		$last  = strtolower( $value[ strlen( $value ) - 1 ] );
		$value = (int) $value;

		switch ( $last ) {
			case 'g':
				$value *= 1024;
				// Fall through.
			case 'm':
				$value *= 1024;
				// Fall through.
			case 'k':
				$value *= 1024;
		}

		return $value;
	}

	/**
	 * Generate detailed HTML report
	 *
	 * @since 2.0.0
	 * @param array $results Test results from run_tests()
	 * @return string HTML report
	 */
	public static function generate_html_report( array $results ): string {
		ob_start();
		?>
		<div class="aiddata-lms-test-report">
			<div class="aiddata-header">
				<h1>AidData LMS Database Test Report</h1>
				<p class="timestamp">Generated: <?php echo esc_html( $results['timestamp'] ); ?></p>
			</div>

			<!-- Summary -->
			<div class="test-summary <?php echo $results['summary']['failed'] === 0 ? 'passed' : 'failed'; ?>">
				<h2>Test Summary</h2>
				<div class="summary-grid">
					<div class="summary-item">
						<span class="label">Total Tests:</span>
						<span class="value"><?php echo esc_html( $results['summary']['total'] ); ?></span>
					</div>
					<div class="summary-item passed">
						<span class="label">Passed:</span>
						<span class="value"><?php echo esc_html( $results['summary']['passed'] ); ?></span>
					</div>
					<div class="summary-item failed">
						<span class="label">Failed:</span>
						<span class="value"><?php echo esc_html( $results['summary']['failed'] ); ?></span>
					</div>
					<div class="summary-item warnings">
						<span class="label">Warnings:</span>
						<span class="value"><?php echo esc_html( $results['summary']['warnings'] ); ?></span>
					</div>
					<div class="summary-item pass-rate">
						<span class="label">Pass Rate:</span>
						<span class="value"><?php echo esc_html( $results['summary']['pass_rate'] ); ?>%</span>
					</div>
				</div>
			</div>

			<!-- Environment Tests -->
			<div class="test-section">
				<h2>Environment Validation</h2>
				<table class="test-table">
					<thead>
						<tr>
							<th>Check</th>
							<th>Required</th>
							<th>Current</th>
							<th>Status</th>
							<th>Message</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $results['environment'] as $key => $env ) : ?>
							<?php if ( is_array( $env ) && isset( $env['pass'] ) ) : ?>
								<tr class="<?php echo $env['pass'] ? 'passed' : 'failed'; ?>">
									<td><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></td>
									<td><?php echo esc_html( $env['required'] ?? 'N/A' ); ?></td>
									<td><?php echo esc_html( $env['value'] ?? 'N/A' ); ?></td>
									<td>
										<span class="status-badge <?php echo $env['pass'] ? 'pass' : 'fail'; ?>">
											<?php echo $env['pass'] ? '✓ Pass' : '✗ Fail'; ?>
										</span>
									</td>
									<td><?php echo esc_html( $env['message'] ); ?></td>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- All Tests by Category -->
			<div class="test-section">
				<h2>Detailed Test Results</h2>
				<?php
				$categories = array();
				foreach ( $results['tests'] as $test ) {
					$category = $test['category'] ?? 'Other';
					if ( ! isset( $categories[ $category ] ) ) {
						$categories[ $category ] = array();
					}
					$categories[ $category ][] = $test;
				}
				?>

				<?php foreach ( $categories as $category => $tests ) : ?>
					<div class="test-category">
						<h3><?php echo esc_html( $category ); ?></h3>
						<table class="test-table">
							<thead>
								<tr>
									<th>Test</th>
									<th>Status</th>
									<th>Message</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $tests as $test ) : ?>
									<tr class="<?php echo $test['pass'] ? 'passed' : 'failed'; ?>">
										<td><?php echo esc_html( $test['test'] ); ?></td>
										<td>
											<span class="status-badge <?php echo $test['pass'] ? 'pass' : 'fail'; ?>">
												<?php echo $test['pass'] ? '✓ Pass' : '✗ Fail'; ?>
											</span>
										</td>
										<td><?php echo esc_html( $test['message'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Conclusion -->
			<div class="test-conclusion <?php echo $results['summary']['failed'] === 0 ? 'passed' : 'failed'; ?>">
				<?php if ( $results['summary']['failed'] === 0 ) : ?>
					<h2>✓ All Tests Passed</h2>
					<p>Database schema is correctly implemented and validated. Ready for production use.</p>
				<?php else : ?>
					<h2>✗ Tests Failed</h2>
					<p>Some tests failed. Please review the errors above and take corrective action.</p>
				<?php endif; ?>
			</div>
		</div>

		<style>
			.aiddata-lms-test-report {
				font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
				max-width: 1200px;
				margin: 20px auto;
				padding: 20px;
				background: #fff;
			}
			.aiddata-header {
				text-align: center;
				padding: 20px 0;
				border-bottom: 2px solid #ddd;
			}
			.aiddata-header h1 {
				margin: 0 0 10px 0;
				color: #333;
			}
			.timestamp {
				color: #666;
				font-size: 14px;
			}
			.test-summary {
				margin: 30px 0;
				padding: 20px;
				border-radius: 8px;
				border: 2px solid #ddd;
			}
			.test-summary.passed {
				background: #f0f9f4;
				border-color: #10b981;
			}
			.test-summary.failed {
				background: #fef2f2;
				border-color: #ef4444;
			}
			.summary-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
				gap: 15px;
				margin-top: 15px;
			}
			.summary-item {
				padding: 15px;
				background: #fff;
				border-radius: 6px;
				text-align: center;
				border: 1px solid #e5e7eb;
			}
			.summary-item .label {
				display: block;
				font-size: 12px;
				color: #6b7280;
				margin-bottom: 5px;
			}
			.summary-item .value {
				display: block;
				font-size: 24px;
				font-weight: bold;
				color: #111827;
			}
			.test-section {
				margin: 30px 0;
			}
			.test-section h2, .test-category h3 {
				color: #111827;
				border-bottom: 2px solid #e5e7eb;
				padding-bottom: 10px;
				margin-bottom: 20px;
			}
			.test-table {
				width: 100%;
				border-collapse: collapse;
				margin-bottom: 20px;
			}
			.test-table th {
				background: #f9fafb;
				padding: 12px;
				text-align: left;
				border-bottom: 2px solid #e5e7eb;
				font-weight: 600;
				color: #374151;
			}
			.test-table td {
				padding: 12px;
				border-bottom: 1px solid #e5e7eb;
			}
			.test-table tr.passed {
				background: #f0fdf4;
			}
			.test-table tr.failed {
				background: #fef2f2;
			}
			.status-badge {
				display: inline-block;
				padding: 4px 12px;
				border-radius: 12px;
				font-size: 12px;
				font-weight: 600;
			}
			.status-badge.pass {
				background: #10b981;
				color: #fff;
			}
			.status-badge.fail {
				background: #ef4444;
				color: #fff;
			}
			.test-conclusion {
				margin: 30px 0;
				padding: 30px;
				border-radius: 8px;
				text-align: center;
			}
			.test-conclusion.passed {
				background: #f0f9f4;
				border: 2px solid #10b981;
			}
			.test-conclusion.failed {
				background: #fef2f2;
				border: 2px solid #ef4444;
			}
			.test-conclusion h2 {
				margin: 0 0 10px 0;
				border: none;
			}
		</style>
		<?php
		return ob_get_clean();
	}
}

