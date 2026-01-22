<?php
/**
 * Phase 0 Validation Test Suite
 *
 * Comprehensive validation of all Phase 0 deliverables before proceeding to Phase 1.
 * Tests environment, database, post types, taxonomies, autoloader, core classes,
 * integration, security, and performance.
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes/admin
 * @since      2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Phase 0 Validation Class
 *
 * Provides comprehensive validation of all Phase 0 components.
 *
 * @since 2.0.0
 */
class AidData_LMS_Phase_0_Validation {

	/**
	 * Validation results.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private static array $results = array();

	/**
	 * Test counters.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private static array $counters = array(
		'total'   => 0,
		'passed'  => 0,
		'failed'  => 0,
		'skipped' => 0,
	);

	/**
	 * Performance metrics.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private static array $metrics = array();

	/**
	 * Start time for performance tracking.
	 *
	 * @since 2.0.0
	 * @var float
	 */
	private static float $start_time = 0.0;

	/**
	 * Run all validation tests.
	 *
	 * @since 2.0.0
	 * @return array Comprehensive validation results.
	 */
	public static function run_all_tests(): array {
		self::$start_time = microtime( true );
		self::$results    = array(
			'timestamp'   => current_time( 'mysql' ),
			'php_version' => PHP_VERSION,
			'wp_version'  => get_bloginfo( 'version' ),
			'tests'       => array(),
		);
		self::$counters   = array(
			'total'   => 0,
			'passed'  => 0,
			'failed'  => 0,
			'skipped' => 0,
		);
		self::$metrics    = array();

		// Run all test categories.
		self::test_environment();
		self::test_database_schema();
		self::test_post_types();
		self::test_taxonomies();
		self::test_autoloader();
		self::test_core_classes();
		self::test_integration();
		self::test_security();
		self::test_performance();

		// Add summary.
		self::$results['summary']     = self::$counters;
		self::$results['metrics']     = self::$metrics;
		self::$results['duration']    = microtime( true ) - self::$start_time;
		self::$results['passed']      = self::$counters['failed'] === 0;
		self::$results['phase_0_exit_criteria'] = self::evaluate_exit_criteria();

		return self::$results;
	}

