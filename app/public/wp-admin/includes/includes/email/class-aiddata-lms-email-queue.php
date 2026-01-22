<?php
/**
 * Email Queue Manager
 *
 * Handles email queueing, processing, priority management, scheduling,
 * retry logic, and batch processing for the AidData LMS system.
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
 * Email Queue Manager Class
 *
 * Manages email queue operations including adding emails to queue,
 * processing batches, handling retries, and cleanup.
 *
 * @since 1.0.0
 */
class AidData_LMS_Email_Queue {

	/**
	 * Email queue table name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name;

	/**
	 * Maximum retry attempts
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $max_attempts = 3;

	/**
	 * Constructor
	 *
	 * Initializes the email queue manager, sets up table name,
	 * and registers WP-Cron hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->table_name = AidData_LMS_Database::get_table_name( 'email' );

		// Schedule cron if not already scheduled.
		if ( ! wp_next_scheduled( 'aiddata_lms_process_email_queue' ) ) {
			wp_schedule_event( time(), 'aiddata_lms_five_minutes', 'aiddata_lms_process_email_queue' );
		}

		// Hook to cron event.
		add_action( 'aiddata_lms_process_email_queue', array( $this, 'process_queue' ) );

		// Add custom cron schedule.
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );
	}

	/**
	 * Add email to queue
	 *
	 * Validates email address, prepares data with options, and inserts
	 * into email queue with priority and scheduling support.
	 *
	 * @since 1.0.0
	 *
	 * @param string $recipient_email Email address of recipient.
	 * @param string $subject         Email subject line.
	 * @param string $message         Email message body (HTML).
	 * @param string $email_type      Type of email (e.g., 'enrollment_confirmation').
	 * @param array  $options         Optional settings (recipient_name, user_id, template_id, template_data, priority, scheduled_for).
	 * @return int|WP_Error Email ID on success, WP_Error on failure.
	 */
	public function add_to_queue( 
		string $recipient_email, 
		string $subject, 
		string $message, 
		string $email_type, 
		array $options = [] 
	) {
		global $wpdb;

		// Validate email.
		if ( ! is_email( $recipient_email ) ) {
			return new WP_Error( 'invalid_email', __( 'Invalid email address.', 'aiddata-lms' ) );
		}

		// Parse options.
		$defaults = array(
			'recipient_name' => '',
			'user_id'        => 0,
			'template_id'    => null,
			'template_data'  => array(),
			'priority'       => 5, // 1-10, 1 = highest.
			'scheduled_for'  => null,
		);

		$options = wp_parse_args( $options, $defaults );

		// Prepare data.
		$data = array(
			'recipient_email' => sanitize_email( $recipient_email ),
			'recipient_name'  => sanitize_text_field( $options['recipient_name'] ),
			'user_id'         => absint( $options['user_id'] ),
			'subject'         => sanitize_text_field( $subject ),
			'message'         => wp_kses_post( $message ),
			'email_type'      => sanitize_key( $email_type ),
			'template_id'     => $options['template_id'] ? sanitize_key( $options['template_id'] ) : null,
			'template_data'   => ! empty( $options['template_data'] ) ? wp_json_encode( $options['template_data'] ) : null,
			'priority'        => min( max( absint( $options['priority'] ), 1 ), 10 ),
			'status'          => 'pending',
			'attempts'        => 0,
			'scheduled_for'   => $options['scheduled_for'],
		);

		$format = array( '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s' );

		// Insert into queue.
		$result = $wpdb->insert( $this->table_name, $data, $format );

		if ( $result === false ) {
			error_log( sprintf( 'Failed to queue email: %s', $wpdb->last_error ) );
			return new WP_Error( 'db_error', __( 'Failed to queue email.', 'aiddata-lms' ), $wpdb->last_error );
		}

		$email_id = $wpdb->insert_id;

		// Fire action.
		do_action( 'aiddata_lms_email_queued', $email_id, $email_type, $recipient_email );

		return $email_id;
	}

