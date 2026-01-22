<?php
/**
 * Analytics Tracking System
 *
 * Handles event tracking, session management, and analytics queries with privacy compliance.
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Analytics
 *
 * Manages analytics tracking for tutorials including events, sessions, and user activity.
 * Implements privacy-compliant tracking with IP hashing and session management.
 *
 * @since 1.0.0
 */
class AidData_LMS_Analytics {

	/**
	 * Analytics table name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name;

	/**
	 * Constructor.
	 *
	 * Initializes the analytics manager and registers event hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = AidData_LMS_Database::get_table_name( 'analytics' );

		// Register hooks for automatic tracking.
		$this->register_hooks();
	}

	/**
	 * Register hooks for automatic event tracking.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks(): void {
		// Track enrollment events.
		add_action( 'aiddata_lms_user_enrolled', array( $this, 'track_enrollment' ), 10, 4 );

		// Track progress events.
		add_action( 'aiddata_lms_step_completed', array( $this, 'track_step_completion' ), 10, 3 );

		// Track tutorial view.
		add_action( 'template_redirect', array( $this, 'track_tutorial_view' ) );

		// Track tutorial completion.
		add_action( 'aiddata_lms_tutorial_completed', array( $this, 'track_tutorial_completion' ), 10, 3 );
	}

	/**
	 * Track an event in the analytics system.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $tutorial_id Tutorial ID.
	 * @param string $event_type  Event type identifier.
	 * @param array  $event_data  Optional event data as associative array.
	 * @param int|null $user_id   Optional user ID. If null, uses current user or null for guest.
	 * @return int|WP_Error Event ID on success, WP_Error on failure.
	 */
	public function track_event( int $tutorial_id, string $event_type, array $event_data = array(), ?int $user_id = null ) {
		global $wpdb;

		// Validate tutorial exists.
		if ( ! get_post( $tutorial_id ) ) {
			return new WP_Error( 'invalid_tutorial', __( 'Tutorial not found.', 'aiddata-lms' ) );
		}

		// Get user ID if not provided.
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
			$user_id = $user_id > 0 ? $user_id : null;
		}

		// Get session ID.
		$session_id = $this->get_session_id();

		// Get IP address (hashed for privacy).
		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$ip_hash    = $this->hash_ip_address( $ip_address );

		// Get user agent.
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		// Get referrer.
		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';

		// Prepare data.
		$data = array(
			'tutorial_id' => $tutorial_id,
			'user_id'     => $user_id,
			'event_type'  => sanitize_key( $event_type ),
			'event_data'  => ! empty( $event_data ) ? wp_json_encode( $event_data ) : null,
			'session_id'  => $session_id,
			'ip_address'  => $ip_hash,
			'user_agent'  => substr( $user_agent, 0, 500 ),
			'referrer'    => substr( $referrer, 0, 500 ),
		);

		$format = array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' );

		// Insert event.
		$result = $wpdb->insert( $this->table_name, $data, $format );

		if ( false === $result ) {
			error_log( sprintf( 'Analytics tracking error: %s', $wpdb->last_error ) );
			return new WP_Error( 'db_error', __( 'Failed to track event.', 'aiddata-lms' ), $wpdb->last_error );
		}

		$event_id = $wpdb->insert_id;

		// Fire action for extensions.
		do_action( 'aiddata_lms_event_tracked', $event_id, $event_type, $tutorial_id, $user_id );

