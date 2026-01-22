<?php
/**
 * Analytics Tracking System Test Suite
 *
 * Comprehensive test suite for analytics tracking functionality.
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Analytics_Test
 *
 * Test suite for the analytics tracking system.
 *
 * @since 1.0.0
 */
class AidData_LMS_Analytics_Test {

	/**
	 * Test results array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $results = array();

	/**
	 * Test user ID.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $test_user_id = 0;

	/**
	 * Test tutorial ID.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $test_tutorial_id = 0;

	/**
	 * Analytics instance.
	 *
	 * @since 1.0.0
	 * @var AidData_LMS_Analytics
	 */
	private $analytics;

	/**
	 * Run all tests.
	 *
	 * @since 1.0.0
	 *
	 * @return array Test results.
	 */
	public function run_tests(): array {
		$this->setup_test_data();

		// Run tests.
		$this->test_class_instantiation();
		$this->test_table_name_initialization();
		$this->test_track_event_success();
		$this->test_track_event_invalid_tutorial();
		$this->test_track_event_with_user();
		$this->test_track_event_with_data();
		$this->test_track_event_guest_user();
		$this->test_get_tutorial_analytics();
		$this->test_get_user_analytics();
		$this->test_get_platform_analytics();
		$this->test_get_event_count();
		$this->test_get_unique_users();
		$this->test_tutorial_analytics_with_date_range();
		$this->test_session_id_generation();
		$this->test_ip_hashing();
		$this->test_enrollment_tracking_hook();
		$this->test_step_completion_tracking_hook();
		$this->test_tutorial_view_tracking();
		$this->test_delete_old_records();
		$this->test_event_data_json_storage();

		$this->cleanup_test_data();

		return $this->results;
	}

	/**
	 * Setup test data.
	 *
	 * @since 1.0.0
	 */
	private function setup_test_data(): void {
		// Create test user.
		$this->test_user_id = wp_create_user( 'analytics_test_user_' . time(), 'password', 'analytics_test@example.com' );

		// Create test tutorial.
		$this->test_tutorial_id = wp_insert_post(
			array(
				'post_title'   => 'Analytics Test Tutorial',
				'post_type'    => 'aiddata_tutorial',
				'post_status'  => 'publish',
				'post_content' => 'Test content',
			)
		);

		// Initialize analytics.
		$this->analytics = new AidData_LMS_Analytics();
	}