	/**
	 * Process email queue
	 *
	 * Retrieves pending emails from queue ordered by priority and scheduled time,
	 * attempts to send each email, handles retries, and updates status.
	 *
	 * @since 1.0.0
	 *
	 * @param int $batch_size Number of emails to process in this batch. Default 10.
	 * @return array Results summary with counts of sent, failed, and skipped emails.
	 */
	public function process_queue( int $batch_size = 10 ): array {
		global $wpdb;

		// Get pending emails ordered by priority and scheduled time.
		$emails = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} 
				WHERE status = 'pending' 
				AND (scheduled_for IS NULL OR scheduled_for <= NOW())
				ORDER BY priority ASC, created_at ASC
				LIMIT %d",
				$batch_size
			)
		);

		$results = array(
			'sent'    => 0,
			'failed'  => 0,
			'skipped' => 0,
		);

		foreach ( $emails as $email ) {
			// Update status to processing.
			$wpdb->update(
				$this->table_name,
				array( 'status' => 'processing' ),
				array( 'id' => $email->id ),
				array( '%s' ),
				array( '%d' )
			);

			// Attempt to send.
			$sent = $this->send_email( $email );

			if ( $sent ) {
				$this->mark_as_sent( $email->id );
				$results['sent']++;
			} else {
				// Check if we should retry.
				$attempts = $email->attempts + 1;

				if ( $attempts < $this->max_attempts ) {
					// Retry later.
					$wpdb->update(
						$this->table_name,
						array(
							'status'       => 'pending',
							'attempts'     => $attempts,
							'last_attempt' => current_time( 'mysql' ),
						),
						array( 'id' => $email->id ),
						array( '%s', '%d', '%s' ),
						array( '%d' )
					);
					$results['skipped']++;
				} else {
					// Max attempts reached.
					$this->mark_as_failed( $email->id, 'Maximum retry attempts reached' );
					$results['failed']++;
				}
			}
		}

		do_action( 'aiddata_lms_queue_processed', $results );

		return $results;
	}

	/**
	 * Retry failed emails
	 *
	 * Resets failed emails back to pending status for another attempt.
	 * Only retries emails that haven't exceeded max attempts.
	 *
	 * @since 1.0.0
	 *
	 * @param int $max_attempts Maximum number of attempts allowed. Default 3.
	 * @return int Number of emails reset for retry.
	 */
	public function retry_failed( int $max_attempts = 3 ): int {
		global $wpdb;

		$updated = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$this->table_name} 
				SET status = 'pending', attempts = 0 
				WHERE status = 'failed' 
				AND attempts < %d",
				$max_attempts
			)
		);

		return $updated ?: 0;
	}

	/**
	 * Get queue statistics
	 *
	 * Retrieves counts of emails in each status for monitoring
	 * and dashboard display.
	 *
	 * @since 1.0.0
	 *
	 * @return array Statistics with pending, processing, sent, failed, and total counts.
	 */
	public function get_queue_stats(): array {
		global $wpdb;

		$stats = $wpdb->get_row(
			"SELECT 
				COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
				COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing,
				COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent,
				COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed,
				COUNT(*) as total
			FROM {$this->table_name}",
			ARRAY_A
		);

		return $stats ?: array(
			'pending'    => 0,
			'processing' => 0,
			'sent'       => 0,
			'failed'     => 0,
			'total'      => 0,
		);
	}

	/**
	 * Get pending emails
	 *
	 * Retrieves emails that are ready to be sent (pending status
	 * and scheduled time has passed).
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit Maximum number of emails to retrieve. Default 10.
	 * @return array Array of email objects.
	 */
	public function get_pending_emails( int $limit = 10 ): array {
		global $wpdb;

		$emails = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} 
				WHERE status = 'pending' 
				AND (scheduled_for IS NULL OR scheduled_for <= NOW())
				ORDER BY priority ASC, created_at ASC
				LIMIT %d",
				$limit
			)
		);

		return $emails ?: array();
	}

	/**
	 * Mark email as sent
	 *
	 * Updates email status to 'sent' and sets sent timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @param int $email_id Email record ID.
	 * @return bool True on success, false on failure.
	 */
	public function mark_as_sent( int $email_id ): bool {
		global $wpdb;

		$result = $wpdb->update(
			$this->table_name,
			array(
				'status'  => 'sent',
				'sent_at' => current_time( 'mysql' ),
			),
			array( 'id' => $email_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		do_action( 'aiddata_lms_email_sent', $email_id );

		return $result !== false;
	}

	/**
	 * Mark email as failed
	 *
	 * Updates email status to 'failed' and stores error message.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $email_id      Email record ID.
	 * @param string $error_message Error message to store.
	 * @return bool True on success, false on failure.
	 */
	public function mark_as_failed( int $email_id, string $error_message ): bool {
		global $wpdb;

		$result = $wpdb->update(
			$this->table_name,
			array(
				'status'        => 'failed',
				'error_message' => sanitize_textarea_field( $error_message ),
				'last_attempt'  => current_time( 'mysql' ),
			),
			array( 'id' => $email_id ),
			array( '%s', '%s', '%s' ),
			array( '%d' )
		);

		do_action( 'aiddata_lms_email_failed', $email_id, $error_message );

		return $result !== false;
	}

	/**
	 * Delete old emails
	 *
	 * Removes sent emails older than specified number of days
	 * to keep database clean.
	 *
	 * @since 1.0.0
	 *
	 * @param int $days Number of days to keep. Default 30.
	 * @return int Number of emails deleted.
	 */
	public function delete_old_emails( int $days = 30 ): int {
		global $wpdb;

		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table_name} 
				WHERE status = 'sent' 
				AND sent_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days
			)
		);

		return $deleted ?: 0;
	}

	/**
	 * Send email
	 *
	 * Attempts to send an email using WordPress wp_mail function.
	 * Applies filters for customization and logs errors.
	 *
	 * @since 1.0.0
	 *
	 * @param object $email Email object from database.
	 * @return bool True on success, false on failure.
	 */
	private function send_email( object $email ): bool {
		global $wpdb;

		// Apply filters for customization.
		$to      = apply_filters( 'aiddata_lms_email_to', $email->recipient_email, $email );
		$subject = apply_filters( 'aiddata_lms_email_subject', $email->subject, $email );
		$message = apply_filters( 'aiddata_lms_email_message', $email->message, $email );

		// Set headers.
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);

		if ( ! empty( $email->recipient_name ) ) {
			$to = sprintf( '%s <%s>', $email->recipient_name, $email->recipient_email );
		}

		$headers = apply_filters( 'aiddata_lms_email_headers', $headers, $email );

		// Send email.
		$sent = wp_mail( $to, $subject, $message, $headers );

		if ( ! $sent ) {
			// Log error.
			error_log( sprintf( 'Failed to send email ID %d to %s', $email->id, $email->recipient_email ) );

			// Update attempt.
			$wpdb->update(
				$this->table_name,
				array(
					'attempts'      => $email->attempts + 1,
					'last_attempt'  => current_time( 'mysql' ),
					'error_message' => 'wp_mail() returned false',
				),
				array( 'id' => $email->id ),
				array( '%d', '%s', '%s' ),
				array( '%d' )
			);
		}

		return $sent;
	}

	/**
	 * Add custom cron schedule
	 *
	 * Adds a 5-minute interval schedule for email queue processing.
	 *
	 * @since 1.0.0
	 *
	 * @param array $schedules Existing cron schedules.
	 * @return array Modified schedules array.
	 */
	public function add_cron_schedule( array $schedules ): array {
		$schedules['aiddata_lms_five_minutes'] = array(
			'interval' => 5 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 5 Minutes', 'aiddata-lms' ),
		);

		return $schedules;
	}
}

