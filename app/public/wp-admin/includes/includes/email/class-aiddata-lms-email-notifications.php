<?php
/**
 * Email Notification Triggers
 *
 * Handles automated email notifications for enrollment, progress, and
 * completion events in the AidData LMS system.
 *
 * @package AidData_LMS
 * @subpackage Email
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email Notification Triggers Class
 *
 * Listens to plugin events and triggers email notifications using
 * templates and the email queue system.
 *
 * @since 1.0.0
 */
class AidData_LMS_Email_Notifications {

	/**
	 * Template manager instance
	 *
	 * @since 1.0.0
	 * @var AidData_LMS_Email_Templates
	 */
	private $template_manager;

	/**
	 * Queue manager instance
	 *
	 * @since 1.0.0
	 * @var AidData_LMS_Email_Queue
	 */
	private $queue_manager;

	/**
	 * Constructor
	 *
	 * Initializes template and queue managers, and registers event hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->template_manager = new AidData_LMS_Email_Templates();
		$this->queue_manager    = new AidData_LMS_Email_Queue();

		$this->register_hooks();
	}

	/**
	 * Register event hooks
	 *
	 * Registers WordPress hooks to listen for enrollment, progress,
	 * and completion events.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks(): void {
		// Enrollment events.
		add_action( 'aiddata_lms_user_enrolled', array( $this, 'on_user_enrolled' ), 10, 4 );

		// Progress events.
		add_action( 'aiddata_lms_progress_updated', array( $this, 'on_progress_updated' ), 10, 3 );

		// Completion events.
		add_action( 'aiddata_lms_tutorial_completed', array( $this, 'on_tutorial_completed' ), 10, 3 );

		// Certificate events (for future use).
		add_action( 'aiddata_lms_certificate_generated', array( $this, 'on_certificate_generated' ), 10, 3 );
	}

	/**
	 * Handle user enrollment event
	 *
	 * Sends enrollment confirmation email when user enrolls in a tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $enrollment_id Enrollment record ID.
	 * @param int    $user_id       User ID.
	 * @param int    $tutorial_id   Tutorial ID.
	 * @param string $source        Enrollment source (e.g., 'web', 'api').
	 */
	public function on_user_enrolled( 
		int $enrollment_id, 
		int $user_id, 
		int $tutorial_id, 
		string $source 
	): void {
		$user     = get_userdata( $user_id );
		$tutorial = get_post( $tutorial_id );

		if ( ! $user || ! $tutorial ) {
			error_log( sprintf( 'Email notification failed: Invalid user (%d) or tutorial (%d)', $user_id, $tutorial_id ) );
			return;
		}

		// Prepare variables.
		$variables = array(
			'{user_name}'            => $user->display_name,
			'{user_email}'           => $user->user_email,
			'{user_first_name}'      => ! empty( $user->first_name ) ? $user->first_name : $user->display_name,
			'{user_last_name}'       => $user->last_name,
			'{tutorial_title}'       => $tutorial->post_title,
			'{tutorial_url}'         => get_permalink( $tutorial_id ),
			'{tutorial_description}' => wp_trim_words( $tutorial->post_excerpt, 30 ),
			'{enrolled_date}'        => wp_date( get_option( 'date_format' ) ),
		);

		// Allow filtering of variables.
		$variables = apply_filters( 'aiddata_lms_enrollment_email_variables', $variables, $user_id, $tutorial_id );

		// Render template.
		$message = $this->template_manager->render_template( 'enrollment-confirmation', $variables );

		if ( empty( $message ) ) {
			error_log( sprintf( 'Email template rendering failed: enrollment-confirmation for user %d', $user_id ) );
			return;
		}

		// Queue email.
		$result = $this->queue_manager->add_to_queue(
			$user->user_email,
			sprintf( 
				/* translators: %s: tutorial title */
				__( 'Welcome to %s', 'aiddata-lms' ), 
				$tutorial->post_title 
			),
			$message,
			'enrollment_confirmation',
			array(
				'recipient_name' => $user->display_name,
				'user_id'        => $user_id,
				'priority'       => 3, // High priority.
			)
		);

		if ( is_wp_error( $result ) ) {
			error_log( sprintf( 'Failed to queue enrollment email: %s', $result->get_error_message() ) );
		}
	}

