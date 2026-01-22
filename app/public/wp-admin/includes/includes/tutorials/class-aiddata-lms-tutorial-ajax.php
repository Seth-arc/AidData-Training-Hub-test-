<?php
/**
 * Tutorial AJAX Handlers
 *
 * Handles AJAX requests for enrollment, unenrollment, progress updates,
 * and time tracking with proper security and error handling.
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
 * Class AidData_LMS_Tutorial_AJAX
 *
 * Manages AJAX endpoints for tutorial enrollment and progress operations.
 *
 * @since 1.0.0
 */
class AidData_LMS_Tutorial_AJAX {

	/**
	 * Constructor.
	 *
	 * Registers all AJAX actions for logged-in and guest users.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Logged-in users
		add_action( 'wp_ajax_aiddata_lms_enroll_tutorial', array( $this, 'enroll_tutorial' ) );
		add_action( 'wp_ajax_aiddata_lms_unenroll_tutorial', array( $this, 'unenroll_tutorial' ) );
		add_action( 'wp_ajax_aiddata_lms_check_enrollment_status', array( $this, 'check_enrollment_status' ) );
		add_action( 'wp_ajax_aiddata_lms_update_step_progress', array( $this, 'update_step_progress' ) );
		add_action( 'wp_ajax_aiddata_lms_update_time_spent', array( $this, 'update_time_spent' ) );
		add_action( 'wp_ajax_aiddata_search_tutorials', array( $this, 'search_tutorials' ) );
		add_action( 'wp_ajax_aiddata_lms_load_step', array( $this, 'load_step' ) );

		// Guest users (if allowing guest preview)
		add_action( 'wp_ajax_nopriv_aiddata_lms_check_enrollment_status', array( $this, 'check_enrollment_status' ) );
	}

	/**
	 * Enroll user in tutorial via AJAX.
	 *
	 * Verifies nonce, validates user login, and enrolls user in tutorial.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enroll_tutorial(): void {
		// Verify nonce
		check_ajax_referer( 'aiddata-lms-enrollment', 'nonce' );

		// Check user login
		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				array( 'message' => __( 'You must be logged in to enroll.', 'aiddata-lms' ) ),
				401
			);
		}

		// Get and validate parameters
		$tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;

		if ( empty( $tutorial_id ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid tutorial ID.', 'aiddata-lms' ) ),
				400
			);
		}

		// Perform enrollment
		$user_id            = get_current_user_id();
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();

		$result = $enrollment_manager->enroll_user( $user_id, $tutorial_id, 'web' );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
					'code'    => $result->get_error_code(),
				),
				400
			);
		}

		// Get enrollment data
		$enrollment = $enrollment_manager->get_enrollment( $user_id, $tutorial_id );

		wp_send_json_success(
			array(
				'message'    => __( 'Successfully enrolled in tutorial.', 'aiddata-lms' ),
				'enrollment' => array(
					'id'          => $enrollment->id,
					'enrolled_at' => $enrollment->enrolled_at,
					'status'      => $enrollment->status,
				),
				'redirect_url' => get_permalink( $tutorial_id ),
			)
		);
	}

	/**
	 * Unenroll user from tutorial via AJAX.
	 *
	 * Verifies nonce, validates user login, and unenrolls user from tutorial.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function unenroll_tutorial(): void {
		check_ajax_referer( 'aiddata-lms-enrollment', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				array( 'message' => __( 'You must be logged in.', 'aiddata-lms' ) ),
				401
			);
		}

		$tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;

		if ( empty( $tutorial_id ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid tutorial ID.', 'aiddata-lms' ) ),
				400
			);
		}

		$user_id            = get_current_user_id();
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();

		// Confirm unenrollment
		$confirm = isset( $_POST['confirm'] ) && $_POST['confirm'] === 'yes';

		if ( ! $confirm ) {
			wp_send_json_error(
				array( 'message' => __( 'Please confirm unenrollment.', 'aiddata-lms' ) ),
				400
			);
		}

		$result = $enrollment_manager->unenroll_user( $user_id, $tutorial_id );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
					'code'    => $result->get_error_code(),
				),
				400
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Successfully unenrolled from tutorial.', 'aiddata-lms' ),
			)
		);
	}

	/**
	 * Check enrollment status via AJAX.
	 *
	 * Returns enrollment and progress information for the current user and tutorial.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function check_enrollment_status(): void {
		$tutorial_id = isset( $_GET['tutorial_id'] ) ? absint( $_GET['tutorial_id'] ) : 0;

		if ( empty( $tutorial_id ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid tutorial ID.', 'aiddata-lms' ) ),
				400
			);
		}

		$user_id            = get_current_user_id();
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();

		$is_enrolled = $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );

		$response = array(
			'enrolled'        => $is_enrolled,
			'user_logged_in'  => is_user_logged_in(),
		);

		if ( $is_enrolled && $user_id > 0 ) {
			$enrollment       = $enrollment_manager->get_enrollment( $user_id, $tutorial_id );
			$progress_manager = new AidData_LMS_Tutorial_Progress();
			$progress         = $progress_manager->get_progress( $user_id, $tutorial_id );

			$response['enrollment'] = array(
				'id'          => $enrollment->id,
				'status'      => $enrollment->status,
				'enrolled_at' => $enrollment->enrolled_at,
			);

			if ( $progress ) {
				$response['progress'] = array(
					'percent'      => $progress->progress_percent,
					'current_step' => $progress->current_step,
					'status'       => $progress->status,
				);
			}
		}

		wp_send_json_success( $response );
	}

	/**
	 * Update step progress via AJAX.
	 *
	 * Verifies nonce, validates enrollment, and updates progress for a specific step.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_step_progress(): void {
		check_ajax_referer( 'aiddata-lms-progress', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				array( 'message' => __( 'You must be logged in.', 'aiddata-lms' ) ),
				401
			);
		}

		$tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;
		$step_index  = isset( $_POST['step_index'] ) ? absint( $_POST['step_index'] ) : -1;

		if ( empty( $tutorial_id ) || $step_index < 0 ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid parameters.', 'aiddata-lms' ) ),
				400
			);
		}

		$user_id = get_current_user_id();

		// Verify enrollment
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		if ( ! $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not enrolled in this tutorial.', 'aiddata-lms' ) ),
				403
			);
		}

		// Update progress
		$progress_manager = new AidData_LMS_Tutorial_Progress();
		$result           = $progress_manager->update_progress( $user_id, $tutorial_id, $step_index );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
					'code'    => $result->get_error_code(),
				),
				400
			);
		}

		// Get updated progress
		$progress = $progress_manager->get_progress( $user_id, $tutorial_id );

		// Check for milestones
		$milestone_checker = new AidData_LMS_Progress_Milestones();
		$milestone         = $milestone_checker->check_milestone( $user_id, $tutorial_id, $progress->progress_percent );

		$response_data = array(
			'message'  => __( 'Progress updated successfully.', 'aiddata-lms' ),
			'progress' => array(
				'percent'         => $progress->progress_percent,
				'current_step'    => $progress->current_step,
				'completed_steps' => $progress_manager->get_completed_steps( $user_id, $tutorial_id ),
				'status'          => $progress->status,
			),
		);

		// Add milestone data if reached
		if ( $milestone ) {
			$response_data['milestone'] = array(
				'reached' => $milestone,
				'message' => $milestone_checker->get_milestone_message( $milestone ),
				'details' => $milestone_checker->get_milestone_details( $milestone ),
			);
		}

		wp_send_json_success( $response_data );
	}

	/**
	 * Update time spent via AJAX.
	 *
	 * Verifies nonce and accumulates time spent on a tutorial.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_time_spent(): void {
		check_ajax_referer( 'aiddata-lms-progress', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				array( 'message' => __( 'You must be logged in.', 'aiddata-lms' ) ),
				401
			);
		}

		$tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;
		$seconds     = isset( $_POST['seconds'] ) ? absint( $_POST['seconds'] ) : 0;

		if ( empty( $tutorial_id ) || $seconds <= 0 ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid parameters.', 'aiddata-lms' ) ),
				400
			);
		}

		$user_id          = get_current_user_id();
		$progress_manager = new AidData_LMS_Tutorial_Progress();

		$result = $progress_manager->update_time_spent( $user_id, $tutorial_id, $seconds );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
				),
				400
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Time updated.', 'aiddata-lms' ),
			)
		);
	}

	/**
	 * Search tutorials via AJAX.
	 *
	 * Searches tutorials by title for use in prerequisites selector.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function search_tutorials(): void {
		// Get search query
		$query = isset( $_GET['query'] ) ? sanitize_text_field( $_GET['query'] ) : '';

		if ( strlen( $query ) < 2 ) {
			wp_send_json_error(
				array( 'message' => __( 'Search query too short.', 'aiddata-lms' ) ),
				400
			);
		}

		// Get excluded IDs
		$exclude = isset( $_GET['exclude'] ) ? array_map( 'absint', (array) $_GET['exclude'] ) : array();

		// Query tutorials
		$args = array(
			'post_type'      => 'aiddata_tutorial',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => 10,
			's'              => $query,
			'post__not_in'   => $exclude,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$tutorials_query = new WP_Query( $args );
		$tutorials       = array();

		if ( $tutorials_query->have_posts() ) {
			while ( $tutorials_query->have_posts() ) {
				$tutorials_query->the_post();
				$post_id = get_the_ID();

				$short_desc = get_post_meta( $post_id, '_tutorial_short_description', true );
				$excerpt    = ! empty( $short_desc ) ? wp_trim_words( $short_desc, 15 ) : '';

				$tutorials[] = array(
					'id'      => $post_id,
					'title'   => get_the_title(),
					'excerpt' => $excerpt,
				);
			}
			wp_reset_postdata();
		}

		wp_send_json_success(
			array(
				'tutorials' => $tutorials,
			)
		);
	}

	/**
	 * Load tutorial step content via AJAX.
	 *
	 * Verifies nonce, validates enrollment, and returns step content HTML.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function load_step(): void {
		// Verify nonce
		check_ajax_referer( 'aiddata-lms-progress', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				array( 'message' => __( 'You must be logged in.', 'aiddata-lms' ) ),
				401
			);
		}

		$tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;
		$step_index  = isset( $_POST['step_index'] ) ? absint( $_POST['step_index'] ) : -1;

		if ( empty( $tutorial_id ) || $step_index < 0 ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid parameters.', 'aiddata-lms' ) ),
				400
			);
		}

		$user_id = get_current_user_id();

		// Verify enrollment
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		if ( ! $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not enrolled in this tutorial.', 'aiddata-lms' ) ),
				403
			);
		}

		// Get tutorial steps
		$steps = get_post_meta( $tutorial_id, '_tutorial_steps', true );
		if ( ! isset( $steps[ $step_index ] ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid step index.', 'aiddata-lms' ) ),
				404
			);
		}

		$step = $steps[ $step_index ];

		// Update current step in progress
		$progress_manager = new AidData_LMS_Tutorial_Progress();
		$progress_manager->set_current_step( $user_id, $tutorial_id, $step_index );

		// Render step content
		$renderer = new AidData_LMS_Step_Renderer();
		$html     = $renderer->render_step_content( $step, $step_index, $tutorial_id );

		wp_send_json_success(
			array(
				'html' => $html,
				'step' => array(
					'index'    => $step_index,
					'title'    => $step['title'],
					'type'     => isset( $step['type'] ) ? $step['type'] : 'text',
					'required' => isset( $step['required'] ) ? $step['required'] : true,
				),
			)
		);
	}
}