	/**
	 * Cleanup test data.
	 *
	 * @since 1.0.0
	 */
	private function cleanup_test_data(): void {
		global $wpdb;

		// Delete test user.
		if ( $this->test_user_id ) {
			wp_delete_user( $this->test_user_id );
		}

		// Delete test tutorial.
		if ( $this->test_tutorial_id ) {
			wp_delete_post( $this->test_tutorial_id, true );
		}

		// Clean up analytics records.
		$table_name = AidData_LMS_Database::get_table_name( 'analytics' );
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $table_name WHERE tutorial_id = %d OR user_id = %d",
				$this->test_tutorial_id,
				$this->test_user_id
			)
		);
	}

	/**
	 * Test 1: Class instantiation.
	 *
	 * @since 1.0.0
	 */
	private function test_class_instantiation(): void {
		$this->results[] = array(
			'test'    => 'Class Instantiation',
			'passed'  => $this->analytics instanceof AidData_LMS_Analytics,
			'message' => 'Analytics class should instantiate correctly',
		);
	}

	/**
	 * Test 2: Table name initialization.
	 *
	 * @since 1.0.0
	 */
	private function test_table_name_initialization(): void {
		global $wpdb;
		$expected = $wpdb->prefix . 'aiddata_lms_tutorial_analytics';

		$this->results[] = array(
			'test'    => 'Table Name Initialization',
			'passed'  => $this->analytics->table_name === $expected,
			'message' => 'Table name should be set correctly',
		);
	}

	/**
	 * Test 3: Track event - Success.
	 *
	 * @since 1.0.0
	 */
	private function test_track_event_success(): void {
		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'test_event',
			array( 'test' => 'data' ),
			$this->test_user_id
		);

		$this->results[] = array(
			'test'    => 'Track Event - Success',
			'passed'  => is_int( $event_id ) && $event_id > 0,
			'message' => 'Should return event ID on success',
		);
	}

	/**
	 * Test 4: Track event - Invalid tutorial.
	 *
	 * @since 1.0.0
	 */
	private function test_track_event_invalid_tutorial(): void {
		$result = $this->analytics->track_event(
			999999,
			'test_event',
			array(),
			$this->test_user_id
		);

		$this->results[] = array(
			'test'    => 'Track Event - Invalid Tutorial',
			'passed'  => is_wp_error( $result ) && 'invalid_tutorial' === $result->get_error_code(),
			'message' => 'Should return WP_Error for invalid tutorial',
		);
	}

	/**
	 * Test 5: Track event with user ID.
	 *
	 * @since 1.0.0
	 */
	private function test_track_event_with_user(): void {
		global $wpdb;

		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'user_event',
			array(),
			$this->test_user_id
		);

		$table_name = AidData_LMS_Database::get_table_name( 'analytics' );
		$event      = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE id = %d",
				$event_id
			)
		);

		$this->results[] = array(
			'test'    => 'Track Event with User ID',
			'passed'  => $event && (int) $event->user_id === $this->test_user_id,
			'message' => 'Should store user ID correctly',
		);
	}

	/**
	 * Test 6: Track event with event data.
	 *
	 * @since 1.0.0
	 */
	private function test_track_event_with_data(): void {
		global $wpdb;

		$event_data = array(
			'key1' => 'value1',
			'key2' => 123,
		);

		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'data_event',
			$event_data,
			$this->test_user_id
		);

		$table_name    = AidData_LMS_Database::get_table_name( 'analytics' );
		$event         = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE id = %d",
				$event_id
			)
		);
		$retrieved_data = json_decode( $event->event_data, true );

		$this->results[] = array(
			'test'    => 'Track Event with Event Data',
			'passed'  => $retrieved_data === $event_data,
			'message' => 'Should store and retrieve event data as JSON',
		);
	}

	/**
	 * Test 7: Track event - Guest user.
	 *
	 * @since 1.0.0
	 */
	private function test_track_event_guest_user(): void {
		global $wpdb;

		// Track event without user ID.
		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'guest_event',
			array(),
			null
		);

		$table_name = AidData_LMS_Database::get_table_name( 'analytics' );
		$event      = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE id = %d",
				$event_id
			)
		);

		$this->results[] = array(
			'test'    => 'Track Event - Guest User',
			'passed'  => $event && is_null( $event->user_id ),
			'message' => 'Should allow tracking for guest users (NULL user_id)',
		);
	}

	/**
	 * Test 8: Get tutorial analytics.
	 *
	 * @since 1.0.0
	 */
	private function test_get_tutorial_analytics(): void {
		// Track multiple events.
		$this->analytics->track_event( $this->test_tutorial_id, 'view', array(), $this->test_user_id );
		$this->analytics->track_event( $this->test_tutorial_id, 'view', array(), $this->test_user_id );
		$this->analytics->track_event( $this->test_tutorial_id, 'complete', array(), $this->test_user_id );

		$analytics = $this->analytics->get_tutorial_analytics( $this->test_tutorial_id );

		$passed = isset( $analytics['tutorial_id'] ) &&
				isset( $analytics['event_counts'] ) &&
				is_array( $analytics['event_counts'] ) &&
				$analytics['unique_users'] > 0;

		$this->results[] = array(
			'test'    => 'Get Tutorial Analytics',
			'passed'  => $passed,
			'message' => 'Should return tutorial analytics data',
		);
	}

	/**
	 * Test 9: Get user analytics.
	 *
	 * @since 1.0.0
	 */
	private function test_get_user_analytics(): void {
		// Track events.
		$this->analytics->track_event( $this->test_tutorial_id, 'user_view', array(), $this->test_user_id );

		$analytics = $this->analytics->get_user_analytics( $this->test_user_id );

		$passed = isset( $analytics['user_id'] ) &&
				isset( $analytics['total_events'] ) &&
				$analytics['total_events'] > 0 &&
				isset( $analytics['unique_tutorials'] );

		$this->results[] = array(
			'test'    => 'Get User Analytics',
			'passed'  => $passed,
			'message' => 'Should return user analytics data',
		);
	}

	/**
	 * Test 10: Get platform analytics.
	 *
	 * @since 1.0.0
	 */
	private function test_get_platform_analytics(): void {
		$analytics = $this->analytics->get_platform_analytics();

		$passed = isset( $analytics['total_events'] ) &&
				isset( $analytics['unique_users'] ) &&
				isset( $analytics['unique_tutorials'] ) &&
				isset( $analytics['top_events'] ) &&
				is_array( $analytics['top_events'] );

		$this->results[] = array(
			'test'    => 'Get Platform Analytics',
			'passed'  => $passed,
			'message' => 'Should return platform-wide analytics',
		);
	}

	/**
	 * Test 11: Get event count.
	 *
	 * @since 1.0.0
	 */
	private function test_get_event_count(): void {
		// Track specific events.
		$this->analytics->track_event( $this->test_tutorial_id, 'count_test', array(), $this->test_user_id );
		$this->analytics->track_event( $this->test_tutorial_id, 'count_test', array(), $this->test_user_id );
		$this->analytics->track_event( $this->test_tutorial_id, 'count_test', array(), $this->test_user_id );

		$count = $this->analytics->get_event_count( 'count_test', $this->test_tutorial_id );

		$this->results[] = array(
			'test'    => 'Get Event Count',
			'passed'  => $count >= 3,
			'message' => 'Should return accurate event count',
		);
	}

	/**
	 * Test 12: Get unique users.
	 *
	 * @since 1.0.0
	 */
	private function test_get_unique_users(): void {
		// Track events.
		$this->analytics->track_event( $this->test_tutorial_id, 'unique_test', array(), $this->test_user_id );

		$count = $this->analytics->get_unique_users( $this->test_tutorial_id );

		$this->results[] = array(
			'test'    => 'Get Unique Users',
			'passed'  => $count >= 1,
			'message' => 'Should return unique user count',
		);
	}

	/**
	 * Test 13: Tutorial analytics with date range.
	 *
	 * @since 1.0.0
	 */
	private function test_tutorial_analytics_with_date_range(): void {
		$date_range = array(
			'start' => gmdate( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
			'end'   => gmdate( 'Y-m-d H:i:s', strtotime( '+1 day' ) ),
		);

		$analytics = $this->analytics->get_tutorial_analytics( $this->test_tutorial_id, $date_range );

		$this->results[] = array(
			'test'    => 'Tutorial Analytics with Date Range',
			'passed'  => isset( $analytics['date_range'] ) && $analytics['date_range'] === $date_range,
			'message' => 'Should accept and return date range',
		);
	}

	/**
	 * Test 14: Session ID generation.
	 *
	 * @since 1.0.0
	 */
	private function test_session_id_generation(): void {
		global $wpdb;

		// Track event to generate session.
		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'session_test',
			array(),
			$this->test_user_id
		);

		$table_name = AidData_LMS_Database::get_table_name( 'analytics' );
		$event      = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE id = %d",
				$event_id
			)
		);

		$this->results[] = array(
			'test'    => 'Session ID Generation',
			'passed'  => $event && ! empty( $event->session_id ),
			'message' => 'Should generate and store session ID',
		);
	}

	/**
	 * Test 15: IP address hashing.
	 *
	 * @since 1.0.0
	 */
	private function test_ip_hashing(): void {
		global $wpdb;

		// Track event.
		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'ip_test',
			array(),
			$this->test_user_id
		);

		$table_name = AidData_LMS_Database::get_table_name( 'analytics' );
		$event      = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE id = %d",
				$event_id
			)
		);

		// IP should be hashed (64 character SHA256).
		$is_hashed = $event && strlen( $event->ip_address ) === 64;

		$this->results[] = array(
			'test'    => 'IP Address Hashing',
			'passed'  => $is_hashed,
			'message' => 'Should hash IP addresses for privacy',
		);
	}

	/**
	 * Test 16: Enrollment tracking hook.
	 *
	 * @since 1.0.0
	 */
	private function test_enrollment_tracking_hook(): void {
		// Trigger enrollment hook.
		do_action( 'aiddata_lms_user_enrolled', 123, $this->test_user_id, $this->test_tutorial_id, 'test' );

		// Check if event was tracked.
		$count = $this->analytics->get_event_count( 'tutorial_enroll', $this->test_tutorial_id );

		$this->results[] = array(
			'test'    => 'Enrollment Tracking Hook',
			'passed'  => $count > 0,
			'message' => 'Should track enrollment events via hook',
		);
	}

	/**
	 * Test 17: Step completion tracking hook.
	 *
	 * @since 1.0.0
	 */
	private function test_step_completion_tracking_hook(): void {
		// Trigger step completion hook.
		do_action( 'aiddata_lms_step_completed', $this->test_user_id, $this->test_tutorial_id, 1 );

		// Check if event was tracked.
		$count = $this->analytics->get_event_count( 'step_complete', $this->test_tutorial_id );

		$this->results[] = array(
			'test'    => 'Step Completion Tracking Hook',
			'passed'  => $count > 0,
			'message' => 'Should track step completion via hook',
		);
	}

	/**
	 * Test 18: Tutorial view tracking.
	 *
	 * @since 1.0.0
	 */
	private function test_tutorial_view_tracking(): void {
		// Manually track a view.
		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'tutorial_view',
			array( 'page' => 'overview' )
		);

		$this->results[] = array(
			'test'    => 'Tutorial View Tracking',
			'passed'  => is_int( $event_id ) && $event_id > 0,
			'message' => 'Should track tutorial views',
		);
	}

	/**
	 * Test 19: Delete old records.
	 *
	 * @since 1.0.0
	 */
	private function test_delete_old_records(): void {
		global $wpdb;

		$table_name = AidData_LMS_Database::get_table_name( 'analytics' );

		// Insert old record.
		$wpdb->insert(
			$table_name,
			array(
				'tutorial_id' => $this->test_tutorial_id,
				'user_id'     => $this->test_user_id,
				'event_type'  => 'old_event',
				'created_at'  => gmdate( 'Y-m-d H:i:s', strtotime( '-400 days' ) ),
			),
			array( '%d', '%d', '%s', '%s' )
		);

		// Delete records older than 365 days.
		$deleted = $this->analytics->delete_old_records( 365 );

		$this->results[] = array(
			'test'    => 'Delete Old Records',
			'passed'  => $deleted >= 1,
			'message' => 'Should delete old analytics records',
		);
	}

	/**
	 * Test 20: Event data JSON storage.
	 *
	 * @since 1.0.0
	 */
	private function test_event_data_json_storage(): void {
		global $wpdb;

		$complex_data = array(
			'nested' => array(
				'key' => 'value',
			),
			'array'  => array( 1, 2, 3 ),
			'string' => 'test',
		);

		$event_id = $this->analytics->track_event(
			$this->test_tutorial_id,
			'json_test',
			$complex_data,
			$this->test_user_id
		);

		$table_name     = AidData_LMS_Database::get_table_name( 'analytics' );
		$event          = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE id = %d",
				$event_id
			)
		);
		$retrieved_data = json_decode( $event->event_data, true );

		$this->results[] = array(
			'test'    => 'Event Data JSON Storage',
			'passed'  => $retrieved_data === $complex_data,
			'message' => 'Should store complex data structures as JSON',
		);
	}

	/**
	 * Display test results.
	 *
	 * @since 1.0.0
	 */
	public function display_results(): void {
		$total  = count( $this->results );
		$passed = count( array_filter( $this->results, fn( $r ) => $r['passed'] ) );
		$failed = $total - $passed;

		echo '<div class="wrap">';
		echo '<h1>Analytics Test Results</h1>';
		echo '<p><strong>Total Tests:</strong> ' . esc_html( $total ) . ' | ';
		echo '<strong style="color: green;">Passed:</strong> ' . esc_html( $passed ) . ' | ';
		echo '<strong style="color: red;">Failed:</strong> ' . esc_html( $failed ) . '</p>';

		echo '<table class="wp-list-table widefat fixed striped">';
		echo '<thead><tr>';
		echo '<th width="5%">Status</th>';
		echo '<th width="30%">Test</th>';
		echo '<th width="65%">Message</th>';
		echo '</tr></thead>';
		echo '<tbody>';

		foreach ( $this->results as $result ) {
			$status_color = $result['passed'] ? 'green' : 'red';
			$status_icon  = $result['passed'] ? '✓' : '✗';

			echo '<tr>';
			echo '<td style="text-align: center; color: ' . esc_attr( $status_color ) . '; font-size: 20px;">' . esc_html( $status_icon ) . '</td>';
			echo '<td><strong>' . esc_html( $result['test'] ) . '</strong></td>';
			echo '<td>' . esc_html( $result['message'] ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</div>';
	}
}

