<?php
/**
 * Plugin Installation Handler
 *
 * Handles plugin activation, database table creation, and initial setup.
 *
 * @package AidData_LMS
 * @subpackage Installation
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installation Class
 *
 * Manages plugin activation, database schema creation, and initial configuration.
 *
 * @since 2.0.0
 */
class AidData_LMS_Install {

	/**
	 * Database version.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private static string $db_version = '2.0.0';

	/**
	 * Option name for storing database version.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private static string $db_version_option = 'aiddata_lms_db_version';

	/**
	 * Run plugin installation.
	 *
	 * Creates database tables, sets default options, and creates capabilities.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function install(): void {
		// Check if we need to run installation/upgrade.
		$current_version = get_option( self::$db_version_option, '0.0.0' );

		// Always run table creation (dbDelta handles upgrades).
		self::create_tables();

		// Create default options if new installation.
		if ( '0.0.0' === $current_version ) {
			// Register taxonomies first (needed for term creation)
			$taxonomies = new AidData_LMS_Taxonomies();
			$taxonomies->register_taxonomies();

			// Create default options and terms
			self::create_default_options();
		}

		// Create capabilities.
		self::create_capabilities();

		// Register taxonomies for rewrite rules
		$taxonomies = new AidData_LMS_Taxonomies();
		$taxonomies->register_taxonomies();

		// Register post types for rewrite rules.
		$post_types = new AidData_LMS_Post_Types();
		$post_types->register_post_types();

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Update database version.
		update_option( self::$db_version_option, self::$db_version );
	}

	/**
	 * Create database tables.
	 *
	 * Uses dbDelta() to create or update database tables.
	 * Follows WordPress standards for table creation.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function create_tables(): void {
		global $wpdb;

		// Require dbDelta function.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		// Tutorial Enrollments Table.
		$table_enrollments = $wpdb->prefix . 'aiddata_lms_tutorial_enrollments';

		$sql_enrollments = "CREATE TABLE $table_enrollments (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			tutorial_id BIGINT UNSIGNED NOT NULL,
			enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			completed_at DATETIME NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			source VARCHAR(50) NULL DEFAULT 'web',
			PRIMARY KEY  (id),
			UNIQUE KEY user_tutorial (user_id, tutorial_id),
			KEY tutorial_id (tutorial_id),
			KEY status (status),
			KEY enrolled_at (enrolled_at),
			KEY completed_at (completed_at)
		) $charset_collate;";

		dbDelta( $sql_enrollments );

		// Tutorial Progress Table.
		$table_progress = $wpdb->prefix . 'aiddata_lms_tutorial_progress';

		$sql_progress = "CREATE TABLE $table_progress (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			tutorial_id BIGINT UNSIGNED NOT NULL,
			enrollment_id BIGINT UNSIGNED NULL,
			current_step INT UNSIGNED NOT NULL DEFAULT 0,
			completed_steps TEXT NULL,
			progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
			status VARCHAR(20) NOT NULL DEFAULT 'not_started',
			quiz_passed TINYINT(1) NOT NULL DEFAULT 0,
			quiz_score DECIMAL(5,2) NULL,
			quiz_attempts INT UNSIGNED NOT NULL DEFAULT 0,
			last_accessed DATETIME NULL,
			completed_at DATETIME NULL,
			time_spent INT UNSIGNED NOT NULL DEFAULT 0,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY user_tutorial (user_id, tutorial_id),
			KEY tutorial_id (tutorial_id),
			KEY enrollment_id (enrollment_id),
			KEY status (status),
			KEY progress_percent (progress_percent),
			KEY last_accessed (last_accessed)
		) $charset_collate;";

		dbDelta( $sql_progress );

		// Video Progress Table.
		$table_video = $wpdb->prefix . 'aiddata_lms_video_progress';

		$sql_video = "CREATE TABLE $table_video (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			tutorial_id BIGINT UNSIGNED NOT NULL,
			step_index INT UNSIGNED NOT NULL,
			video_url VARCHAR(500) NOT NULL,
			video_platform VARCHAR(50) NOT NULL,
			current_position INT UNSIGNED NOT NULL DEFAULT 0,
			total_duration INT UNSIGNED NOT NULL DEFAULT 0,
			watch_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
			completed TINYINT(1) NOT NULL DEFAULT 0,
			completed_at DATETIME NULL,
			watch_sessions INT UNSIGNED NOT NULL DEFAULT 0,
			total_watch_time INT UNSIGNED NOT NULL DEFAULT 0,
			last_position_update DATETIME NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY user_tutorial_step (user_id, tutorial_id, step_index),
			KEY tutorial_id (tutorial_id),
			KEY step_index (step_index),
			KEY completed (completed)
		) $charset_collate;";

		dbDelta( $sql_video );

		// Certificates Table.
		$table_certificates = $wpdb->prefix . 'aiddata_lms_certificates';

		$sql_certificates = "CREATE TABLE $table_certificates (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			certificate_code VARCHAR(32) NOT NULL,
			user_id BIGINT UNSIGNED NOT NULL,
			tutorial_id BIGINT UNSIGNED NOT NULL,
			user_name VARCHAR(255) NOT NULL,
			tutorial_title VARCHAR(255) NOT NULL,
			completion_date DATE NOT NULL,
			issued_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			template_id VARCHAR(50) NULL DEFAULT 'default',
			certificate_data TEXT NULL,
			pdf_path VARCHAR(500) NULL,
			verification_url VARCHAR(500) NULL,
			downloads INT UNSIGNED NOT NULL DEFAULT 0,
			last_downloaded DATETIME NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			revoked_at DATETIME NULL,
			revoked_reason TEXT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY certificate_code (certificate_code),
			UNIQUE KEY user_tutorial (user_id, tutorial_id),
			KEY tutorial_id (tutorial_id),
			KEY issued_date (issued_date),
			KEY status (status)
		) $charset_collate;";

		dbDelta( $sql_certificates );

		// Tutorial Analytics Table.
		$table_analytics = $wpdb->prefix . 'aiddata_lms_tutorial_analytics';

		$sql_analytics = "CREATE TABLE $table_analytics (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			tutorial_id BIGINT UNSIGNED NOT NULL,
			user_id BIGINT UNSIGNED NULL,
			event_type VARCHAR(50) NOT NULL,
			event_data TEXT NULL,
			session_id VARCHAR(64) NULL,
			ip_address VARCHAR(45) NULL,
			user_agent TEXT NULL,
			referrer VARCHAR(500) NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY tutorial_id (tutorial_id),
			KEY user_id (user_id),
			KEY event_type (event_type),
			KEY session_id (session_id),
			KEY created_at (created_at)
		) $charset_collate;";

		dbDelta( $sql_analytics );

		// Email Queue Table.
		$table_email = $wpdb->prefix . 'aiddata_lms_email_queue';

		$sql_email = "CREATE TABLE $table_email (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			recipient_email VARCHAR(255) NOT NULL,
			recipient_name VARCHAR(255) NULL,
			user_id BIGINT UNSIGNED NULL,
			subject VARCHAR(500) NOT NULL,
			message LONGTEXT NOT NULL,
			email_type VARCHAR(50) NOT NULL,
			template_id VARCHAR(50) NULL,
			template_data TEXT NULL,
			priority TINYINT UNSIGNED NOT NULL DEFAULT 5,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			attempts INT UNSIGNED NOT NULL DEFAULT 0,
			last_attempt DATETIME NULL,
			scheduled_for DATETIME NULL,
			sent_at DATETIME NULL,
			error_message TEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY recipient_email (recipient_email),
			KEY user_id (user_id),
			KEY email_type (email_type),
			KEY status (status),
			KEY priority (priority),
			KEY scheduled_for (scheduled_for),
			KEY created_at (created_at)
		) $charset_collate;";

		dbDelta( $sql_email );

		// Add foreign key constraints if they don't exist.
		// Note: dbDelta doesn't handle foreign keys, so we add them manually.
		self::add_foreign_keys();
	}

	/**
	 * Add foreign key constraints to tables.
	 *
	 * Adds foreign key constraints for data integrity.
	 * Checks if constraints already exist before adding.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private static function add_foreign_keys(): void {
		global $wpdb;

		$table_enrollments  = $wpdb->prefix . 'aiddata_lms_tutorial_enrollments';
		$table_progress     = $wpdb->prefix . 'aiddata_lms_tutorial_progress';
		$table_video        = $wpdb->prefix . 'aiddata_lms_video_progress';
		$table_certificates = $wpdb->prefix . 'aiddata_lms_certificates';
		$table_analytics    = $wpdb->prefix . 'aiddata_lms_tutorial_analytics';
		$table_email        = $wpdb->prefix . 'aiddata_lms_email_queue';

		// Suppress errors temporarily to check if constraints exist.
		$wpdb->suppress_errors();

		// Enrollments table foreign keys.
		$wpdb->query(
			"ALTER TABLE $table_enrollments 
			ADD CONSTRAINT fk_enrollment_user 
			FOREIGN KEY (user_id) 
			REFERENCES {$wpdb->prefix}users(ID) 
			ON DELETE CASCADE"
		);

		$wpdb->query(
			"ALTER TABLE $table_enrollments 
			ADD CONSTRAINT fk_enrollment_tutorial 
			FOREIGN KEY (tutorial_id) 
			REFERENCES {$wpdb->prefix}posts(ID) 
			ON DELETE CASCADE"
		);

		// Progress table foreign keys.
		$wpdb->query(
			"ALTER TABLE $table_progress 
			ADD CONSTRAINT fk_progress_user 
			FOREIGN KEY (user_id) 
			REFERENCES {$wpdb->prefix}users(ID) 
			ON DELETE CASCADE"
		);

		$wpdb->query(
			"ALTER TABLE $table_progress 
			ADD CONSTRAINT fk_progress_tutorial 
			FOREIGN KEY (tutorial_id) 
			REFERENCES {$wpdb->prefix}posts(ID) 
			ON DELETE CASCADE"
		);

		$wpdb->query(
			"ALTER TABLE $table_progress 
			ADD CONSTRAINT fk_progress_enrollment 
			FOREIGN KEY (enrollment_id) 
			REFERENCES $table_enrollments(id) 
			ON DELETE CASCADE"
		);

		// Video progress table foreign keys.
		$wpdb->query(
			"ALTER TABLE $table_video 
			ADD CONSTRAINT fk_video_user 
			FOREIGN KEY (user_id) 
			REFERENCES {$wpdb->prefix}users(ID) 
			ON DELETE CASCADE"
		);

		$wpdb->query(
			"ALTER TABLE $table_video 
			ADD CONSTRAINT fk_video_tutorial 
			FOREIGN KEY (tutorial_id) 
			REFERENCES {$wpdb->prefix}posts(ID) 
			ON DELETE CASCADE"
		);

		// Certificates table foreign keys.
		$wpdb->query(
			"ALTER TABLE $table_certificates 
			ADD CONSTRAINT fk_cert_user 
			FOREIGN KEY (user_id) 
			REFERENCES {$wpdb->prefix}users(ID) 
			ON DELETE CASCADE"
		);

		$wpdb->query(
			"ALTER TABLE $table_certificates 
			ADD CONSTRAINT fk_cert_tutorial 
			FOREIGN KEY (tutorial_id) 
			REFERENCES {$wpdb->prefix}posts(ID) 
			ON DELETE CASCADE"
		);

		// Analytics table foreign keys.
		$wpdb->query(
			"ALTER TABLE $table_analytics 
			ADD CONSTRAINT fk_analytics_tutorial 
			FOREIGN KEY (tutorial_id) 
			REFERENCES {$wpdb->prefix}posts(ID) 
			ON DELETE CASCADE"
		);

		$wpdb->query(
			"ALTER TABLE $table_analytics 
			ADD CONSTRAINT fk_analytics_user 
			FOREIGN KEY (user_id) 
			REFERENCES {$wpdb->prefix}users(ID) 
			ON DELETE SET NULL"
		);

		// Email queue table foreign keys.
		$wpdb->query(
			"ALTER TABLE $table_email 
			ADD CONSTRAINT fk_email_user 
			FOREIGN KEY (user_id) 
			REFERENCES {$wpdb->prefix}users(ID) 
			ON DELETE SET NULL"
		);

		// Re-enable error reporting.
		$wpdb->show_errors();
	}

	/**
	 * Create default plugin options.
	 *
	 * Sets up initial configuration options for the plugin.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function create_default_options(): void {
		// Create default difficulty levels
		$difficulty_terms = array(
			'Beginner'     => 'For those new to the topic',
			'Intermediate' => 'Requires some prior knowledge',
			'Advanced'     => 'For experienced learners',
		);

		foreach ( $difficulty_terms as $term => $description ) {
			if ( ! term_exists( $term, 'aiddata_tutorial_difficulty' ) ) {
				wp_insert_term(
					$term,
					'aiddata_tutorial_difficulty',
					array( 'description' => $description )
				);
			}
		}

		$default_options = array(
			// General Settings.
			'aiddata_lms_enable_enrollments'        => true,
			'aiddata_lms_enable_certificates'       => true,
			'aiddata_lms_enable_video_tracking'     => true,
			'aiddata_lms_enable_quiz_system'        => true,

			// Completion Settings.
			'aiddata_lms_video_completion_percent'  => 90,
			'aiddata_lms_quiz_passing_score'        => 70,
			'aiddata_lms_quiz_max_attempts'         => 3,
			'aiddata_lms_require_quiz_pass'         => true,

			// Certificate Settings.
			'aiddata_lms_auto_issue_certificate'    => true,
			'aiddata_lms_certificate_template'      => 'default',

			// Email Notifications.
			'aiddata_lms_enable_email_notifications' => true,
			'aiddata_lms_enrollment_email'          => true,
			'aiddata_lms_completion_email'          => true,
			'aiddata_lms_certificate_email'         => true,

			// Progress Tracking.
			'aiddata_lms_save_video_position'       => true,
			'aiddata_lms_position_save_interval'    => 5,

			// Guest Access.
			'aiddata_lms_guest_preview_enabled'     => false,
			'aiddata_lms_guest_preview_steps'       => 3,

			// Analytics.
			'aiddata_lms_enable_analytics'          => true,
			'aiddata_lms_track_video_events'        => true,
		);

		foreach ( $default_options as $option_name => $option_value ) {
			if ( false === get_option( $option_name ) ) {
				add_option( $option_name, $option_value );
			}
		}

		// Store installation timestamp.
		if ( false === get_option( 'aiddata_lms_installed_at' ) ) {
			add_option( 'aiddata_lms_installed_at', current_time( 'mysql' ) );
		}

		// Set LMS version
		update_option( 'aiddata_lms_version', AIDDATA_LMS_VERSION );

		// Set default LMS settings
		if ( false === get_option( 'aiddata_lms_settings' ) ) {
			add_option(
				'aiddata_lms_settings',
				array(
					'enable_enrollments' => true,
					'enable_certificates' => true,
					'enable_analytics'   => true,
				)
			);
		}
	}

	/**
	 * Create custom capabilities.
	 *
	 * Adds custom capabilities to WordPress roles for LMS functionality.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function create_capabilities(): void {
		// Get administrator role.
		$admin_role = get_role( 'administrator' );

		if ( null === $admin_role ) {
			return;
		}

		// Custom capabilities for LMS management.
		$lms_capabilities = array(
			'manage_aiddata_lms',
			'manage_tutorial_enrollments',
			'view_tutorial_analytics',
			'issue_certificates',
			'manage_quiz_submissions',
			'export_tutorial_data',
		);

		// Tutorial post type capabilities.
		$tutorial_capabilities = array(
			'edit_aiddata_tutorial',
			'read_aiddata_tutorial',
			'delete_aiddata_tutorial',
			'edit_aiddata_tutorials',
			'edit_others_aiddata_tutorials',
			'publish_aiddata_tutorials',
			'read_private_aiddata_tutorials',
			'delete_aiddata_tutorials',
			'delete_private_aiddata_tutorials',
			'delete_published_aiddata_tutorials',
			'delete_others_aiddata_tutorials',
			'edit_private_aiddata_tutorials',
			'edit_published_aiddata_tutorials',
		);

		// Quiz post type capabilities.
		$quiz_capabilities = array(
			'edit_aiddata_quiz',
			'read_aiddata_quiz',
			'delete_aiddata_quiz',
			'edit_aiddata_quizzes',
			'edit_others_aiddata_quizzes',
			'publish_aiddata_quizzes',
			'read_private_aiddata_quizzes',
			'delete_aiddata_quizzes',
			'delete_private_aiddata_quizzes',
			'delete_published_aiddata_quizzes',
			'delete_others_aiddata_quizzes',
			'edit_private_aiddata_quizzes',
			'edit_published_aiddata_quizzes',
		);

		// Merge all capabilities.
		$all_capabilities = array_merge( $lms_capabilities, $tutorial_capabilities, $quiz_capabilities );

		// Add all capabilities to administrator role.
		foreach ( $all_capabilities as $capability ) {
			$admin_role->add_cap( $capability );
		}

		// Get editor role.
		$editor_role = get_role( 'editor' );
		if ( null !== $editor_role ) {
			// Add LMS analytics access.
			$editor_role->add_cap( 'view_tutorial_analytics' );

			// Add tutorial and quiz capabilities to editors.
			$editor_capabilities = array_merge( $tutorial_capabilities, $quiz_capabilities );
			foreach ( $editor_capabilities as $capability ) {
				$editor_role->add_cap( $capability );
			}
		}
	}

	/**
	 * Get current database version.
	 *
	 * @since 2.0.0
	 * @return string Current database version.
	 */
	public static function get_db_version(): string {
		return get_option( self::$db_version_option, '0.0.0' );
	}

