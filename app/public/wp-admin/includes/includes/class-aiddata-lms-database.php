<?php
/**
 * Database Helper Class
 *
 * Provides database utility methods and table name constants.
 *
 * @package AidData_LMS
 * @subpackage Database
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database Helper Class
 *
 * Provides static methods for database operations and table name management.
 *
 * @since 2.0.0
 */
class AidData_LMS_Database {

	/**
	 * Table name constants.
	 *
	 * @since 2.0.0
	 */
	public const TABLE_ENROLLMENTS  = 'aiddata_lms_tutorial_enrollments';
	public const TABLE_PROGRESS     = 'aiddata_lms_tutorial_progress';
	public const TABLE_VIDEO        = 'aiddata_lms_video_progress';
	public const TABLE_CERTIFICATES = 'aiddata_lms_certificates';
	public const TABLE_ANALYTICS    = 'aiddata_lms_tutorial_analytics';
	public const TABLE_EMAIL        = 'aiddata_lms_email_queue';

	/**
	 * Check if a table exists.
	 *
	 * @since 2.0.0
	 * @param string $table_name The full table name (with prefix).
	 * @return bool True if table exists, false otherwise.
	 */
	public static function table_exists( string $table_name ): bool {
		global $wpdb;

		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		return ( $table_name === $table_exists );
	}

	/**
	 * Get full table name with prefix.
	 *
	 * @since 2.0.0
	 * @param string $table_key Table constant key (e.g., 'enrollments', 'progress').
	 * @return string|null Full table name with prefix, or null if invalid key.
	 */
	public static function get_table_name( string $table_key ): ?string {
		global $wpdb;

		$tables = array(
			'enrollments'  => self::TABLE_ENROLLMENTS,
			'progress'     => self::TABLE_PROGRESS,
			'video'        => self::TABLE_VIDEO,
			'certificates' => self::TABLE_CERTIFICATES,
			'analytics'    => self::TABLE_ANALYTICS,
			'email'        => self::TABLE_EMAIL,
		);

		if ( ! isset( $tables[ $table_key ] ) ) {
			return null;
		}

		return $wpdb->prefix . $tables[ $table_key ];
	}

	/**
	 * Verify database schema integrity.
	 *
	 * Checks if all required tables exist and have correct structure.
	 *
	 * @since 2.0.0
	 * @return array Array of validation results.
	 */
	public static function verify_schema(): array {
		$results = array(
			'tables_exist'     => array(),
			'all_tables_exist' => true,
			'missing_tables'   => array(),
		);

		$table_keys = array( 'enrollments', 'progress', 'video', 'certificates', 'analytics', 'email' );

		foreach ( $table_keys as $key ) {
			$table_name = self::get_table_name( $key );
			$exists     = self::table_exists( $table_name );

			$results['tables_exist'][ $key ] = $exists;

			if ( ! $exists ) {
				$results['all_tables_exist'] = false;
				$results['missing_tables'][] = $key;
			}
		}

		return $results;
	}

	/**
	 * Get all LMS table names.
	 *
	 * Returns an array of all table names with WordPress prefix.
	 *
	 * @since 2.0.0
	 * @return array Array of table names.
	 */
	public static function get_all_tables(): array {
		global $wpdb;

		return array(
			'enrollments'  => $wpdb->prefix . self::TABLE_ENROLLMENTS,
			'progress'     => $wpdb->prefix . self::TABLE_PROGRESS,
			'video'        => $wpdb->prefix . self::TABLE_VIDEO,
			'certificates' => $wpdb->prefix . self::TABLE_CERTIFICATES,
			'analytics'    => $wpdb->prefix . self::TABLE_ANALYTICS,
			'email'        => $wpdb->prefix . self::TABLE_EMAIL,
		);
	}

	/**
	 * Get table column count.
	 *
	 * Returns the number of columns in a table.
	 *
	 * @since 2.0.0
	 * @param string $table_name Full table name with prefix.
	 * @return int Number of columns, or 0 if table doesn't exist.
	 */
	public static function get_column_count( string $table_name ): int {
		global $wpdb;

		if ( ! self::table_exists( $table_name ) ) {
			return 0;
		}

		$columns = $wpdb->get_results( "DESCRIBE $table_name", ARRAY_A );

		return is_array( $columns ) ? count( $columns ) : 0;
	}

