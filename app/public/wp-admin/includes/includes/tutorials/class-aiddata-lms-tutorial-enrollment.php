<?php
/**
 * Tutorial Enrollment Manager
 *
 * Handles user enrollment, unenrollment, status tracking, and prerequisite validation.
 *
 * @package AidData_LMS
 * @subpackage Tutorials
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Tutorial_Enrollment
 *
 * Manages tutorial enrollment operations including enrollment, unenrollment,
 * status tracking, and prerequisite validation.
 *
 * @since 1.0.0
 */
class AidData_LMS_Tutorial_Enrollment {

	/**
	 * Database table name for enrollments.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name;

	/**
	 * Constructor.
	 *
	 * Initializes the enrollment manager and sets up table name.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->table_name = AidData_LMS_Database::get_table_name( 'enrollments' );
	}

	/**
	 * Enroll a user in a tutorial.
	 *
	 * Validates enrollment requirements and creates enrollment record.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $user_id     User ID to enroll.
	 * @param int    $tutorial_id Tutorial post ID.
	 * @param string $source      Enrollment source (e.g., 'web', 'api', 'admin').
	 * @return int|WP_Error Enrollment ID on success, WP_Error on failure.
	 */
	public function enroll_user( int $user_id, int $tutorial_id, string $source = 'web' ) {
		global $wpdb;

		// Fire before enrollment hook.
		do_action( 'aiddata_lms_before_enrollment', $user_id, $tutorial_id, $source );

		// Validate enrollment.
		$validation = $this->validate_enrollment( $user_id, $tutorial_id );

		if ( ! $validation['valid'] ) {
			$error_message = ! empty( $validation['errors'] ) ? implode( ' ', $validation['errors'] ) : __( 'Enrollment validation failed.', 'aiddata-lms' );
			return new WP_Error( 'enrollment_invalid', $error_message );
		}

		// Insert enrollment record.
		$result = $wpdb->insert(
			$this->table_name,
			array(
				'user_id'     => $user_id,
				'tutorial_id' => $tutorial_id,
				'enrolled_at' => current_time( 'mysql' ),
				'status'      => 'active',
				'source'      => sanitize_key( $source ),
			),
			array( '%d', '%d', '%s', '%s', '%s' )
		);

		if ( false === $result ) {
			error_log( sprintf( 'Enrollment insert failed: %s', $wpdb->last_error ) );
			return new WP_Error( 'database_error', __( 'Failed to create enrollment record.', 'aiddata-lms' ), $wpdb->last_error );
		}

		$enrollment_id = $wpdb->insert_id;

		// Fire after enrollment hook.
		do_action( 'aiddata_lms_user_enrolled', $enrollment_id, $user_id, $tutorial_id, $source );

		return $enrollment_id;
	}

	/**
	 * Unenroll a user from a tutorial.
	 *
	 * Marks enrollment as cancelled but preserves progress data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID to unenroll.
	 * @param int $tutorial_id Tutorial post ID.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function unenroll_user( int $user_id, int $tutorial_id ) {
		global $wpdb;

		// Check if user is enrolled.
		$enrollment = $this->get_enrollment( $user_id, $tutorial_id );

		if ( ! $enrollment ) {
			return new WP_Error( 'not_enrolled', __( 'User is not enrolled in this tutorial.', 'aiddata-lms' ) );
		}

		// Update enrollment status to cancelled.
		$result = $wpdb->update(
			$this->table_name,
			array(
				'status'        => 'cancelled',
				'unenrolled_at' => current_time( 'mysql' ),
			),
			array(
				'user_id'     => $user_id,
				'tutorial_id' => $tutorial_id,
			),
			array( '%s', '%s' ),
			array( '%d', '%d' )
		);

		if ( false === $result ) {
			error_log( sprintf( 'Unenrollment update failed: %s', $wpdb->last_error ) );
			return new WP_Error( 'database_error', __( 'Failed to update enrollment record.', 'aiddata-lms' ), $wpdb->last_error );
		}

		// Fire unenrollment hook.
		do_action( 'aiddata_lms_user_unenrolled', $user_id, $tutorial_id );

		return true;
	}

	/**
	 * Get all enrollments for a specific user.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $user_id User ID.
	 * @param string $status  Optional. Filter by enrollment status. Default 'active'.
	 * @return array Array of enrollment objects.
	 */
	public function get_user_enrollments( int $user_id, string $status = 'active' ): array {
		global $wpdb;

		$where = $wpdb->prepare( 'WHERE user_id = %d', $user_id );

		if ( ! empty( $status ) && 'all' !== $status ) {
			$where .= $wpdb->prepare( ' AND status = %s', $status );
		}

		$query = "SELECT * FROM {$this->table_name} {$where} ORDER BY enrolled_at DESC";

		$results = $wpdb->get_results( $query );

		return $results ? $results : array();
	}

