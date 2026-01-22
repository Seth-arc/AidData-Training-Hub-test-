<?php
/**
 * Tutorial Progress Manager
 *
 * Handles tutorial progress tracking, step completion, percentage calculation,
 * and status management.
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
 * Class AidData_LMS_Tutorial_Progress
 *
 * Manages tutorial progress tracking including step completion,
 * progress percentage calculation, and time tracking.
 *
 * @since 1.0.0
 */
class AidData_LMS_Tutorial_Progress {

	/**
	 * Database table name for progress.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name;

	/**
	 * Constructor.
	 *
	 * Initializes the progress manager and sets up table name and hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->table_name = AidData_LMS_Database::get_table_name( 'progress' );

		// Hook into enrollment events to initialize progress.
		add_action( 'aiddata_lms_user_enrolled', array( $this, 'on_user_enrolled' ), 10, 4 );
	}

	/**
	 * Update user's progress for a tutorial step.
	 *
	 * Updates step completion, progress percentage, and status.
	 *
	 * @since 1.0.0
	 *
	 * @param int      $user_id       User ID.
	 * @param int      $tutorial_id   Tutorial post ID.
	 * @param int      $step_index    Step index (0-based).
	 * @param int|null $enrollment_id Optional. Enrollment ID. Default null.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function update_progress( int $user_id, int $tutorial_id, int $step_index, ?int $enrollment_id = null ) {
		global $wpdb;

		// Validate user exists.
		if ( ! get_userdata( $user_id ) ) {
			return new WP_Error(
				'invalid_user',
				__( 'User does not exist.', 'aiddata-lms' )
			);
		}

		// Validate tutorial exists.
		$tutorial = get_post( $tutorial_id );
		if ( ! $tutorial || 'aiddata_tutorial' !== $tutorial->post_type ) {
			return new WP_Error(
				'invalid_tutorial',
				__( 'Tutorial does not exist.', 'aiddata-lms' )
			);
		}

		// Validate step index.
		if ( $step_index < 0 ) {
			return new WP_Error(
				'invalid_step',
				__( 'Invalid step index.', 'aiddata-lms' )
			);
		}

		// Get current progress record.
		$progress = $this->get_progress( $user_id, $tutorial_id );

		if ( ! $progress ) {
			// Initialize progress if it doesn't exist.
			if ( is_null( $enrollment_id ) ) {
				// Try to get enrollment ID.
				$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
				$enrollment         = $enrollment_manager->get_enrollment( $user_id, $tutorial_id );

				if ( ! $enrollment ) {
					return new WP_Error(
						'not_enrolled',
						__( 'User is not enrolled in this tutorial.', 'aiddata-lms' )
					);
				}

				$enrollment_id = $enrollment->id;
			}

			$init_result = $this->initialize_progress( $user_id, $tutorial_id, $enrollment_id );

			if ( is_wp_error( $init_result ) ) {
				return $init_result;
			}

			$progress = $this->get_progress( $user_id, $tutorial_id );
		}

		// Get completed steps.
		$completed_steps = $this->get_completed_steps( $user_id, $tutorial_id );

		// Add new step if not already completed.
		if ( ! in_array( $step_index, $completed_steps, true ) ) {
			$completed_steps[] = $step_index;
			sort( $completed_steps );
		}

		// Save updated completion.
		$completed_string = implode( ',', $completed_steps );

		// Calculate percentage.
		$total_steps      = $this->get_tutorial_step_count( $tutorial_id );
		$progress_percent = 0.00;

		if ( $total_steps > 0 ) {
			$progress_percent = ( count( $completed_steps ) / $total_steps ) * 100;
		}

		// Determine status.
		$status = 'in_progress';
		if ( $progress_percent >= 100 ) {
			$status = 'completed';
		} elseif ( 0 === count( $completed_steps ) ) {
			$status = 'not_started';
		}

		// Update progress record.
		$result = $wpdb->update(
			$this->table_name,
			array(
				'current_step'     => $step_index,
				'completed_steps'  => $completed_string,
				'progress_percent' => $progress_percent,
				'status'           => $status,
				'last_accessed'    => current_time( 'mysql' ),
			),
			array(
				'user_id'     => $user_id,
				'tutorial_id' => $tutorial_id,
			),
			array( '%d', '%s', '%f', '%s', '%s' ),
			array( '%d', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'database_error',
				__( 'Failed to update progress.', 'aiddata-lms' ),
				$wpdb->last_error
			);
		}

		// Fire hooks.
		do_action( 'aiddata_lms_step_completed', $user_id, $tutorial_id, $step_index );
		do_action( 'aiddata_lms_progress_updated', $user_id, $tutorial_id, $progress_percent );

		// If completed, mark enrollment as completed.
		if ( 'completed' === $status && 100 === (int) $progress_percent ) {
			$this->mark_tutorial_complete( $user_id, $tutorial_id );
		}

		return true;
	}

	/**
	 * Get user's progress for a tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return object|null Progress object or null if not found.
	 */
	public function get_progress( int $user_id, int $tutorial_id ): ?object {
		global $wpdb;

		$progress = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} 
				WHERE user_id = %d 
				AND tutorial_id = %d
				LIMIT 1",
				$user_id,
				$tutorial_id
			)
		);

		return $progress ?: null;
	}

	/**
	 * Get the last step index accessed by user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return int Last step index, or 0 if not found.
	 */
	public function get_last_step( int $user_id, int $tutorial_id ): int {
		$progress = $this->get_progress( $user_id, $tutorial_id );

		if ( ! $progress ) {
			return 0;
		}

		return (int) $progress->current_step;
	}

	/**
	 * Calculate progress percentage for a tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return float Progress percentage (0-100).
	 */
	public function calculate_progress_percent( int $user_id, int $tutorial_id ): float {
		$progress = $this->get_progress( $user_id, $tutorial_id );

		if ( ! $progress ) {
			return 0.0;
		}

		return (float) $progress->progress_percent;
	}

	/**
	 * Mark a tutorial as complete.
	 *
	 * Updates both progress and enrollment records.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function mark_tutorial_complete( int $user_id, int $tutorial_id ) {
		global $wpdb;

		// Get progress record.
		$progress = $this->get_progress( $user_id, $tutorial_id );

		if ( ! $progress ) {
			return new WP_Error(
				'progress_not_found',
				__( 'Progress record not found.', 'aiddata-lms' )
			);
		}

		// Check if already completed.
		if ( 'completed' === $progress->status && ! is_null( $progress->completed_at ) ) {
			return new WP_Error(
				'already_completed',
				__( 'Tutorial already marked as completed.', 'aiddata-lms' )
			);
		}

		// Update progress record.
		$result = $wpdb->update(
			$this->table_name,
			array(
				'status'       => 'completed',
				'completed_at' => current_time( 'mysql' ),
			),
			array(
				'user_id'     => $user_id,
				'tutorial_id' => $tutorial_id,
			),
			array( '%s', '%s' ),
			array( '%d', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'database_error',
				__( 'Failed to mark tutorial as completed.', 'aiddata-lms' ),
				$wpdb->last_error
			);
		}

		// Update enrollment record.
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		$enrollment_result  = $enrollment_manager->mark_completed( $user_id, $tutorial_id );

		if ( is_wp_error( $enrollment_result ) ) {
			// Log error but don't fail - progress is already updated.
			error_log( 'AidData LMS: Failed to update enrollment completion: ' . $enrollment_result->get_error_message() );
		}

		// Fire completion hook.
		do_action( 'aiddata_lms_tutorial_completed', $user_id, $tutorial_id, $progress->enrollment_id );

		return true;
	}

	/**
	 * Get array of completed step indices.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return array Array of completed step indices.
	 */
	public function get_completed_steps( int $user_id, int $tutorial_id ): array {
		$progress = $this->get_progress( $user_id, $tutorial_id );

		if ( ! $progress || empty( $progress->completed_steps ) ) {
			return array();
		}

		$steps = explode( ',', $progress->completed_steps );

		// Convert to integers and filter out empty values.
		$steps = array_map( 'intval', array_filter( $steps ) );

		return $steps;
	}

	/**
	 * Check if a specific step is completed.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 * @param int $step_index  Step index to check.
	 *
	 * @return bool True if step is completed, false otherwise.
	 */
	public function is_step_completed( int $user_id, int $tutorial_id, int $step_index ): bool {
		$completed_steps = $this->get_completed_steps( $user_id, $tutorial_id );

		return in_array( $step_index, $completed_steps, true );
	}

	/**
	 * Update time spent on a tutorial.
	 *
	 * Accumulates time spent in seconds.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 * @param int $seconds     Time spent in seconds to add.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function update_time_spent( int $user_id, int $tutorial_id, int $seconds ) {
		global $wpdb;

		// Validate seconds is positive.
		if ( $seconds <= 0 ) {
			return new WP_Error(
				'invalid_time',
				__( 'Time must be a positive number.', 'aiddata-lms' )
			);
		}

		// Get current progress.
		$progress = $this->get_progress( $user_id, $tutorial_id );

		if ( ! $progress ) {
			return new WP_Error(
				'progress_not_found',
				__( 'Progress record not found.', 'aiddata-lms' )
			);
		}

		// Calculate new time spent.
		$new_time_spent = (int) $progress->time_spent + $seconds;

		// Update record.
		$result = $wpdb->update(
			$this->table_name,
			array(
				'time_spent'    => $new_time_spent,
				'last_accessed' => current_time( 'mysql' ),
			),
			array(
				'user_id'     => $user_id,
				'tutorial_id' => $tutorial_id,
			),
			array( '%d', '%s' ),
			array( '%d', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'database_error',
				__( 'Failed to update time spent.', 'aiddata-lms' ),
				$wpdb->last_error
			);
		}

		return true;
	}

	/**
	 * Format time spent for display.
	 *
	 * Converts seconds to human-readable format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $seconds Time in seconds.
	 *
	 * @return string Formatted time string.
	 */
	public function format_time_spent( int $seconds ): string {
		$hours   = floor( $seconds / 3600 );
		$minutes = floor( ( $seconds % 3600 ) / 60 );

		if ( $hours > 0 ) {
			/* translators: %1$d: hours, %2$d: minutes */
			return sprintf( __( '%1$d hour(s), %2$d minute(s)', 'aiddata-lms' ), $hours, $minutes );
		}

		/* translators: %d: minutes */
		return sprintf( __( '%d minute(s)', 'aiddata-lms' ), $minutes );
	}

	/**
	 * Initialize progress record when user enrolls.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id       User ID.
	 * @param int $tutorial_id   Tutorial post ID.
	 * @param int $enrollment_id Enrollment record ID.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function initialize_progress( int $user_id, int $tutorial_id, int $enrollment_id ) {
		global $wpdb;

		// Check if progress already exists.
		$exists = $this->get_progress( $user_id, $tutorial_id );

		if ( $exists ) {
			return new WP_Error(
				'progress_exists',
				__( 'Progress record already exists.', 'aiddata-lms' )
			);
		}

		// Create initial progress record.
		$result = $wpdb->insert(
			$this->table_name,
			array(
				'user_id'          => $user_id,
				'tutorial_id'      => $tutorial_id,
				'enrollment_id'    => $enrollment_id,
				'current_step'     => 0,
				'completed_steps'  => '',
				'progress_percent' => 0.00,
				'status'           => 'not_started',
				'quiz_passed'      => 0,
				'quiz_attempts'    => 0,
				'time_spent'       => 0,
			),
			array( '%d', '%d', '%d', '%d', '%s', '%f', '%s', '%d', '%d', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'database_error',
				__( 'Failed to initialize progress record.', 'aiddata-lms' ),
				$wpdb->last_error
			);
		}

		return true;
	}

	/**
	 * Set the current step for a user's progress.
	 *
	 * Updates the current_step field to indicate which step the user is viewing.
	 * Does not mark steps as complete.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 * @param int $step_index  Step index (0-based).
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function set_current_step( int $user_id, int $tutorial_id, int $step_index ) {
		global $wpdb;

		// Get current progress record.
		$progress = $this->get_progress( $user_id, $tutorial_id );

		if ( ! $progress ) {
			return new WP_Error(
				'no_progress',
				__( 'Progress record not found.', 'aiddata-lms' )
			);
		}

		// Update current step.
		$result = $wpdb->update(
			$this->table_name,
			array(
				'current_step' => $step_index,
			),
			array(
				'user_id'     => $user_id,
				'tutorial_id' => $tutorial_id,
			),
			array( '%d' ),
			array( '%d', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'update_failed',
				__( 'Failed to update current step.', 'aiddata-lms' )
			);
		}

		return true;
	}

	/**
	 * Handle enrollment event to initialize progress.
	 *
	 * Hooked to 'aiddata_lms_user_enrolled' action.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $enrollment_id Enrollment record ID.
	 * @param int    $user_id       User ID.
	 * @param int    $tutorial_id   Tutorial post ID.
	 * @param string $source        Enrollment source.
	 *
	 * @return void
	 */
	public function on_user_enrolled( int $enrollment_id, int $user_id, int $tutorial_id, string $source ): void {
		$result = $this->initialize_progress( $user_id, $tutorial_id, $enrollment_id );

		if ( is_wp_error( $result ) ) {
			// Log error but don't interrupt enrollment process.
			error_log( 'AidData LMS: Failed to initialize progress: ' . $result->get_error_message() );
		}
	}

	/**
	 * Get the number of steps in a tutorial.
	 *
	 * Retrieves step count from tutorial post meta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return int Number of steps, or 0 if not found.
	 */
	private function get_tutorial_step_count( int $tutorial_id ): int {
		$steps = get_post_meta( $tutorial_id, '_tutorial_steps', true );

		if ( empty( $steps ) || ! is_array( $steps ) ) {
			return 0;
		}

		return count( $steps );
	}

	/**
	 * Get progress statistics for a tutorial.
	 *
	 * Returns aggregated progress data for all users.
	 *
	 * @since 1.0.0
	 *
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return array Progress statistics.
	 */
	public function get_tutorial_progress_stats( int $tutorial_id ): array {
		global $wpdb;

		$stats = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT 
					COUNT(*) as total_learners,
					COUNT(CASE WHEN status = 'not_started' THEN 1 END) as not_started,
					COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress,
					COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
					AVG(progress_percent) as avg_progress,
					AVG(time_spent) as avg_time_spent
				FROM {$this->table_name}
				WHERE tutorial_id = %d",
				$tutorial_id
			),
			ARRAY_A
		);

		if ( ! $stats ) {
			return array(
				'total_learners'  => 0,
				'not_started'     => 0,
				'in_progress'     => 0,
				'completed'       => 0,
				'avg_progress'    => 0.0,
				'avg_time_spent'  => 0,
			);
		}

		// Convert to appropriate types.
		$stats['total_learners'] = (int) $stats['total_learners'];
		$stats['not_started']    = (int) $stats['not_started'];
		$stats['in_progress']    = (int) $stats['in_progress'];
		$stats['completed']      = (int) $stats['completed'];
		$stats['avg_progress']   = (float) $stats['avg_progress'];
		$stats['avg_time_spent'] = (int) $stats['avg_time_spent'];

		return $stats;
	}

	/**
	 * Get user's overall progress across all tutorials.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array Array of progress records.
	 */
	public function get_user_all_progress( int $user_id ): array {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} 
				WHERE user_id = %d 
				ORDER BY last_accessed DESC",
				$user_id
			)
		);

		return $results ?: array();
	}

	/**
	 * Reset progress for a user in a tutorial.
	 *
	 * Clears all progress while maintaining the record.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id     User ID.
	 * @param int $tutorial_id Tutorial post ID.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function reset_progress( int $user_id, int $tutorial_id ) {
		global $wpdb;

		$result = $wpdb->update(
			$this->table_name,
			array(
				'current_step'     => 0,
				'completed_steps'  => '',
				'progress_percent' => 0.00,
				'status'           => 'not_started',
				'quiz_passed'      => 0,
				'quiz_score'       => null,
				'quiz_attempts'    => 0,
				'completed_at'     => null,
				'time_spent'       => 0,
			),
			array(
				'user_id'     => $user_id,
				'tutorial_id' => $tutorial_id,
			),
			array( '%d', '%s', '%f', '%s', '%d', '%f', '%d', '%s', '%d' ),
			array( '%d', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'database_error',
				__( 'Failed to reset progress.', 'aiddata-lms' ),
				$wpdb->last_error
			);
		}

		// Fire hook.
		do_action( 'aiddata_lms_progress_reset', $user_id, $tutorial_id );

		return true;
	}
}