	/**
	 * Handle progress update event
	 *
	 * Sends progress reminder emails at key milestones (25%, 50%, 75%).
	 *
	 * @since 1.0.0
	 *
	 * @param int   $user_id         User ID.
	 * @param int   $tutorial_id     Tutorial ID.
	 * @param float $progress_percent Progress percentage.
	 */
	public function on_progress_updated( 
		int $user_id, 
		int $tutorial_id, 
		float $progress_percent 
	): void {
		// Send reminder at specific milestones.
		$milestones      = array( 25, 50, 75 );
		$rounded_progress = round( $progress_percent );

		if ( ! in_array( $rounded_progress, $milestones, true ) ) {
			return;
		}

		// Check if already sent for this milestone.
		$sent_meta_key = '_aiddata_lms_progress_email_' . $rounded_progress;
		$already_sent  = get_user_meta( $user_id, $sent_meta_key . '_' . $tutorial_id, true );

		if ( $already_sent ) {
			return;
		}

		$user     = get_userdata( $user_id );
		$tutorial = get_post( $tutorial_id );

		if ( ! $user || ! $tutorial ) {
			return;
		}

		// Prepare variables.
		$variables = array(
			'{user_first_name}'  => ! empty( $user->first_name ) ? $user->first_name : $user->display_name,
			'{tutorial_title}'   => $tutorial->post_title,
			'{tutorial_url}'     => get_permalink( $tutorial_id ),
			'{progress_percent}' => $rounded_progress,
		);

		// Allow filtering of variables.
		$variables = apply_filters( 'aiddata_lms_progress_email_variables', $variables, $user_id, $tutorial_id );

		// Render template.
		$message = $this->template_manager->render_template( 'progress-reminder', $variables );

		if ( empty( $message ) ) {
			return;
		}

		// Queue email.
		$result = $this->queue_manager->add_to_queue(
			$user->user_email,
			sprintf( 
				/* translators: 1: progress percentage, 2: tutorial title */
				__( 'You\'re %1$d%% through %2$s!', 'aiddata-lms' ), 
				$rounded_progress, 
				$tutorial->post_title 
			),
			$message,
			'progress_reminder',
			array(
				'recipient_name' => $user->display_name,
				'user_id'        => $user_id,
				'priority'       => 5, // Normal priority.
			)
		);

		if ( ! is_wp_error( $result ) ) {
			// Mark as sent.
			update_user_meta( $user_id, $sent_meta_key . '_' . $tutorial_id, time() );
		}
	}

	/**
	 * Handle tutorial completion event
	 *
	 * Sends congratulations email when user completes a tutorial.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id       User ID.
	 * @param int $tutorial_id   Tutorial ID.
	 * @param int $enrollment_id Enrollment record ID.
	 */
	public function on_tutorial_completed( 
		int $user_id, 
		int $tutorial_id, 
		int $enrollment_id 
	): void {
		$user     = get_userdata( $user_id );
		$tutorial = get_post( $tutorial_id );

		if ( ! $user || ! $tutorial ) {
			return;
		}

		// Prepare variables.
		$variables = array(
			'{user_first_name}'  => ! empty( $user->first_name ) ? $user->first_name : $user->display_name,
			'{tutorial_title}'   => $tutorial->post_title,
			'{completion_date}'  => wp_date( get_option( 'date_format' ) ),
			'{certificate_url}'  => home_url( '/certificates/' . $enrollment_id ), // Placeholder for future certificate system.
		);

		// Allow filtering of variables.
		$variables = apply_filters( 'aiddata_lms_completion_email_variables', $variables, $user_id, $tutorial_id );

		// Render template.
		$message = $this->template_manager->render_template( 'completion-congratulations', $variables );

		if ( empty( $message ) ) {
			error_log( sprintf( 'Email template rendering failed: completion-congratulations for user %d', $user_id ) );
			return;
		}

		// Queue email.
		$result = $this->queue_manager->add_to_queue(
			$user->user_email,
			sprintf( 
				/* translators: %s: tutorial title */
				__( 'Congratulations on completing %s!', 'aiddata-lms' ), 
				$tutorial->post_title 
			),
			$message,
			'completion_congratulations',
			array(
				'recipient_name' => $user->display_name,
				'user_id'        => $user_id,
				'priority'       => 2, // High priority.
			)
		);

		if ( is_wp_error( $result ) ) {
			error_log( sprintf( 'Failed to queue completion email: %s', $result->get_error_message() ) );
		}
	}

	/**
	 * Handle certificate generation event
	 *
	 * Sends email with certificate download link (for future use).
	 *
	 * @since 1.0.0
	 *
	 * @param int    $user_id        User ID.
	 * @param int    $tutorial_id    Tutorial ID.
	 * @param string $certificate_id Certificate ID.
	 */
	public function on_certificate_generated( 
		int $user_id, 
		int $tutorial_id, 
		string $certificate_id 
	): void {
		// Placeholder for future certificate system.
		// This hook is registered but not yet fully implemented.
		do_action( 'aiddata_lms_certificate_email_triggered', $user_id, $tutorial_id, $certificate_id );
	}
}