	/**
	 * Get table row count.
	 *
	 * Returns the number of rows in a table.
	 *
	 * @since 2.0.0
	 * @param string $table_name Full table name with prefix.
	 * @return int Number of rows, or 0 if table doesn't exist.
	 */
	public static function get_row_count( string $table_name ): int {
		global $wpdb;

		if ( ! self::table_exists( $table_name ) ) {
			return 0;
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

		return (int) $count;
	}

	/**
	 * Get table size in MB.
	 *
	 * Returns the approximate size of a table in megabytes.
	 *
	 * @since 2.0.0
	 * @param string $table_name Full table name with prefix.
	 * @return float Table size in MB, or 0 if table doesn't exist.
	 */
	public static function get_table_size( string $table_name ): float {
		global $wpdb;

		if ( ! self::table_exists( $table_name ) ) {
			return 0.0;
		}

		$size = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT (data_length + index_length) / 1024 / 1024 
				FROM information_schema.TABLES 
				WHERE table_schema = %s 
				AND table_name = %s",
				DB_NAME,
				$table_name
			)
		);

		return (float) $size;
	}

	/**
	 * Get database statistics.
	 *
	 * Returns comprehensive statistics about all LMS tables.
	 *
	 * @since 2.0.0
	 * @return array Database statistics.
	 */
	public static function get_statistics(): array {
		$tables = self::get_all_tables();
		$stats  = array(
			'tables'      => array(),
			'total_rows'  => 0,
			'total_size'  => 0.0,
			'all_healthy' => true,
		);

		foreach ( $tables as $key => $table_name ) {
			$exists = self::table_exists( $table_name );

			$table_stats = array(
				'exists'   => $exists,
				'rows'     => 0,
				'columns'  => 0,
				'size_mb'  => 0.0,
			);

			if ( $exists ) {
				$table_stats['rows']    = self::get_row_count( $table_name );
				$table_stats['columns'] = self::get_column_count( $table_name );
				$table_stats['size_mb'] = self::get_table_size( $table_name );

				$stats['total_rows'] += $table_stats['rows'];
				$stats['total_size'] += $table_stats['size_mb'];
			} else {
				$stats['all_healthy'] = false;
			}

			$stats['tables'][ $key ] = $table_stats;
		}

		return $stats;
	}

	/**
	 * Check foreign key constraints.
	 *
	 * Verifies that foreign key constraints are properly set up.
	 *
	 * @since 2.0.0
	 * @param string $table_name Full table name with prefix.
	 * @return array Array of foreign key information.
	 */
	public static function check_foreign_keys( string $table_name ): array {
		global $wpdb;

		if ( ! self::table_exists( $table_name ) ) {
			return array();
		}

		$constraints = $wpdb->get_results(
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
				$table_name
			),
			ARRAY_A
		);

		return is_array( $constraints ) ? $constraints : array();
	}

	/**
	 * Validate table indexes.
	 *
	 * Checks if all expected indexes exist on a table.
	 *
	 * @since 2.0.0
	 * @param string $table_name Full table name with prefix.
	 * @return array Array of index information.
	 */
	public static function validate_indexes( string $table_name ): array {
		global $wpdb;

		if ( ! self::table_exists( $table_name ) ) {
			return array();
		}

		$indexes = $wpdb->get_results( "SHOW INDEX FROM $table_name", ARRAY_A );

		$index_summary = array();

		if ( is_array( $indexes ) ) {
			foreach ( $indexes as $index ) {
				$index_name = $index['Key_name'];

				if ( ! isset( $index_summary[ $index_name ] ) ) {
					$index_summary[ $index_name ] = array(
						'type'    => ( 'PRIMARY' === $index_name ) ? 'PRIMARY' : ( ( 0 === (int) $index['Non_unique'] ) ? 'UNIQUE' : 'INDEX' ),
						'columns' => array(),
					);
				}

				$index_summary[ $index_name ]['columns'][] = $index['Column_name'];
			}
		}

		return $index_summary;
	}
}

