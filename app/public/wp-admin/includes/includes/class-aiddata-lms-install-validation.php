<?php
/**
 * Database Installation Validation Class
 *
 * Validates database table creation, schema structure, and integrity.
 *
 * @package AidData_LMS
 * @subpackage Validation
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installation Validation Class
 *
 * Provides comprehensive validation of database installation and schema.
 *
 * @since 2.0.0
 */
class AidData_LMS_Install_Validation {

	/**
	 * Validation results.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private static array $results = array();

	/**
	 * Test counter.
	 *
	 * @since 2.0.0
	 * @var int
	 */
	private static int $test_count = 0;

	/**
	 * Passed test counter.
	 *
	 * @since 2.0.0
	 * @var int
	 */
	private static int $passed_count = 0;

	/**
	 * Run all validation tests.
	 *
	 * @since 2.0.0
	 * @return array Validation results.
	 */
	public static function run_all_tests(): array {
		self::$results      = array();
		self::$test_count   = 0;
		self::$passed_count = 0;

		echo "\n=== DATABASE INSTALLATION VALIDATION ===\n\n";

		// Test 1: Table Existence.
		self::test_tables_exist();

		// Test 2: Table Structure.
		self::test_table_structures();

		// Test 3: Primary Keys.
		self::test_primary_keys();

		// Test 4: Indexes.
		self::test_indexes();

		// Test 5: Foreign Keys.
		self::test_foreign_keys();

		// Test 6: Column Data Types.
		self::test_column_types();

		// Test 7: Default Values.
		self::test_default_values();

		// Test 8: Charset and Collation.
		self::test_charset_collation();

		// Test 9: Options Created.
		self::test_options_created();

		// Test 10: Capabilities Created.
		self::test_capabilities();

		// Test 11: Database Version.
		self::test_database_version();

		// Summary.
		self::print_summary();

		return self::$results;
	}

