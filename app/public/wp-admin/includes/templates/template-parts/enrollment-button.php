<?php
/**
 * Enrollment Button Template Part
 *
 * Displays enrollment widget with different states based on user status.
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tutorial_id = get_the_ID();
$user_id = get_current_user_id();

// Get managers
$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();

// Get enrollment settings
$allow_enrollment = get_post_meta( $tutorial_id, '_tutorial_allow_enrollment', true );
$enrollment_limit = get_post_meta( $tutorial_id, '_tutorial_enrollment_limit', true );
$enrollment_deadline = get_post_meta( $tutorial_id, '_tutorial_enrollment_deadline', true );

// Check if user is logged in
if ( ! $user_id ) {
	?>
	<div class="enrollment-widget guest-user">
		<p class="enrollment-message"><?php esc_html_e( 'Please log in to enroll in this tutorial.', 'aiddata-lms' ); ?></p>
		<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="button button-primary button-large">
			<?php esc_html_e( 'Log In to Enroll', 'aiddata-lms' ); ?>
		</a>
		<p class="register-link">
			<?php
			/* translators: %s: registration URL */
			printf(
				wp_kses_post( __( 'Don\'t have an account? <a href="%s">Register here</a>', 'aiddata-lms' ) ),
				esc_url( wp_registration_url() )
			);
			?>
		</p>
	</div>
	<?php
	return;
}

// Check enrollment status
$is_enrolled = $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );

if ( $is_enrolled ) {
	// User is already enrolled
	$enrollment = $enrollment_manager->get_enrollment( $user_id, $tutorial_id );
	$status = $enrollment ? $enrollment->status : 'active';
	
	?>
	<div class="enrollment-widget enrolled-user" data-status="<?php echo esc_attr( $status ); ?>">
		<div class="enrollment-status">
			<span class="status-icon dashicons dashicons-yes-alt"></span>
			<span class="status-text"><?php esc_html_e( 'You are enrolled', 'aiddata-lms' ); ?></span>
		</div>
		
		<?php if ( 'completed' === $status ) : ?>
			<p class="enrollment-message success">
				<?php esc_html_e( 'Congratulations! You have completed this tutorial.', 'aiddata-lms' ); ?>
			</p>
			<a href="<?php echo esc_url( get_permalink() . '?action=continue' ); ?>" class="button button-secondary button-large">
				<?php esc_html_e( 'Review Tutorial', 'aiddata-lms' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( get_permalink() . '?action=continue' ); ?>" class="button button-primary button-large">
				<?php esc_html_e( 'Continue Learning', 'aiddata-lms' ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
	return;
}

// Check if enrollment is allowed
if ( ! $allow_enrollment ) {
	?>
	<div class="enrollment-widget enrollment-closed">
		<p class="enrollment-message error">
			<span class="dashicons dashicons-lock"></span>
			<?php esc_html_e( 'Enrollment is currently closed for this tutorial.', 'aiddata-lms' ); ?>
		</p>
	</div>
	<?php
	return;
}

// Check enrollment deadline
if ( ! empty( $enrollment_deadline ) ) {
	$deadline_date = strtotime( $enrollment_deadline );
	if ( $deadline_date && $deadline_date < current_time( 'timestamp' ) ) {
		?>
		<div class="enrollment-widget enrollment-deadline-passed">
			<p class="enrollment-message error">
				<span class="dashicons dashicons-calendar-alt"></span>
				<?php esc_html_e( 'The enrollment deadline for this tutorial has passed.', 'aiddata-lms' ); ?>
			</p>
		</div>
		<?php
		return;
	}
}

// Check enrollment limit
if ( ! empty( $enrollment_limit ) && $enrollment_limit > 0 ) {
	$current_enrollments = $enrollment_manager->get_enrollment_count( $tutorial_id );
	if ( $current_enrollments >= $enrollment_limit ) {
		?>
		<div class="enrollment-widget enrollment-full">
			<p class="enrollment-message error">
				<span class="dashicons dashicons-groups"></span>
				<?php esc_html_e( 'This tutorial has reached its enrollment limit.', 'aiddata-lms' ); ?>
			</p>
		</div>
		<?php
		return;
	}
}

// User can enroll - show enrollment button
?>
<div class="enrollment-widget can-enroll">
	<form method="post" class="enrollment-form" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
		<?php wp_nonce_field( 'aiddata_enroll_tutorial', 'enrollment_nonce' ); ?>
		<input type="hidden" name="tutorial_id" value="<?php echo esc_attr( $tutorial_id ); ?>">
		<input type="hidden" name="action" value="aiddata_lms_enroll">
		
		<button type="submit" class="button button-primary button-large enroll-button">
			<span class="button-text"><?php esc_html_e( 'Enroll Now', 'aiddata-lms' ); ?></span>
			<span class="button-loader" style="display: none;">
				<span class="dashicons dashicons-update spin"></span>
			</span>
		</button>
		
		<div class="enrollment-feedback" role="alert" aria-live="polite"></div>
	</form>
	
	<?php if ( ! empty( $enrollment_deadline ) ) : ?>
		<p class="enrollment-deadline-info">
			<?php
			/* translators: %s: deadline date */
			printf(
				esc_html__( 'Enrollment closes: %s', 'aiddata-lms' ),
				esc_html( date_i18n( get_option( 'date_format' ), strtotime( $enrollment_deadline ) ) )
			);
			?>
		</p>
	<?php endif; ?>
	
	<?php if ( ! empty( $enrollment_limit ) && $enrollment_limit > 0 ) : ?>
		<?php
		$current_enrollments = $enrollment_manager->get_enrollment_count( $tutorial_id );
		$spots_left = $enrollment_limit - $current_enrollments;
		?>
		<p class="enrollment-spots-info">
			<?php
			/* translators: %d: number of spots remaining */
			printf(
				esc_html( _n( '%d spot remaining', '%d spots remaining', $spots_left, 'aiddata-lms' ) ),
				$spots_left
			);
			?>
		</p>
	<?php endif; ?>
</div>