		return $event_id;
	}

	/**
	 * Get analytics data for a specific tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $tutorial_id Tutorial ID.
	 * @param array $date_range  Optional date range with 'start' and 'end' keys.
	 * @return array Analytics data including event counts, unique users, and sessions.
	 */
	public function get_tutorial_analytics( int $tutorial_id, array $date_range = array() ): array {
		global $wpdb;

		// Parse date range.
		$where_date = $this->build_date_where_clause( $date_range );

		// Get event counts by type.
		$event_counts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT event_type, COUNT(*) as count
				 FROM {$this->table_name}
				 WHERE tutorial_id = %d
				 $where_date
				 GROUP BY event_type
				 ORDER BY count DESC",
				$tutorial_id
			),
			ARRAY_A
		);

		// Get unique users.
		$unique_users = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT user_id)
				 FROM {$this->table_name}
				 WHERE tutorial_id = %d
				 AND user_id IS NOT NULL
				 $where_date",
				$tutorial_id
			)
		);

		// Get unique sessions.
		$unique_sessions = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT session_id)
				 FROM {$this->table_name}
				 WHERE tutorial_id = %d
				 $where_date",
				$tutorial_id
			)
		);

		return array(
			'tutorial_id'     => $tutorial_id,
			'event_counts'    => $event_counts ?: array(),
			'unique_users'    => (int) $unique_users,
			'unique_sessions' => (int) $unique_sessions,
			'date_range'      => $date_range,
		);
	}

	/**
	 * Get analytics data for a specific user.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $user_id    User ID.
	 * @param array $date_range Optional date range with 'start' and 'end' keys.
	 * @return array Analytics data including event counts and tutorial activity.
	 */
	public function get_user_analytics( int $user_id, array $date_range = array() ): array {
		global $wpdb;

		// Parse date range.
		$where_date = $this->build_date_where_clause( $date_range );

		// Total events.
		$total_events = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} WHERE user_id = %d $where_date",
				$user_id
			)
		);

		// Unique tutorials accessed.
		$unique_tutorials = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT tutorial_id) FROM {$this->table_name} WHERE user_id = %d $where_date",
				$user_id
			)
		);

		// Event counts by type.
		$event_counts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT event_type, COUNT(*) as count
				 FROM {$this->table_name}
				 WHERE user_id = %d
				 $where_date
				 GROUP BY event_type
				 ORDER BY count DESC",
				$user_id
			),
			ARRAY_A
		);

		// Tutorial activity.
		$tutorial_activity = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT tutorial_id, COUNT(*) as event_count, MAX(created_at) as last_activity
				 FROM {$this->table_name}
				 WHERE user_id = %d
				 $where_date
				 GROUP BY tutorial_id
				 ORDER BY last_activity DESC",
				$user_id
			),
			ARRAY_A
		);

		return array(
			'user_id'           => $user_id,
			'total_events'      => (int) $total_events,
			'unique_tutorials'  => (int) $unique_tutorials,
			'event_counts'      => $event_counts ?: array(),
			'tutorial_activity' => $tutorial_activity ?: array(),
			'date_range'        => $date_range,
		);
	}

	/**
	 * Get platform-wide analytics.
	 *
	 * @since 1.0.0
	 *
	 * @param array $date_range Optional date range with 'start' and 'end' keys.
	 * @return array Platform analytics data.
	 */
	public function get_platform_analytics( array $date_range = array() ): array {
		global $wpdb;

		$where_date = $this->build_date_where_clause( $date_range );

		// Total events.
		$total_events = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->table_name} WHERE 1=1 $where_date"
		);

		// Unique users.
		$unique_users = $wpdb->get_var(
			"SELECT COUNT(DISTINCT user_id) FROM {$this->table_name} WHERE user_id IS NOT NULL $where_date"
		);

		// Unique tutorials accessed.
		$unique_tutorials = $wpdb->get_var(
			"SELECT COUNT(DISTINCT tutorial_id) FROM {$this->table_name} WHERE 1=1 $where_date"
		);

		// Top events.
		$top_events = $wpdb->get_results(
			"SELECT event_type, COUNT(*) as count
			 FROM {$this->table_name}
			 WHERE 1=1 $where_date
			 GROUP BY event_type
			 ORDER BY count DESC
			 LIMIT 10",
			ARRAY_A
		);

		// Top tutorials.
		$top_tutorials = $wpdb->get_results(
			"SELECT tutorial_id, COUNT(*) as event_count, COUNT(DISTINCT user_id) as user_count
			 FROM {$this->table_name}
			 WHERE 1=1 $where_date
			 GROUP BY tutorial_id
			 ORDER BY event_count DESC
			 LIMIT 10",
			ARRAY_A
		);

		return array(
			'total_events'     => (int) $total_events,
			'unique_users'     => (int) $unique_users,
			'unique_tutorials' => (int) $unique_tutorials,
			'top_events'       => $top_events ?: array(),
			'top_tutorials'    => $top_tutorials ?: array(),
			'date_range'       => $date_range,
		);
	}

	/**
	 * Get count of specific event type.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $event_type  Event type to count.
	 * @param int|null $tutorial_id Optional tutorial ID to filter by.
	 * @param array    $date_range  Optional date range with 'start' and 'end' keys.
	 * @return int Event count.
	 */
	public function get_event_count( string $event_type, ?int $tutorial_id = null, array $date_range = array() ): int {
		global $wpdb;

		$where_date = $this->build_date_where_clause( $date_range );

		if ( $tutorial_id ) {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$this->table_name}
					 WHERE event_type = %s AND tutorial_id = %d $where_date",
					$event_type,
					$tutorial_id
				)
			);
		} else {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$this->table_name}
					 WHERE event_type = %s $where_date",
					$event_type
				)
			);
		}

		return (int) $count;
	}

	/**
	 * Get count of unique users.
	 *
	 * @since 1.0.0
	 *
	 * @param int|null $tutorial_id Optional tutorial ID to filter by.
	 * @param array    $date_range  Optional date range with 'start' and 'end' keys.
	 * @return int Unique user count.
	 */
	public function get_unique_users( ?int $tutorial_id = null, array $date_range = array() ): int {
		global $wpdb;

		$where_date = $this->build_date_where_clause( $date_range );

		if ( $tutorial_id ) {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(DISTINCT user_id) FROM {$this->table_name}
					 WHERE tutorial_id = %d AND user_id IS NOT NULL $where_date",
					$tutorial_id
				)
			);
		} else {
			$count = $wpdb->get_var(
				"SELECT COUNT(DISTINCT user_id) FROM {$this->table_name}
				 WHERE user_id IS NOT NULL $where_date"
			);
		}

		return (int) $count;
	}

	/**
	 * Build date range WHERE clause for SQL queries.
	 *
	 * @since 1.0.0
	 *
	 * @param array $date_range Date range with 'start' and 'end' keys.
	 * @return string SQL WHERE clause fragment.
	 */
	private function build_date_where_clause( array $date_range ): string {
		global $wpdb;

		if ( empty( $date_range ) ) {
			return '';
		}

		$where = '';

		if ( ! empty( $date_range['start'] ) ) {
			$where .= $wpdb->prepare( ' AND created_at >= %s', $date_range['start'] );
		}

		if ( ! empty( $date_range['end'] ) ) {
			$where .= $wpdb->prepare( ' AND created_at <= %s', $date_range['end'] );
		}

		return $where;
	}

	/**
	 * Get or create session ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string Session ID.
	 */
	private function get_session_id(): string {
		// Check for existing session.
		if ( ! session_id() && ! headers_sent() ) {
			session_start();
		}

		// Get or create session ID.
		if ( ! isset( $_SESSION['aiddata_lms_session_id'] ) ) {
			$_SESSION['aiddata_lms_session_id'] = wp_generate_uuid4();
		}

		return $_SESSION['aiddata_lms_session_id'];
	}

	/**
	 * Hash IP address for privacy compliance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $ip IP address to hash.
	 * @return string Hashed IP address.
	 */
	private function hash_ip_address( string $ip ): string {
		// Hash IP for privacy (GDPR compliance).
		$salt = get_option( 'aiddata_lms_analytics_salt', '' );

		// Generate and save salt if it doesn't exist.
		if ( empty( $salt ) ) {
			$salt = wp_generate_password( 64, true, true );
			update_option( 'aiddata_lms_analytics_salt', $salt );
		}

		return hash( 'sha256', $ip . $salt );
	}

	/**
	 * Track enrollment event.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $enrollment_id Enrollment ID.
	 * @param int    $user_id       User ID.
	 * @param int    $tutorial_id   Tutorial ID.
	 * @param string $source        Enrollment source.
	 */
	public function track_enrollment( int $enrollment_id, int $user_id, int $tutorial_id, string $source ): void {
		$this->track_event(
			$tutorial_id,
			'tutorial_enroll',
			array(
				'enrollment_id' => $enrollment_id,
				'source'        => $source,
			),
			$user_id
		);
	}

	/**
	 * Track step completion event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial ID.
	 * @param int $step_index  Step index.
	 */
	public function track_step_completion( int $user_id, int $tutorial_id, int $step_index ): void {
		$this->track_event(
			$tutorial_id,
			'step_complete',
			array(
				'step_index' => $step_index,
			),
			$user_id
		);
	}

	/**
	 * Track tutorial view on single tutorial pages.
	 *
	 * @since 1.0.0
	 */
	public function track_tutorial_view(): void {
		if ( ! is_singular( 'aiddata_tutorial' ) ) {
			return;
		}

		$tutorial_id = get_the_ID();

		if ( ! $tutorial_id ) {
			return;
		}

		$this->track_event(
			$tutorial_id,
			'tutorial_view',
			array(
				'page' => 'overview',
			)
		);
	}

	/**
	 * Track tutorial completion event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id       User ID.
	 * @param int $tutorial_id   Tutorial ID.
	 * @param int $enrollment_id Enrollment ID.
	 */
	public function track_tutorial_completion( int $user_id, int $tutorial_id, int $enrollment_id ): void {
		$this->track_event(
			$tutorial_id,
			'tutorial_complete',
			array(
				'enrollment_id' => $enrollment_id,
			),
			$user_id
		);
	}

	/**
	 * Delete old analytics records.
	 *
	 * @since 1.0.0
	 *
	 * @param int $days Number of days to keep.
	 * @return int Number of records deleted.
	 */
	public function delete_old_records( int $days = 365 ): int {
		global $wpdb;

		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table_name}
				 WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days
			)
		);

		return $deleted ?: 0;
	}
}