	/**
	 * Test if all required tables exist.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_tables_exist(): void {
		global $wpdb;

		echo "Test 1: Table Existence\n";
		echo str_repeat( '-', 50 ) . "\n";

		$tables = array(
			'enrollments'  => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'     => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'        => $wpdb->prefix . 'aiddata_lms_video_progress',
			'certificates' => $wpdb->prefix . 'aiddata_lms_certificates',
			'analytics'    => $wpdb->prefix . 'aiddata_lms_tutorial_analytics',
			'email'        => $wpdb->prefix . 'aiddata_lms_email_queue',
		);

		foreach ( $tables as $key => $table_name ) {
			self::$test_count++;
			$exists = ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name );

			if ( $exists ) {
				self::$passed_count++;
				self::$results[ "table_exists_$key" ] = true;
				echo "✅ PASS - Table '{$key}' exists\n";
			} else {
				self::$results[ "table_exists_$key" ] = false;
				echo "❌ FAIL - Table '{$key}' does not exist\n";
			}
		}

		echo "\n";
	}

	/**
	 * Test table structures.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_table_structures(): void {
		global $wpdb;

		echo "Test 2: Table Structures\n";
		echo str_repeat( '-', 50 ) . "\n";

		// Expected column counts.
		$expected_columns = array(
			'enrollments'  => 7,  // id, user_id, tutorial_id, enrolled_at, completed_at, status, source.
			'progress'     => 15, // id, user_id, tutorial_id, enrollment_id, current_step, completed_steps, progress_percent, status, quiz_passed, quiz_score, quiz_attempts, last_accessed, completed_at, time_spent, updated_at.
			'video'        => 16, // id, user_id, tutorial_id, step_index, video_url, video_platform, current_position, total_duration, watch_percent, completed, completed_at, watch_sessions, total_watch_time, last_position_update, created_at, updated_at.
			'certificates' => 17, // id, certificate_code, user_id, tutorial_id, user_name, tutorial_title, completion_date, issued_date, template_id, certificate_data, pdf_path, verification_url, downloads, last_downloaded, status, revoked_at, revoked_reason.
			'analytics'    => 10, // id, tutorial_id, user_id, event_type, event_data, session_id, ip_address, user_agent, referrer, created_at.
			'email'        => 17, // id, recipient_email, recipient_name, user_id, subject, message, email_type, template_id, template_data, priority, status, attempts, last_attempt, scheduled_for, sent_at, error_message, created_at.
		);

		foreach ( $expected_columns as $key => $expected_count ) {
			self::$test_count++;
			$table_name = '';

			// Map key to table name.
			switch ( $key ) {
				case 'enrollments':
					$table_name = $wpdb->prefix . 'aiddata_lms_tutorial_enrollments';
					break;
				case 'progress':
					$table_name = $wpdb->prefix . 'aiddata_lms_tutorial_progress';
					break;
				case 'video':
					$table_name = $wpdb->prefix . 'aiddata_lms_video_progress';
					break;
				case 'certificates':
					$table_name = $wpdb->prefix . 'aiddata_lms_certificates';
					break;
				case 'analytics':
					$table_name = $wpdb->prefix . 'aiddata_lms_tutorial_analytics';
					break;
				case 'email':
					$table_name = $wpdb->prefix . 'aiddata_lms_email_queue';
					break;
			}

			$columns = $wpdb->get_results( "DESCRIBE $table_name", ARRAY_A );
			$actual_count = count( $columns );

			if ( $actual_count === $expected_count ) {
				self::$passed_count++;
				self::$results[ "structure_$key" ] = true;
				echo "✅ PASS - '{$key}' has correct number of columns ($actual_count)\n";
			} else {
				self::$results[ "structure_$key" ] = false;
				echo "❌ FAIL - '{$key}' has $actual_count columns, expected $expected_count\n";
			}
		}

		echo "\n";
	}

	/**
	 * Test primary keys.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_primary_keys(): void {
		global $wpdb;

		echo "Test 3: Primary Keys\n";
		echo str_repeat( '-', 50 ) . "\n";

		$tables = array(
			'enrollments' => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'    => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'       => $wpdb->prefix . 'aiddata_lms_video_progress',
		);

		foreach ( $tables as $key => $table_name ) {
			self::$test_count++;

			$pk = $wpdb->get_row(
				"SHOW KEYS FROM $table_name WHERE Key_name = 'PRIMARY'",
				ARRAY_A
			);

			if ( $pk && 'id' === $pk['Column_name'] ) {
				self::$passed_count++;
				self::$results[ "pk_$key" ] = true;
				echo "✅ PASS - '{$key}' has primary key on 'id'\n";
			} else {
				self::$results[ "pk_$key" ] = false;
				echo "❌ FAIL - '{$key}' primary key not found or incorrect\n";
			}
		}

		echo "\n";
	}

	/**
	 * Test indexes.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_indexes(): void {
		global $wpdb;

		echo "Test 4: Indexes\n";
		echo str_repeat( '-', 50 ) . "\n";

		// Expected indexes per table.
		$expected_indexes = array(
			'enrollments' => array( 'PRIMARY', 'user_tutorial', 'tutorial_id', 'status', 'enrolled_at', 'completed_at' ),
			'progress'    => array( 'PRIMARY', 'user_tutorial', 'tutorial_id', 'enrollment_id', 'status', 'progress_percent', 'last_accessed' ),
			'video'       => array( 'PRIMARY', 'user_tutorial_step', 'tutorial_id', 'step_index', 'completed' ),
		);

		foreach ( $expected_indexes as $key => $expected_index_list ) {
			$table_name = $wpdb->prefix . 'aiddata_lms_tutorial_' . $key;

			if ( 'video' === $key ) {
				$table_name = $wpdb->prefix . 'aiddata_lms_video_progress';
			}

			$indexes = $wpdb->get_results( "SHOW INDEX FROM $table_name", ARRAY_A );
			$actual_indexes = array_unique( array_column( $indexes, 'Key_name' ) );

			foreach ( $expected_index_list as $expected_index ) {
				self::$test_count++;

				if ( in_array( $expected_index, $actual_indexes, true ) ) {
					self::$passed_count++;
					self::$results[ "index_{$key}_{$expected_index}" ] = true;
					echo "✅ PASS - '{$key}' has index '{$expected_index}'\n";
				} else {
					self::$results[ "index_{$key}_{$expected_index}" ] = false;
					echo "❌ FAIL - '{$key}' missing index '{$expected_index}'\n";
				}
			}
		}

		echo "\n";
	}

	/**
	 * Test foreign keys.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_foreign_keys(): void {
		global $wpdb;

		echo "Test 5: Foreign Keys\n";
		echo str_repeat( '-', 50 ) . "\n";

		$tables = array(
			'enrollments' => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'    => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'       => $wpdb->prefix . 'aiddata_lms_video_progress',
		);

		$expected_fks = array(
			'enrollments' => array( 'fk_enrollment_user', 'fk_enrollment_tutorial' ),
			'progress'    => array( 'fk_progress_user', 'fk_progress_tutorial', 'fk_progress_enrollment' ),
			'video'       => array( 'fk_video_user', 'fk_video_tutorial' ),
		);

		$db_name = $wpdb->dbname;

		foreach ( $expected_fks as $key => $fk_list ) {
			$table_name = $wpdb->prefix . 'aiddata_lms_tutorial_' . $key;

			if ( 'video' === $key ) {
				$table_name = $wpdb->prefix . 'aiddata_lms_video_progress';
			}

			$foreign_keys = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT CONSTRAINT_NAME 
					FROM information_schema.TABLE_CONSTRAINTS 
					WHERE TABLE_SCHEMA = %s 
					AND TABLE_NAME = %s 
					AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
					$db_name,
					$table_name
				),
				ARRAY_A
			);

			$actual_fks = array_column( $foreign_keys, 'CONSTRAINT_NAME' );

			foreach ( $fk_list as $expected_fk ) {
				self::$test_count++;

				if ( in_array( $expected_fk, $actual_fks, true ) ) {
					self::$passed_count++;
					self::$results[ "fk_{$key}_{$expected_fk}" ] = true;
					echo "✅ PASS - '{$key}' has foreign key '{$expected_fk}'\n";
				} else {
					self::$results[ "fk_{$key}_{$expected_fk}" ] = false;
					echo "⚠️  WARN - '{$key}' missing foreign key '{$expected_fk}' (may already exist)\n";
				}
			}
		}

		echo "\n";
	}

	/**
	 * Test column data types.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_column_types(): void {
		global $wpdb;

		echo "Test 6: Column Data Types\n";
		echo str_repeat( '-', 50 ) . "\n";

		// Test critical columns have correct types.
		$critical_types = array(
			'enrollments' => array(
				'id'          => 'bigint',
				'user_id'     => 'bigint',
				'tutorial_id' => 'bigint',
				'status'      => 'varchar',
			),
			'progress'    => array(
				'id'               => 'bigint',
				'user_id'          => 'bigint',
				'tutorial_id'      => 'bigint',
				'progress_percent' => 'decimal',
				'quiz_passed'      => 'tinyint',
			),
			'video'       => array(
				'id'             => 'bigint',
				'user_id'        => 'bigint',
				'tutorial_id'    => 'bigint',
				'step_index'     => 'int',
				'watch_percent'  => 'decimal',
			),
		);

		foreach ( $critical_types as $key => $columns ) {
			$table_name = $wpdb->prefix . 'aiddata_lms_tutorial_' . $key;

			if ( 'video' === $key ) {
				$table_name = $wpdb->prefix . 'aiddata_lms_video_progress';
			}

			$table_columns = $wpdb->get_results( "DESCRIBE $table_name", ARRAY_A );
			$column_types = array();

			foreach ( $table_columns as $col ) {
				$type = $col['Type'];
				// Extract base type (remove size and unsigned).
				if ( preg_match( '/^([a-z]+)/i', $type, $matches ) ) {
					$column_types[ $col['Field'] ] = strtolower( $matches[1] );
				}
			}

			foreach ( $columns as $col_name => $expected_type ) {
				self::$test_count++;

				if ( isset( $column_types[ $col_name ] ) && $column_types[ $col_name ] === $expected_type ) {
					self::$passed_count++;
					self::$results[ "type_{$key}_{$col_name}" ] = true;
					echo "✅ PASS - '{$key}.{$col_name}' is '{$expected_type}'\n";
				} else {
					$actual_type = $column_types[ $col_name ] ?? 'NOT FOUND';
					self::$results[ "type_{$key}_{$col_name}" ] = false;
					echo "❌ FAIL - '{$key}.{$col_name}' is '{$actual_type}', expected '{$expected_type}'\n";
				}
			}
		}

		echo "\n";
	}

	/**
	 * Test default values.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_default_values(): void {
		global $wpdb;

		echo "Test 7: Default Values\n";
		echo str_repeat( '-', 50 ) . "\n";

		$expected_defaults = array(
			'enrollments' => array(
				'status' => 'active',
				'source' => 'web',
			),
			'progress'    => array(
				'current_step'     => '0',
				'progress_percent' => '0.00',
				'status'           => 'not_started',
				'quiz_passed'      => '0',
				'quiz_attempts'    => '0',
				'time_spent'       => '0',
			),
			'video'       => array(
				'current_position'  => '0',
				'total_duration'    => '0',
				'watch_percent'     => '0.00',
				'completed'         => '0',
				'watch_sessions'    => '0',
				'total_watch_time'  => '0',
			),
		);

		foreach ( $expected_defaults as $key => $columns ) {
			$table_name = $wpdb->prefix . 'aiddata_lms_tutorial_' . $key;

			if ( 'video' === $key ) {
				$table_name = $wpdb->prefix . 'aiddata_lms_video_progress';
			}

			$table_columns = $wpdb->get_results( "DESCRIBE $table_name", ARRAY_A );
			$column_defaults = array();

			foreach ( $table_columns as $col ) {
				$column_defaults[ $col['Field'] ] = $col['Default'];
			}

			foreach ( $columns as $col_name => $expected_default ) {
				self::$test_count++;

				$actual_default = $column_defaults[ $col_name ] ?? null;

				if ( $actual_default === $expected_default ) {
					self::$passed_count++;
					self::$results[ "default_{$key}_{$col_name}" ] = true;
					echo "✅ PASS - '{$key}.{$col_name}' has default '{$expected_default}'\n";
				} else {
					self::$results[ "default_{$key}_{$col_name}" ] = false;
					echo "❌ FAIL - '{$key}.{$col_name}' default is '{$actual_default}', expected '{$expected_default}'\n";
				}
			}
		}

		echo "\n";
	}

	/**
	 * Test charset and collation.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_charset_collation(): void {
		global $wpdb;

		echo "Test 8: Charset and Collation\n";
		echo str_repeat( '-', 50 ) . "\n";

		$tables = array(
			'enrollments' => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'    => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'       => $wpdb->prefix . 'aiddata_lms_video_progress',
		);

		foreach ( $tables as $key => $table_name ) {
			self::$test_count++;

			$status = $wpdb->get_row( "SHOW TABLE STATUS LIKE '$table_name'", ARRAY_A );

			if ( $status && 'utf8mb4' === $status['Collation'] || strpos( $status['Collation'], 'utf8mb4' ) === 0 ) {
				self::$passed_count++;
				self::$results[ "charset_$key" ] = true;
				echo "✅ PASS - '{$key}' uses utf8mb4 charset\n";
			} else {
				$collation = $status['Collation'] ?? 'UNKNOWN';
				self::$results[ "charset_$key" ] = false;
				echo "❌ FAIL - '{$key}' collation is '{$collation}', expected utf8mb4\n";
			}
		}

		echo "\n";
	}

	/**
	 * Test plugin options.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_options_created(): void {
		echo "Test 9: Plugin Options\n";
		echo str_repeat( '-', 50 ) . "\n";

		$critical_options = array(
			'aiddata_lms_enable_enrollments',
			'aiddata_lms_enable_certificates',
			'aiddata_lms_video_completion_percent',
			'aiddata_lms_quiz_passing_score',
			'aiddata_lms_installed_at',
		);

		foreach ( $critical_options as $option_name ) {
			self::$test_count++;
			$value = get_option( $option_name );

			if ( false !== $value ) {
				self::$passed_count++;
				self::$results[ "option_$option_name" ] = true;
				echo "✅ PASS - Option '{$option_name}' exists\n";
			} else {
				self::$results[ "option_$option_name" ] = false;
				echo "❌ FAIL - Option '{$option_name}' not found\n";
			}
		}

		echo "\n";
	}

	/**
	 * Test capabilities.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_capabilities(): void {
		echo "Test 10: Capabilities\n";
		echo str_repeat( '-', 50 ) . "\n";

		$admin_role = get_role( 'administrator' );

		if ( null === $admin_role ) {
			echo "❌ FAIL - Administrator role not found\n\n";
			return;
		}

		$expected_caps = array(
			'manage_aiddata_lms',
			'manage_tutorial_enrollments',
			'view_tutorial_analytics',
			'issue_certificates',
		);

		foreach ( $expected_caps as $cap ) {
			self::$test_count++;

			if ( $admin_role->has_cap( $cap ) ) {
				self::$passed_count++;
				self::$results[ "cap_$cap" ] = true;
				echo "✅ PASS - Capability '{$cap}' exists\n";
			} else {
				self::$results[ "cap_$cap" ] = false;
				echo "❌ FAIL - Capability '{$cap}' not found\n";
			}
		}

		echo "\n";
	}

	/**
	 * Test database version.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function test_database_version(): void {
		echo "Test 11: Database Version\n";
		echo str_repeat( '-', 50 ) . "\n";

		self::$test_count++;

		$db_version = get_option( 'aiddata_lms_db_version' );

		if ( $db_version && version_compare( $db_version, '2.0.0', '>=' ) ) {
			self::$passed_count++;
			self::$results['db_version'] = true;
			echo "✅ PASS - Database version is $db_version\n";
		} else {
			self::$results['db_version'] = false;
			echo "❌ FAIL - Database version is '$db_version', expected >= 2.0.0\n";
		}

		echo "\n";
	}

	/**
	 * Print summary.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function print_summary(): void {
		echo "=== SUMMARY ===\n";
		echo "Total Tests: " . self::$test_count . "\n";
		echo "Passed: " . self::$passed_count . "\n";
		echo "Failed: " . ( self::$test_count - self::$passed_count ) . "\n";

		$pass_rate = self::$test_count > 0 ? round( ( self::$passed_count / self::$test_count ) * 100, 2 ) : 0;
		echo "Pass Rate: {$pass_rate}%\n\n";

		if ( self::$passed_count === self::$test_count ) {
			echo "✅ ALL TESTS PASSED\n";
		} else {
			echo "❌ SOME TESTS FAILED\n";
		}

		echo "\n";
	}

	/**
	 * Get detailed table report.
	 *
	 * @since 2.0.0
	 * @param string $table_key Table key (enrollments, progress, video).
	 * @return string Formatted table report.
	 */
	public static function get_table_report( string $table_key ): string {
		global $wpdb;

		$tables = array(
			'enrollments' => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'    => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'       => $wpdb->prefix . 'aiddata_lms_video_progress',
		);

		if ( ! isset( $tables[ $table_key ] ) ) {
			return "Invalid table key: $table_key\n";
		}

		$table_name = $tables[ $table_key ];
		$output = "\n=== TABLE REPORT: $table_name ===\n\n";

		// Columns.
		$columns = $wpdb->get_results( "DESCRIBE $table_name", ARRAY_A );
		$output .= "Columns:\n";
		foreach ( $columns as $col ) {
			$output .= sprintf(
				"  - %s: %s %s %s\n",
				$col['Field'],
				$col['Type'],
				$col['Null'] === 'NO' ? 'NOT NULL' : 'NULL',
				$col['Default'] ? "DEFAULT {$col['Default']}" : ''
			);
		}

		// Indexes.
		$indexes = $wpdb->get_results( "SHOW INDEX FROM $table_name", ARRAY_A );
		$output .= "\nIndexes:\n";
		$unique_indexes = array();
		foreach ( $indexes as $idx ) {
			$key = $idx['Key_name'];
			if ( ! isset( $unique_indexes[ $key ] ) ) {
				$unique_indexes[ $key ] = array(
					'unique' => $idx['Non_unique'] === '0',
					'columns' => array(),
				);
			}
			$unique_indexes[ $key ]['columns'][] = $idx['Column_name'];
		}
		foreach ( $unique_indexes as $name => $info ) {
			$type = $info['unique'] ? 'UNIQUE' : 'INDEX';
			$cols = implode( ', ', $info['columns'] );
			$output .= "  - $type $name ($cols)\n";
		}

		// Table status.
		$status = $wpdb->get_row( "SHOW TABLE STATUS LIKE '$table_name'", ARRAY_A );
		$output .= "\nTable Status:\n";
		$output .= "  - Engine: " . ( $status['Engine'] ?? 'UNKNOWN' ) . "\n";
		$output .= "  - Collation: " . ( $status['Collation'] ?? 'UNKNOWN' ) . "\n";
		$output .= "  - Rows: " . ( $status['Rows'] ?? '0' ) . "\n";

		return $output;
	}
}