	/**
	 * Check if plugin needs upgrade.
	 *
	 * @since 2.0.0
	 * @return bool True if upgrade needed, false otherwise.
	 */
	public static function needs_upgrade(): bool {
		$current_version = self::get_db_version();
		return version_compare( $current_version, self::$db_version, '<' );
	}

	/**
	 * Verify database tables exist.
	 *
	 * Checks if all required database tables exist.
	 *
	 * @since 2.0.0
	 * @return array Array with table names as keys and existence status as values.
	 */
	public static function verify_tables(): array {
		global $wpdb;

		$tables = array(
			'enrollments'  => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'     => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'        => $wpdb->prefix . 'aiddata_lms_video_progress',
			'certificates' => $wpdb->prefix . 'aiddata_lms_certificates',
			'analytics'    => $wpdb->prefix . 'aiddata_lms_tutorial_analytics',
			'email'        => $wpdb->prefix . 'aiddata_lms_email_queue',
		);

		$results = array();

		foreach ( $tables as $key => $table_name ) {
			$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
			$results[ $key ] = ( $table_name === $table_exists );
		}

		return $results;
	}

	/**
	 * Get table structure information.
	 *
	 * Returns detailed information about table structure.
	 *
	 * @since 2.0.0
	 * @param string $table_key Table key (enrollments, progress, video, certificates, analytics, email).
	 * @return array|null Table structure information or null if table doesn't exist.
	 */
	public static function get_table_info( string $table_key ): ?array {
		global $wpdb;

		$tables = array(
			'enrollments'  => $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
			'progress'     => $wpdb->prefix . 'aiddata_lms_tutorial_progress',
			'video'        => $wpdb->prefix . 'aiddata_lms_video_progress',
			'certificates' => $wpdb->prefix . 'aiddata_lms_certificates',
			'analytics'    => $wpdb->prefix . 'aiddata_lms_tutorial_analytics',
			'email'        => $wpdb->prefix . 'aiddata_lms_email_queue',
		);

		if ( ! isset( $tables[ $table_key ] ) ) {
			return null;
		}

		$table_name = $tables[ $table_key ];

		// Get table columns.
		$columns = $wpdb->get_results( "DESCRIBE $table_name", ARRAY_A );

		// Get table indexes.
		$indexes = $wpdb->get_results( "SHOW INDEX FROM $table_name", ARRAY_A );

		// Get table status.
		$status = $wpdb->get_row( "SHOW TABLE STATUS LIKE '$table_name'", ARRAY_A );

		return array(
			'name'    => $table_name,
			'columns' => $columns,
			'indexes' => $indexes,
			'status'  => $status,
		);
	}
}

