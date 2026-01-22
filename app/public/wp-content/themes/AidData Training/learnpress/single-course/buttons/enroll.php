<?php
/**
 * Template for displaying Enroll button in single course.
 *
 * This template has been customized to force explicit button styles and
 * allow for potential future direct-linking logic if needed, similar to the continue button.
 *
 * @author  ThimPress (Modified for AidData Training)
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $course ) ) {
	$course = learn_press_get_course();
}
?>

<?php do_action( 'learn-press/before-enroll-form' ); ?>

<form name="enroll-course" class="enroll-course form-button lp-form" method="post" enctype="multipart/form-data">

	<?php do_action( 'learn-press/before-enroll-button' ); ?>

	<input type="hidden" name="enroll-course" value="<?php echo esc_attr( $course->get_id() ); ?>"/>
	<input type="hidden" name="enroll-course-nonce" value="<?php echo esc_attr( wp_create_nonce( 'enroll-course-' . $course->get_id() ) ); ?>"/>

    <!-- Fix: added type="submit" explicitly and full-width styling inline to prevent any JS/CSS overriding issues -->
	<button type="submit" class="lp-button button button-enroll-course" style="display: block; width: 100%; border-radius: 6px; padding: 1rem; background-color: #004E38; color: white; border: none; font-weight: 600; cursor: pointer;">
		<?php echo esc_html( apply_filters( 'learn-press/enroll-course-button-text', esc_html__( 'Start Now', 'learnpress' ) ) ); ?>
	</button>

	<div class="lp-ajax-message"></div>

	<?php do_action( 'learn-press/after-enroll-button' ); ?>

</form>

<?php do_action( 'learn-press/after-enroll-form' ); ?>