	/**
	 * Test environment requirements.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_environment(): void {
		$category = 'Environment';

		// Test PHP version >= 8.1.
		self::add_test(
			$category,
			'PHP Version >= 8.1',
			version_compare( PHP_VERSION, '8.1.0', '>=' ),
			'PHP ' . PHP_VERSION . ( version_compare( PHP_VERSION, '8.1.0', '>=' ) ? ' meets requirement' : ' below minimum 8.1' )
		);

		// Test WordPress version >= 6.4.
		$wp_version = get_bloginfo( 'version' );
		self::add_test(
			$category,
			'WordPress Version >= 6.4',
			version_compare( $wp_version, '6.4.0', '>=' ),
			'WordPress ' . $wp_version . ( version_compare( $wp_version, '6.4.0', '>=' ) ? ' meets requirement' : ' below minimum 6.4' )
		);

		// Test MySQL version.
		global $wpdb;
		$mysql_version = $wpdb->db_version();
		self::add_test(
			$category,
			'MySQL Version >= 5.7',
			version_compare( $mysql_version, '5.7.0', '>=' ),
			'MySQL ' . $mysql_version . ( version_compare( $mysql_version, '5.7.0', '>=' ) ? ' meets requirement' : ' below minimum 5.7' )
		);

		// Test required PHP extensions.
		$required_extensions = array( 'mbstring', 'mysqli', 'json', 'curl' );
		foreach ( $required_extensions as $ext ) {
			self::add_test(
				$category,
				'PHP Extension: ' . $ext,
				extension_loaded( $ext ),
				extension_loaded( $ext ) ? $ext . ' extension loaded' : $ext . ' extension missing'
			);
		}

		// Test memory limit.
		$memory_limit = ini_get( 'memory_limit' );
		$memory_bytes = self::parse_memory_limit( $memory_limit );
		$min_memory   = 128 * 1024 * 1024; // 128MB.
		self::add_test(
			$category,
			'Memory Limit >= 128MB',
			$memory_bytes >= $min_memory || $memory_bytes === -1,
			'Memory limit: ' . $memory_limit . ( $memory_bytes >= $min_memory || $memory_bytes === -1 ? ' (adequate)' : ' (too low)' )
		);

		// Test max execution time.
		$max_execution = ini_get( 'max_execution_time' );
		self::add_test(
			$category,
			'Max Execution Time >= 30s',
			(int) $max_execution >= 30 || (int) $max_execution === 0,
			'Max execution: ' . $max_execution . 's' . ( (int) $max_execution >= 30 || (int) $max_execution === 0 ? ' (adequate)' : ' (too low)' )
		);

		// Test file permissions.
		$upload_dir = wp_upload_dir();
		self::add_test(
			$category,
			'Upload Directory Writable',
			is_writable( $upload_dir['basedir'] ),
			is_writable( $upload_dir['basedir'] ) ? 'Upload directory is writable' : 'Upload directory not writable'
		);

		// Test plugin directory permissions.
		self::add_test(
			$category,
			'Plugin Directory Readable',
			is_readable( AIDDATA_LMS_PATH ),
			is_readable( AIDDATA_LMS_PATH ) ? 'Plugin directory is readable' : 'Plugin directory not readable'
		);
	}

	/**
	 * Test database schema.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_database_schema(): void {
		global $wpdb;
		$category = 'Database Schema';

		// Expected tables.
		$tables = array(
			'enrollments' => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'    => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'       => $wpdb->prefix . 'aiddata_lms_video_progress',
			'certificates' => $wpdb->prefix . 'aiddata_lms_certificates',
			'analytics'   => $wpdb->prefix . 'aiddata_lms_tutorial_analytics',
			'email'       => $wpdb->prefix . 'aiddata_lms_email_queue',
		);

		// Test table existence.
		foreach ( $tables as $key => $table_name ) {
			$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
			self::add_test(
				$category,
				'Table Exists: ' . $key,
				$exists,
				$exists ? $table_name . ' exists' : $table_name . ' missing'
			);

			if ( ! $exists ) {
				continue;
			}

			// Test table structure.
			$columns = $wpdb->get_results( "DESCRIBE `{$table_name}`" );
			self::add_test(
				$category,
				'Table Structure: ' . $key,
				! empty( $columns ),
				! empty( $columns ) ? count( $columns ) . ' columns found' : 'No columns found'
			);

			// Test primary key.
			$has_pk = false;
			foreach ( $columns as $column ) {
				if ( $column->Key === 'PRI' ) {
					$has_pk = true;
					break;
				}
			}
			self::add_test(
				$category,
				'Primary Key: ' . $key,
				$has_pk,
				$has_pk ? 'Primary key exists' : 'Primary key missing'
			);

			// Test indexes.
			$indexes = $wpdb->get_results( "SHOW INDEX FROM `{$table_name}`" );
			self::add_test(
				$category,
				'Indexes: ' . $key,
				! empty( $indexes ),
				! empty( $indexes ) ? count( $indexes ) . ' indexes found' : 'No indexes found'
			);

			// Test charset and collation.
			$table_status = $wpdb->get_row( "SHOW TABLE STATUS LIKE '{$table_name}'" );
			if ( $table_status ) {
				$correct_charset = strpos( $table_status->Collation, 'utf8mb4' ) !== false;
				self::add_test(
					$category,
					'Charset: ' . $key,
					$correct_charset,
					$correct_charset ? 'utf8mb4 charset correct' : 'Incorrect charset: ' . $table_status->Collation
				);

				// Test engine.
				$is_innodb = $table_status->Engine === 'InnoDB';
				self::add_test(
					$category,
					'Engine: ' . $key,
					$is_innodb,
					$is_innodb ? 'InnoDB engine correct' : 'Incorrect engine: ' . $table_status->Engine
				);
			}
		}

		// Test foreign keys (check enrollments table as example).
		$table_name = $tables['enrollments'];
		$create_table = $wpdb->get_row( "SHOW CREATE TABLE `{$table_name}`" );
		if ( $create_table ) {
			$create_sql = $create_table->{'Create Table'};
			$has_fk     = strpos( $create_sql, 'FOREIGN KEY' ) !== false;
			self::add_test(
				$category,
				'Foreign Keys Defined',
				$has_fk,
				$has_fk ? 'Foreign keys found in schema' : 'No foreign keys found'
			);
		}

		// Test no orphaned records.
		$orphaned_enrollments = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$tables['enrollments']} e 
			LEFT JOIN {$wpdb->users} u ON e.user_id = u.ID 
			WHERE u.ID IS NULL"
		);
		self::add_test(
			$category,
			'No Orphaned Enrollments',
			(int) $orphaned_enrollments === 0,
			'Found ' . $orphaned_enrollments . ' orphaned enrollment records'
		);

		// Test database version option.
		$db_version = get_option( 'aiddata_lms_db_version' );
		self::add_test(
			$category,
			'Database Version Stored',
			! empty( $db_version ),
			! empty( $db_version ) ? 'Version: ' . $db_version : 'Database version not stored'
		);
	}

	/**
	 * Test post types.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_post_types(): void {
		$category = 'Post Types';

		// Test tutorial post type.
		$tutorial_pt = get_post_type_object( 'aiddata_tutorial' );
		self::add_test(
			$category,
			'Tutorial Post Type Registered',
			$tutorial_pt !== null,
			$tutorial_pt ? 'aiddata_tutorial registered' : 'aiddata_tutorial not registered'
		);

		if ( $tutorial_pt ) {
			self::add_test(
				$category,
				'Tutorial: Public',
				$tutorial_pt->public === true,
				'Public: ' . ( $tutorial_pt->public ? 'Yes' : 'No' )
			);

			self::add_test(
				$category,
				'Tutorial: Show in Menu',
				$tutorial_pt->show_in_menu === true,
				'Show in menu: ' . ( $tutorial_pt->show_in_menu ? 'Yes' : 'No' )
			);

			self::add_test(
				$category,
				'Tutorial: REST API Enabled',
				$tutorial_pt->show_in_rest === true,
				'REST API: ' . ( $tutorial_pt->show_in_rest ? 'Enabled' : 'Disabled' )
			);

			self::add_test(
				$category,
				'Tutorial: Gutenberg Support',
				$tutorial_pt->show_in_rest === true,
				'Gutenberg: ' . ( $tutorial_pt->show_in_rest ? 'Enabled' : 'Disabled' )
			);
		}

		// Test quiz post type.
		$quiz_pt = get_post_type_object( 'aiddata_quiz' );
		self::add_test(
			$category,
			'Quiz Post Type Registered',
			$quiz_pt !== null,
			$quiz_pt ? 'aiddata_quiz registered' : 'aiddata_quiz not registered'
		);

		if ( $quiz_pt ) {
			self::add_test(
				$category,
				'Quiz: REST API Enabled',
				$quiz_pt->show_in_rest === true,
				'REST API: ' . ( $quiz_pt->show_in_rest ? 'Enabled' : 'Disabled' )
			);
		}

		// Test REST API endpoints.
		$rest_server = rest_get_server();
		$routes      = $rest_server->get_routes();

		self::add_test(
			$category,
			'Tutorial REST Endpoint Exists',
			isset( $routes['/wp/v2/tutorials'] ),
			isset( $routes['/wp/v2/tutorials'] ) ? '/wp/v2/tutorials available' : 'Tutorial endpoint missing'
		);

		self::add_test(
			$category,
			'Quiz REST Endpoint Exists',
			isset( $routes['/wp/v2/quizzes'] ),
			isset( $routes['/wp/v2/quizzes'] ) ? '/wp/v2/quizzes available' : 'Quiz endpoint missing'
		);

		// Test capabilities.
		$admin_role = get_role( 'administrator' );
		if ( $admin_role ) {
			$has_tutorial_cap = $admin_role->has_cap( 'edit_aiddata_tutorial' );
			self::add_test(
				$category,
				'Tutorial Capabilities Created',
				$has_tutorial_cap,
				$has_tutorial_cap ? 'Admin has tutorial capabilities' : 'Tutorial capabilities missing'
			);

			$has_quiz_cap = $admin_role->has_cap( 'edit_aiddata_quiz' );
			self::add_test(
				$category,
				'Quiz Capabilities Created',
				$has_quiz_cap,
				$has_quiz_cap ? 'Admin has quiz capabilities' : 'Quiz capabilities missing'
			);
		}

		// Test rewrite rules.
		global $wp_rewrite;
		$wp_rewrite->flush_rules( false );
		$rules = get_option( 'rewrite_rules' );
		$has_tutorial_rules = false;
		if ( $rules && is_array( $rules ) ) {
			foreach ( $rules as $pattern => $rewrite ) {
				if ( strpos( $pattern, 'tutorial' ) !== false ) {
					$has_tutorial_rules = true;
					break;
				}
			}
		}
		self::add_test(
			$category,
			'Tutorial Rewrite Rules Exist',
			$has_tutorial_rules,
			$has_tutorial_rules ? 'Tutorial rewrite rules configured' : 'Tutorial rewrite rules missing'
		);
	}

	/**
	 * Test taxonomies.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_taxonomies(): void {
		$category = 'Taxonomies';

		// Test tutorial category.
		$cat_tax = get_taxonomy( 'aiddata_tutorial_cat' );
		self::add_test(
			$category,
			'Tutorial Category Registered',
			$cat_tax !== false,
			$cat_tax ? 'aiddata_tutorial_cat registered' : 'aiddata_tutorial_cat not registered'
		);

		if ( $cat_tax ) {
			self::add_test(
				$category,
				'Category: Hierarchical',
				$cat_tax->hierarchical === true,
				'Hierarchical: ' . ( $cat_tax->hierarchical ? 'Yes' : 'No' )
			);

			self::add_test(
				$category,
				'Category: REST API Enabled',
				$cat_tax->show_in_rest === true,
				'REST API: ' . ( $cat_tax->show_in_rest ? 'Enabled' : 'Disabled' )
			);

			self::add_test(
				$category,
				'Category: Assigned to Tutorial',
				in_array( 'aiddata_tutorial', $cat_tax->object_type, true ),
				in_array( 'aiddata_tutorial', $cat_tax->object_type, true ) ? 'Assigned to tutorial CPT' : 'Not assigned to tutorial CPT'
			);
		}

		// Test tutorial tag.
		$tag_tax = get_taxonomy( 'aiddata_tutorial_tag' );
		self::add_test(
			$category,
			'Tutorial Tag Registered',
			$tag_tax !== false,
			$tag_tax ? 'aiddata_tutorial_tag registered' : 'aiddata_tutorial_tag not registered'
		);

		if ( $tag_tax ) {
			self::add_test(
				$category,
				'Tag: Non-Hierarchical',
				$tag_tax->hierarchical === false,
				'Hierarchical: ' . ( $tag_tax->hierarchical ? 'Yes (should be No)' : 'No (correct)' )
			);

			self::add_test(
				$category,
				'Tag: REST API Enabled',
				$tag_tax->show_in_rest === true,
				'REST API: ' . ( $tag_tax->show_in_rest ? 'Enabled' : 'Disabled' )
			);
		}

		// Test difficulty taxonomy.
		$diff_tax = get_taxonomy( 'aiddata_tutorial_difficulty' );
		self::add_test(
			$category,
			'Difficulty Registered',
			$diff_tax !== false,
			$diff_tax ? 'aiddata_tutorial_difficulty registered' : 'aiddata_tutorial_difficulty not registered'
		);

		if ( $diff_tax ) {
			self::add_test(
				$category,
				'Difficulty: Hierarchical',
				$diff_tax->hierarchical === true,
				'Hierarchical: ' . ( $diff_tax->hierarchical ? 'Yes' : 'No' )
			);

			self::add_test(
				$category,
				'Difficulty: REST API Enabled',
				$diff_tax->show_in_rest === true,
				'REST API: ' . ( $diff_tax->show_in_rest ? 'Enabled' : 'Disabled' )
			);
		}

		// Test default difficulty terms.
		$difficulty_terms = get_terms( array(
			'taxonomy'   => 'aiddata_tutorial_difficulty',
			'hide_empty' => false,
		) );
		$term_names = ! is_wp_error( $difficulty_terms ) ? wp_list_pluck( $difficulty_terms, 'name' ) : array();

		self::add_test(
			$category,
			'Default Difficulty Terms Created',
			count( $term_names ) >= 3,
			'Found ' . count( $term_names ) . ' difficulty terms (expected 3+)'
		);

		$required_terms = array( 'Beginner', 'Intermediate', 'Advanced' );
		foreach ( $required_terms as $term_name ) {
			self::add_test(
				$category,
				'Difficulty Term: ' . $term_name,
				in_array( $term_name, $term_names, true ),
				in_array( $term_name, $term_names, true ) ? $term_name . ' exists' : $term_name . ' missing'
			);
		}

		// Test REST API endpoints.
		$rest_server = rest_get_server();
		$routes      = $rest_server->get_routes();

		self::add_test(
			$category,
			'Category REST Endpoint',
			isset( $routes['/wp/v2/tutorial-categories'] ),
			isset( $routes['/wp/v2/tutorial-categories'] ) ? 'Endpoint available' : 'Endpoint missing'
		);

		self::add_test(
			$category,
			'Tag REST Endpoint',
			isset( $routes['/wp/v2/tutorial-tags'] ),
			isset( $routes['/wp/v2/tutorial-tags'] ) ? 'Endpoint available' : 'Endpoint missing'
		);

		self::add_test(
			$category,
			'Difficulty REST Endpoint',
			isset( $routes['/wp/v2/tutorial-difficulty'] ),
			isset( $routes['/wp/v2/tutorial-difficulty'] ) ? 'Endpoint available' : 'Endpoint missing'
		);
	}

	/**
	 * Test autoloader.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_autoloader(): void {
		$category = 'Autoloader';

		// Test autoloader class exists.
		self::add_test(
			$category,
			'Autoloader Class Exists',
			class_exists( 'AidData_LMS_Autoloader' ),
			class_exists( 'AidData_LMS_Autoloader' ) ? 'AidData_LMS_Autoloader loaded' : 'Autoloader class missing'
		);

		// Test autoloader registered.
		$functions = spl_autoload_functions();
		$registered = false;
		if ( is_array( $functions ) ) {
			foreach ( $functions as $function ) {
				if ( is_array( $function ) && $function[0] === 'AidData_LMS_Autoloader' ) {
					$registered = true;
					break;
				}
			}
		}
		self::add_test(
			$category,
			'Autoloader Registered',
			$registered,
			$registered ? 'Autoloader is registered with SPL' : 'Autoloader not registered'
		);

		// Test base class loading.
		$test_classes = array(
			'AidData_LMS_Test'          => 'Base test class',
			'AidData_LMS_Admin_Test'    => 'Admin test class',
			'AidData_LMS_Tutorial_Test' => 'Tutorial test class',
		);

		foreach ( $test_classes as $class => $description ) {
			self::add_test(
				$category,
				'Class Loading: ' . $class,
				class_exists( $class ),
				class_exists( $class ) ? $description . ' loaded' : $description . ' failed to load'
			);
		}

		// Test namespace handling.
		self::add_test(
			$category,
			'Nested Namespace Support',
			class_exists( 'AidData_LMS_Admin_Test' ),
			'Nested namespace loading functional'
		);
	}

	/**
	 * Test core classes.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_core_classes(): void {
		$category = 'Core Classes';

		// Test main class.
		self::add_test(
			$category,
			'Main Class Exists',
			class_exists( 'AidData_LMS' ),
			class_exists( 'AidData_LMS' ) ? 'AidData_LMS class loaded' : 'Main class missing'
		);

		// Test singleton pattern.
		if ( class_exists( 'AidData_LMS' ) ) {
			$instance1 = AidData_LMS::instance();
			$instance2 = AidData_LMS::instance();
			self::add_test(
				$category,
				'Singleton Pattern',
				$instance1 === $instance2,
				$instance1 === $instance2 ? 'Only one instance exists' : 'Multiple instances detected'
			);
		}

		// Test loader class.
		self::add_test(
			$category,
			'Loader Class Exists',
			class_exists( 'AidData_LMS_Loader' ),
			class_exists( 'AidData_LMS_Loader' ) ? 'Hook loader loaded' : 'Loader class missing'
		);

		// Test i18n class.
		self::add_test(
			$category,
			'i18n Class Exists',
			class_exists( 'AidData_LMS_i18n' ),
			class_exists( 'AidData_LMS_i18n' ) ? 'i18n class loaded' : 'i18n class missing'
		);

		// Test post types class.
		self::add_test(
			$category,
			'Post Types Class Exists',
			class_exists( 'AidData_LMS_Post_Types' ),
			class_exists( 'AidData_LMS_Post_Types' ) ? 'Post types class loaded' : 'Post types class missing'
		);

		// Test taxonomies class.
		self::add_test(
			$category,
			'Taxonomies Class Exists',
			class_exists( 'AidData_LMS_Taxonomies' ),
			class_exists( 'AidData_LMS_Taxonomies' ) ? 'Taxonomies class loaded' : 'Taxonomies class missing'
		);

		// Test install class.
		self::add_test(
			$category,
			'Install Class Exists',
			class_exists( 'AidData_LMS_Install' ),
			class_exists( 'AidData_LMS_Install' ) ? 'Install class loaded' : 'Install class missing'
		);

		// Test database class.
		self::add_test(
			$category,
			'Database Class Exists',
			class_exists( 'AidData_LMS_Database' ),
			class_exists( 'AidData_LMS_Database' ) ? 'Database helper loaded' : 'Database class missing'
		);
	}

	/**
	 * Test integration workflows.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_integration(): void {
		$category = 'Integration';

		// Create a test tutorial post.
		$post_id = wp_insert_post( array(
			'post_title'   => 'Phase 0 Validation Test Tutorial',
			'post_content' => 'This is a test tutorial for validation.',
			'post_status'  => 'draft',
			'post_type'    => 'aiddata_tutorial',
		), true );

		self::add_test(
			$category,
			'Create Tutorial Post',
			! is_wp_error( $post_id ) && $post_id > 0,
			! is_wp_error( $post_id ) && $post_id > 0 ? 'Tutorial created (ID: ' . $post_id . ')' : 'Failed to create tutorial'
		);

		if ( ! is_wp_error( $post_id ) && $post_id > 0 ) {
			// Assign category.
			$cat_term = wp_insert_term( 'Test Category', 'aiddata_tutorial_cat' );
			if ( ! is_wp_error( $cat_term ) ) {
				$assigned = wp_set_object_terms( $post_id, $cat_term['term_id'], 'aiddata_tutorial_cat' );
				self::add_test(
					$category,
					'Assign Category',
					! is_wp_error( $assigned ),
					! is_wp_error( $assigned ) ? 'Category assigned successfully' : 'Failed to assign category'
				);
			}

			// Assign tags.
			$tag_result = wp_set_object_terms( $post_id, array( 'test', 'validation' ), 'aiddata_tutorial_tag' );
			self::add_test(
				$category,
				'Assign Tags',
				! is_wp_error( $tag_result ),
				! is_wp_error( $tag_result ) ? 'Tags assigned successfully' : 'Failed to assign tags'
			);

			// Assign difficulty.
			$beginner = term_exists( 'Beginner', 'aiddata_tutorial_difficulty' );
			if ( $beginner ) {
				$diff_result = wp_set_object_terms( $post_id, (int) $beginner['term_id'], 'aiddata_tutorial_difficulty' );
				self::add_test(
					$category,
					'Assign Difficulty',
					! is_wp_error( $diff_result ),
					! is_wp_error( $diff_result ) ? 'Difficulty assigned successfully' : 'Failed to assign difficulty'
				);
			}

			// Retrieve post.
			$retrieved = get_post( $post_id );
			self::add_test(
				$category,
				'Retrieve Tutorial',
				$retrieved && $retrieved->post_type === 'aiddata_tutorial',
				$retrieved ? 'Tutorial retrieved successfully' : 'Failed to retrieve tutorial'
			);

			// Verify terms.
			$terms = wp_get_object_terms( $post_id, array( 'aiddata_tutorial_cat', 'aiddata_tutorial_tag', 'aiddata_tutorial_difficulty' ) );
			self::add_test(
				$category,
				'Verify Terms',
				! is_wp_error( $terms ) && count( $terms ) > 0,
				! is_wp_error( $terms ) ? 'Found ' . count( $terms ) . ' terms' : 'No terms found'
			);

			// Clean up - delete test post.
			$deleted = wp_delete_post( $post_id, true );
			self::add_test(
				$category,
				'Delete Tutorial',
				$deleted !== false && $deleted !== null,
				$deleted ? 'Tutorial deleted successfully' : 'Failed to delete tutorial'
			);

			// Clean up - delete test term.
			if ( ! is_wp_error( $cat_term ) && isset( $cat_term['term_id'] ) ) {
				wp_delete_term( $cat_term['term_id'], 'aiddata_tutorial_cat' );
			}
		}
	}

	/**
	 * Test security measures.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_security(): void {
		$category = 'Security';

		// Test ABSPATH checks in core files.
		$core_files = array(
			AIDDATA_LMS_PATH . 'includes/class-aiddata-lms.php',
			AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-install.php',
			AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-database.php',
			AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-post-types.php',
			AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-taxonomies.php',
		);

		foreach ( $core_files as $file ) {
			if ( file_exists( $file ) ) {
				$content = file_get_contents( $file );
				$has_abspath = strpos( $content, 'ABSPATH' ) !== false;
				self::add_test(
					$category,
					'ABSPATH Check: ' . basename( $file ),
					$has_abspath,
					$has_abspath ? 'ABSPATH check present' : 'ABSPATH check missing'
				);
			}
		}

		// Test SQL preparation.
		global $wpdb;
		$test_safe = $wpdb->prepare( 'SELECT * FROM %s WHERE id = %d', $wpdb->posts, 1 );
		self::add_test(
			$category,
			'SQL Preparation Available',
			$test_safe !== false,
			'$wpdb->prepare() functional'
		);

		// Test capability system.
		$admin = get_role( 'administrator' );
		self::add_test(
			$category,
			'Capability System',
			$admin !== null,
			$admin ? 'WordPress capability system functional' : 'Capability system issue'
		);

		// Test nonce functions.
		self::add_test(
			$category,
			'Nonce Functions Available',
			function_exists( 'wp_create_nonce' ) && function_exists( 'wp_verify_nonce' ),
			'WordPress nonce functions available'
		);

		// Test sanitization functions.
		self::add_test(
			$category,
			'Sanitization Functions',
			function_exists( 'sanitize_text_field' ) && function_exists( 'sanitize_email' ),
			'WordPress sanitization functions available'
		);

		// Test escaping functions.
		self::add_test(
			$category,
			'Escaping Functions',
			function_exists( 'esc_html' ) && function_exists( 'esc_url' ),
			'WordPress escaping functions available'
		);
	}

	/**
	 * Test performance metrics.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function test_performance(): void {
		$category = 'Performance';

		// Track query count.
		global $wpdb;
		$query_count_before = $wpdb->num_queries;

		// Simulate loading admin page.
		if ( post_type_exists( 'aiddata_tutorial' ) ) {
			$posts = get_posts( array(
				'post_type'      => 'aiddata_tutorial',
				'posts_per_page' => 10,
				'fields'         => 'ids',
			) );
		}

		$query_count_after = $wpdb->num_queries;
		$admin_queries     = $query_count_after - $query_count_before;

		self::$metrics['admin_queries'] = $admin_queries;
		self::add_test(
			$category,
			'Admin Page Queries < 20',
			$admin_queries < 20,
			'Admin page: ' . $admin_queries . ' queries' . ( $admin_queries < 20 ? ' (good)' : ' (high)' )
		);

		// Test memory usage.
		$memory_used = memory_get_usage( true );
		$memory_mb   = round( $memory_used / 1024 / 1024, 2 );

		self::$metrics['memory_usage_mb'] = $memory_mb;
		self::add_test(
			$category,
			'Memory Usage < 64MB',
			$memory_mb < 64,
			'Memory: ' . $memory_mb . 'MB' . ( $memory_mb < 64 ? ' (good)' : ' (high)' )
		);

		// Test class loading time.
		$start = microtime( true );
		if ( class_exists( 'AidData_LMS_Test' ) ) {
			new AidData_LMS_Test();
		}
		$load_time = ( microtime( true ) - $start ) * 1000; // Convert to ms.

		self::$metrics['class_load_time_ms'] = $load_time;
		self::add_test(
			$category,
			'Class Load Time < 10ms',
			$load_time < 10,
			'Load time: ' . round( $load_time, 2 ) . 'ms' . ( $load_time < 10 ? ' (fast)' : ' (slow)' )
		);

		// Test database query performance.
		$start = microtime( true );
		global $wpdb;
		$wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE post_type = 'aiddata_tutorial' LIMIT 10" );
		$query_time = ( microtime( true ) - $start ) * 1000;

		self::$metrics['db_query_time_ms'] = $query_time;
		self::add_test(
			$category,
			'DB Query Time < 50ms',
			$query_time < 50,
			'Query time: ' . round( $query_time, 2 ) . 'ms' . ( $query_time < 50 ? ' (fast)' : ' (slow)' )
		);
	}

	/**
	 * Evaluate Phase 0 exit criteria.
	 *
	 * @since 2.0.0
	 * @return array Exit criteria evaluation.
	 */
	private static function evaluate_exit_criteria(): array {
		$criteria = array(
			'environment_ready'       => true,
			'database_complete'       => true,
			'post_types_functional'   => true,
			'taxonomies_functional'   => true,
			'autoloader_working'      => true,
			'core_classes_loaded'     => true,
			'integration_successful'  => true,
			'security_validated'      => true,
			'performance_acceptable'  => true,
			'all_tests_passed'        => self::$counters['failed'] === 0,
			'ready_for_phase_1'       => false,
		);

		// Check each category has no failures.
		foreach ( self::$results['tests'] as $category => $tests ) {
			foreach ( $tests as $test ) {
				if ( ! $test['passed'] ) {
					switch ( $category ) {
						case 'Environment':
							$criteria['environment_ready'] = false;
							break;
						case 'Database Schema':
							$criteria['database_complete'] = false;
							break;
						case 'Post Types':
							$criteria['post_types_functional'] = false;
							break;
						case 'Taxonomies':
							$criteria['taxonomies_functional'] = false;
							break;
						case 'Autoloader':
							$criteria['autoloader_working'] = false;
							break;
						case 'Core Classes':
							$criteria['core_classes_loaded'] = false;
							break;
						case 'Integration':
							$criteria['integration_successful'] = false;
							break;
						case 'Security':
							$criteria['security_validated'] = false;
							break;
						case 'Performance':
							$criteria['performance_acceptable'] = false;
							break;
					}
				}
			}
		}

		// Overall readiness.
		$criteria['ready_for_phase_1'] = $criteria['environment_ready']
			&& $criteria['database_complete']
			&& $criteria['post_types_functional']
			&& $criteria['taxonomies_functional']
			&& $criteria['autoloader_working']
			&& $criteria['core_classes_loaded']
			&& $criteria['integration_successful']
			&& $criteria['security_validated']
			&& $criteria['performance_acceptable']
			&& $criteria['all_tests_passed'];

		return $criteria;
	}