	/**
	 * Get all enrollments for a specific tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $tutorial_id Tutorial post ID.
	 * @param string $status      Optional. Filter by enrollment status. Default 'active'.
	 * @return array Array of enrollment objects.
	 */
	public function get_tutorial_enrollments( int $tutorial_id, string $status = 'active' ): array {
		global $wpdb;

		$where = $wpdb->prepare( 'WHERE tutorial_id = %d', $tutorial_id );

		if ( ! empty( $status ) && 'all' !== $status ) {
			$where .= $wpdb->prepare( ' AND status = %s', $status );
		}

		$query = "SELECT * FROM {$this->table_name} {$where} ORDER BY enrolled_at DESC";

		$results = $wpdb->get_results( $query );

		return $results ? $results : array();
	}

	/**
	 * Check if a user is enrolled in a tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 * @return bool True if enrolled and active, false otherwise.
	 */
	public function is_user_enrolled( int $user_id, int $tutorial_id ): bool {
		global $wpdb;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} 
				WHERE user_id = %d 
				AND tutorial_id = %d 
				AND status = 'active'",
				$user_id,
				$tutorial_id
			)
		);

		return $count > 0;
	}

	/**
	 * Get a specific enrollment record.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 * @return object|null Enrollment object or null if not found.
	 */
	public function get_enrollment( int $user_id, int $tutorial_id ): ?object {
		global $wpdb;

		$enrollment = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} 
				WHERE user_id = %d 
				AND tutorial_id = %d 
				ORDER BY enrolled_at DESC 
				LIMIT 1",
				$user_id,
				$tutorial_id
			)
		);

		return $enrollment ?: null;
	}

	/**
	 * Update enrollment status.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $enrollment_id Enrollment record ID.
	 * @param string $status        New status (active, completed, cancelled, expired).
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function update_enrollment_status( int $enrollment_id, string $status ) {
		global $wpdb;

		// Validate status.
		$valid_statuses = array( 'active', 'completed', 'cancelled', 'expired' );
		if ( ! in_array( $status, $valid_statuses, true ) ) {
			return new WP_Error( 'invalid_status', __( 'Invalid enrollment status.', 'aiddata-lms' ) );
		}

		// Get current enrollment.
		$enrollment = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE id = %d",
				$enrollment_id
			)
		);

		if ( ! $enrollment ) {
			return new WP_Error( 'enrollment_not_found', __( 'Enrollment record not found.', 'aiddata-lms' ) );
		}

		$old_status = $enrollment->status;

		// Update status.
		$result = $wpdb->update(
			$this->table_name,
			array( 'status' => $status ),
			array( 'id' => $enrollment_id ),
			array( '%s' ),
			array( '%d' )
		);

		if ( false === $result ) {
			error_log( sprintf( 'Enrollment status update failed: %s', $wpdb->last_error ) );
			return new WP_Error( 'database_error', __( 'Failed to update enrollment status.', 'aiddata-lms' ), $wpdb->last_error );
		}

		// Fire status change hook.
		do_action( 'aiddata_lms_enrollment_status_changed', $enrollment_id, $old_status, $status );

		return true;
	}

	/**
	 * Mark an enrollment as completed.
	 *
	 * @since 1.0.0
	 *
	 * @param int           $user_id      User ID.
	 * @param int           $tutorial_id  Tutorial post ID.
	 * @param DateTime|null $completed_at Optional. Completion timestamp. Defaults to current time.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function mark_completed( int $user_id, int $tutorial_id, ?DateTime $completed_at = null ) {
		global $wpdb;

		// Get enrollment.
		$enrollment = $this->get_enrollment( $user_id, $tutorial_id );

		if ( ! $enrollment ) {
			return new WP_Error( 'not_enrolled', __( 'User is not enrolled in this tutorial.', 'aiddata-lms' ) );
		}

		// Check if already completed.
		if ( 'completed' === $enrollment->status && ! empty( $enrollment->completed_at ) ) {
			return new WP_Error( 'already_completed', __( 'Tutorial already marked as completed.', 'aiddata-lms' ) );
		}

		// Set completion timestamp.
		$completion_time = $completed_at ? $completed_at->format( 'Y-m-d H:i:s' ) : current_time( 'mysql' );

		// Update enrollment.
		$result = $wpdb->update(
			$this->table_name,
			array(
				'status'       => 'completed',
				'completed_at' => $completion_time,
			),
			array( 'id' => $enrollment->id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		if ( false === $result ) {
			error_log( sprintf( 'Enrollment completion update failed: %s', $wpdb->last_error ) );
			return new WP_Error( 'database_error', __( 'Failed to mark enrollment as completed.', 'aiddata-lms' ), $wpdb->last_error );
		}

		// Fire completion hook.
		do_action( 'aiddata_lms_enrollment_completed', $enrollment->id, $user_id, $tutorial_id );

		return true;
	}

	/**
	 * Get enrollment count for a tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $tutorial_id Tutorial post ID.
	 * @param string|null $status      Optional. Filter by status. Default null (all).
	 * @return int Number of enrollments.
	 */
	public function get_enrollment_count( int $tutorial_id, ?string $status = null ): int {
		global $wpdb;

		$where = $wpdb->prepare( 'WHERE tutorial_id = %d', $tutorial_id );

		if ( ! empty( $status ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $status );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} {$where}" );

		return (int) $count;
	}

	/**
	 * Add enrollment metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $enrollment_id Enrollment record ID.
	 * @param string $meta_key      Meta key.
	 * @param mixed  $meta_value    Meta value.
	 * @return bool True on success, false on failure.
	 */
	public function add_enrollment_meta( int $enrollment_id, string $meta_key, $meta_value ): bool {
		global $wpdb;

		// Get enrollment to validate it exists.
		$enrollment = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT user_id FROM {$this->table_name} WHERE id = %d",
				$enrollment_id
			)
		);

		if ( ! $enrollment ) {
			return false;
		}

		// Store in user meta with enrollment-specific key.
		$meta_key_full = sprintf( 'aiddata_lms_enrollment_%d_%s', $enrollment_id, sanitize_key( $meta_key ) );
		return add_user_meta( $enrollment->user_id, $meta_key_full, $meta_value, true );
	}

	/**
	 * Get enrollment metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $enrollment_id Enrollment record ID.
	 * @param string $meta_key      Meta key.
	 * @return mixed Meta value or false if not found.
	 */
	public function get_enrollment_meta( int $enrollment_id, string $meta_key ) {
		global $wpdb;

		// Get enrollment to get user_id.
		$enrollment = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT user_id FROM {$this->table_name} WHERE id = %d",
				$enrollment_id
			)
		);

		if ( ! $enrollment ) {
			return false;
		}

		$meta_key_full = sprintf( 'aiddata_lms_enrollment_%d_%s', $enrollment_id, sanitize_key( $meta_key ) );
		return get_user_meta( $enrollment->user_id, $meta_key_full, true );
	}

	/**
	 * Update enrollment metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $enrollment_id Enrollment record ID.
	 * @param string $meta_key      Meta key.
	 * @param mixed  $meta_value    Meta value.
	 * @return bool True on success, false on failure.
	 */
	public function update_enrollment_meta( int $enrollment_id, string $meta_key, $meta_value ): bool {
		global $wpdb;

		// Get enrollment to validate it exists.
		$enrollment = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT user_id FROM {$this->table_name} WHERE id = %d",
				$enrollment_id
			)
		);

		if ( ! $enrollment ) {
			return false;
		}

		$meta_key_full = sprintf( 'aiddata_lms_enrollment_%d_%s', $enrollment_id, sanitize_key( $meta_key ) );
		return update_user_meta( $enrollment->user_id, $meta_key_full, $meta_value );
	}

	/**
	 * Validate enrollment requirements.
	 *
	 * Checks user existence, tutorial availability, prerequisites,
	 * enrollment limits, and access permissions.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 * @return array Validation result with 'valid', 'errors', and 'warnings' keys.
	 */
	private function validate_enrollment( int $user_id, int $tutorial_id ): array {
		$errors   = array();
		$warnings = array();

		// Check if user exists.
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			$errors[] = __( 'User does not exist.', 'aiddata-lms' );
			return array(
				'valid'    => false,
				'errors'   => $errors,
				'warnings' => $warnings,
			);
		}

		// Check if tutorial exists and is published.
		$tutorial = get_post( $tutorial_id );
		if ( ! $tutorial || 'aiddata_tutorial' !== $tutorial->post_type ) {
			$errors[] = __( 'Tutorial does not exist.', 'aiddata-lms' );
		}

		if ( $tutorial && 'publish' !== $tutorial->post_status ) {
			$errors[] = __( 'Tutorial is not published.', 'aiddata-lms' );
		}

		// Check if user is already enrolled.
		if ( $this->is_user_enrolled( $user_id, $tutorial_id ) ) {
			$errors[] = __( 'User is already enrolled in this tutorial.', 'aiddata-lms' );
		}

		// Check enrollment limit (if configured).
		$enrollment_limit = get_post_meta( $tutorial_id, '_tutorial_enrollment_limit', true );
		if ( ! empty( $enrollment_limit ) && $enrollment_limit > 0 ) {
			$current_enrollments = $this->get_enrollment_count( $tutorial_id, 'active' );
			if ( $current_enrollments >= $enrollment_limit ) {
				$errors[] = __( 'Enrollment limit has been reached for this tutorial.', 'aiddata-lms' );
			}
		}

		// Check enrollment deadline (if configured).
		$enrollment_deadline = get_post_meta( $tutorial_id, '_tutorial_enrollment_deadline', true );
		if ( ! empty( $enrollment_deadline ) ) {
			$deadline = strtotime( $enrollment_deadline );
			if ( $deadline && time() > $deadline ) {
				$errors[] = __( 'Enrollment deadline has passed.', 'aiddata-lms' );
			}
		}

		// Check prerequisites (if configured).
		$prerequisites = get_post_meta( $tutorial_id, '_tutorial_prerequisites', true );
		if ( ! empty( $prerequisites ) && is_array( $prerequisites ) ) {
			foreach ( $prerequisites as $prerequisite_id ) {
				$prerequisite_enrollment = $this->get_enrollment( $user_id, $prerequisite_id );
				if ( ! $prerequisite_enrollment || 'completed' !== $prerequisite_enrollment->status ) {
					$prerequisite_title = get_the_title( $prerequisite_id );
					$errors[]           = sprintf(
						/* translators: %s: prerequisite tutorial title */
						__( 'You must complete "%s" before enrolling in this tutorial.', 'aiddata-lms' ),
						$prerequisite_title
					);
				}
			}
		}

		// Check access permissions (if restricted by role/capability).
		$required_capability = get_post_meta( $tutorial_id, '_tutorial_required_capability', true );
		if ( ! empty( $required_capability ) && ! user_can( $user_id, $required_capability ) ) {
			$errors[] = __( 'You do not have permission to enroll in this tutorial.', 'aiddata-lms' );
		}

		// Apply custom validation filters.
		$errors   = apply_filters( 'aiddata_lms_enrollment_validation_errors', $errors, $user_id, $tutorial_id );
		$warnings = apply_filters( 'aiddata_lms_enrollment_validation_warnings', $warnings, $user_id, $tutorial_id );

		return array(
			'valid'    => empty( $errors ),
			'errors'   => $errors,
			'warnings' => $warnings,
		);
	}
}

