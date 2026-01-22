<?php
/**
 * Progress Milestones
 *
 * Handles milestone detection and celebration messages for tutorial progress.
 *
 * @package AidData_LMS
 * @subpackage Tutorials
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Progress Milestones Class
 *
 * Tracks and celebrates user progress milestones.
 *
 * @since 1.0.0
 */
class AidData_LMS_Progress_Milestones {

	/**
	 * Milestone percentages
	 *
	 * @var array
	 */
	private $milestones = array( 25, 50, 75, 100 );

	/**
	 * Check if user has reached a new milestone
	 *
	 * @param int   $user_id User ID.
	 * @param int   $tutorial_id Tutorial ID.
	 * @param float $new_percent New progress percentage.
	 * @return int|null Milestone reached or null.
	 */
	public function check_milestone( int $user_id, int $tutorial_id, float $new_percent ): ?int {
		foreach ( $this->milestones as $milestone ) {
			$meta_key = "_tutorial_{$tutorial_id}_milestone_{$milestone}";
			$reached  = get_user_meta( $user_id, $meta_key, true );

			if ( ! $reached && $new_percent >= $milestone ) {
				// Mark milestone as reached
				update_user_meta( $user_id, $meta_key, current_time( 'mysql' ) );

				// Fire action for extensibility
				do_action( 'aiddata_lms_milestone_reached', $user_id, $tutorial_id, $milestone );

				return $milestone;
			}
		}

		return null;
	}

	/**
	 * Get milestone celebration message
	 *
	 * @param int $milestone Milestone percentage.
	 * @return string Celebration message.
	 */
	public function get_milestone_message( int $milestone ): string {
		$messages = array(
			25  => __( '🎉 You\'re 25% complete! Keep going!', 'aiddata-lms' ),
			50  => __( '🌟 Halfway there! You\'re doing great!', 'aiddata-lms' ),
			75  => __( '🚀 75% complete! You\'re almost done!', 'aiddata-lms' ),
			100 => __( '🏆 Congratulations! You\'ve completed the tutorial!', 'aiddata-lms' ),
		);

		return isset( $messages[ $milestone ] ) ? $messages[ $milestone ] : '';
	}

	/**
	 * Get milestone details
	 *
	 * @param int $milestone Milestone percentage.
	 * @return array Milestone details.
	 */
	public function get_milestone_details( int $milestone ): array {
		$details = array(
			25  => array(
				'icon'  => '🎉',
				'color' => '#667eea',
				'title' => __( 'Great Start!', 'aiddata-lms' ),
			),
			50  => array(
				'icon'  => '🌟',
				'color' => '#764ba2',
				'title' => __( 'Halfway There!', 'aiddata-lms' ),
			),
			75  => array(
				'icon'  => '🚀',
				'color' => '#f093fb',
				'title' => __( 'Almost Done!', 'aiddata-lms' ),
			),
			100 => array(
				'icon'  => '🏆',
				'color' => '#46b450',
				'title' => __( 'Completed!', 'aiddata-lms' ),
			),
		);

		return isset( $details[ $milestone ] ) ? $details[ $milestone ] : array();
	}

	/**
	 * Reset all milestones for a user and tutorial
	 *
	 * Useful when resetting progress or when user re-takes a tutorial.
	 *
	 * @param int $user_id User ID.
	 * @param int $tutorial_id Tutorial ID.
	 */
	public function reset_milestones( int $user_id, int $tutorial_id ): void {
		foreach ( $this->milestones as $milestone ) {
			delete_user_meta( $user_id, "_tutorial_{$tutorial_id}_milestone_{$milestone}" );
		}
	}

	/**
	 * Get all reached milestones for a user and tutorial
	 *
	 * @param int $user_id User ID.
	 * @param int $tutorial_id Tutorial ID.
	 * @return array Array of reached milestones with timestamps.
	 */
	public function get_reached_milestones( int $user_id, int $tutorial_id ): array {
		$reached = array();

		foreach ( $this->milestones as $milestone ) {
			$meta_key = "_tutorial_{$tutorial_id}_milestone_{$milestone}";
			$timestamp = get_user_meta( $user_id, $meta_key, true );

			if ( $timestamp ) {
				$reached[ $milestone ] = $timestamp;
			}
		}

		return $reached;
	}

	/**
	 * Check if specific milestone was reached
	 *
	 * @param int $user_id User ID.
	 * @param int $tutorial_id Tutorial ID.
	 * @param int $milestone Milestone percentage to check.
	 * @return bool True if reached, false otherwise.
	 */
	public function is_milestone_reached( int $user_id, int $tutorial_id, int $milestone ): bool {
		$meta_key = "_tutorial_{$tutorial_id}_milestone_{$milestone}";
		return (bool) get_user_meta( $user_id, $meta_key, true );
	}
}
