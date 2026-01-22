<?php
/**
 * Template for displaying Purchase button in single course.
 *
 * This template has been customized to force explicit button styles and ensure form submission works
 * correctly with the custom theme layout.
 *
 * @author  ThimPress (Modified for AidData Training)
 * @package LearnPress/Templates
 * @version 4.0.1
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $course ) ) {
	$course = learn_press_get_course();
}

$classes_purchase  = 'purchase-course form-button lp-form';
$classes_purchase .= ( LearnPress::instance()->checkout()->is_enable_guest_checkout() ) ? ' guest_checkout' : '';

$classes_purchase = apply_filters( 'lp/btn/purchase/classes', $classes_purchase );
?>

<?php do_action( 'learn-press/before-purchase-form' ); ?>

<form name="purchase-course" class="<?php echo esc_attr( $classes_purchase ); ?>" method="post" enctype="multipart/form-data">

	<?php do_action( 'learn-press/before-purchase-button' ); ?>

	<input type="hidden" name="purchase-course" value="<?php echo esc_attr( $course->get_id() ); ?>"/>
	
    <!-- Fix: added type="submit" explicitly and full-width styling inline -->
	<button type="submit" class="lp-button button button-purchase-course" style="display: block; width: 100%; border-radius: 6px; padding: 1rem; background-color: #004E38; color: white; border: none; font-weight: 600; cursor: pointer;">
		<?php echo esc_html( apply_filters( 'learn-press/purchase-course-button-text', esc_html__( 'Buy Now', 'learnpress' ), $course->get_id() ) ); ?>
	</button>

	<div class="lp-ajax-message"></div>

	<?php do_action( 'learn-press/after-purchase-button' ); ?>

</form>

<?php do_action( 'learn-press/after-purchase-form' ); ?>