	/**
	 * Generate comprehensive validation report.
	 *
	 * @since 2.0.0
	 * @return string Formatted report.
	 */
	public static function generate_report(): string {
		$results = self::$results;

		$report = "# PHASE 0 VALIDATION REPORT\n\n";
		$report .= "**Generated:** " . $results['timestamp'] . "\n";
		$report .= "**PHP Version:** " . $results['php_version'] . "\n";
		$report .= "**WordPress Version:** " . $results['wp_version'] . "\n";
		$report .= "**Test Duration:** " . round( $results['duration'], 2 ) . " seconds\n\n";

		$report .= "## Summary\n\n";
		$report .= "- **Total Tests:** " . $results['summary']['total'] . "\n";
		$report .= "- **Passed:** " . $results['summary']['passed'] . "\n";
		$report .= "- **Failed:** " . $results['summary']['failed'] . "\n";
		$report .= "- **Skipped:** " . $results['summary']['skipped'] . "\n";
		$report .= "- **Overall:** " . ( $results['passed'] ? '✅ PASS' : '❌ FAIL' ) . "\n\n";

		$report .= "## Exit Criteria\n\n";
		foreach ( $results['phase_0_exit_criteria'] as $criterion => $met ) {
			$status = $met ? '✅' : '❌';
			$label  = ucwords( str_replace( '_', ' ', $criterion ) );
			$report .= "- {$status} {$label}\n";
		}
		$report .= "\n";

		$report .= "## Performance Metrics\n\n";
		foreach ( $results['metrics'] as $metric => $value ) {
			$label = ucwords( str_replace( '_', ' ', $metric ) );
			$report .= "- **{$label}:** {$value}\n";
		}
		$report .= "\n";

		$report .= "## Detailed Test Results\n\n";
		foreach ( $results['tests'] as $category => $tests ) {
			$report .= "### {$category}\n\n";
			foreach ( $tests as $test ) {
				$status = $test['passed'] ? '✅' : '❌';
				$report .= "- {$status} **{$test['name']}**: {$test['message']}\n";
			}
			$report .= "\n";
		}

		return $report;
	}

	/**
	 * Add a test result.
	 *
	 * @since 2.0.0
	 * @param string $category Test category.
	 * @param string $name Test name.
	 * @param bool   $passed Whether test passed.
	 * @param string $message Result message.
	 * @return void
	 */
	private static function add_test( string $category, string $name, bool $passed, string $message ): void {
		if ( ! isset( self::$results['tests'][ $category ] ) ) {
			self::$results['tests'][ $category ] = array();
		}

		self::$results['tests'][ $category ][] = array(
			'name'    => $name,
			'passed'  => $passed,
			'message' => $message,
		);

		self::$counters['total']++;
		if ( $passed ) {
			self::$counters['passed']++;
		} else {
			self::$counters['failed']++;
		}
	}

	/**
	 * Parse memory limit string to bytes.
	 *
	 * @since 2.0.0
	 * @param string $limit Memory limit string (e.g., "128M", "1G").
	 * @return int Memory limit in bytes, or -1 if unlimited.
	 */
	private static function parse_memory_limit( string $limit ): int {
		if ( $limit === '-1' ) {
			return -1;
		}

		$limit = trim( $limit );
		$last  = strtolower( $limit[ strlen( $limit ) - 1 ] );
		$value = (int) $limit;

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
}

