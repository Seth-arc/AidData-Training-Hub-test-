<?php
/**
 * Template for displaying Continue button in single course.
 *
 * This template overrides the default LearnPress template to use a direct link
 * instead of a form submission, ensuring standard page navigation.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

$user      = learn_press_get_current_user();
$course    = learn_press_get_course();
$course_id = $course ? $course->get_id() : 0;

// Find the first incomplete item (same logic as REST API continue_course).
$continue_url = '';
if ( $course && $user && $user->get_id() > 0 ) {
	$item_id        = 0;
	$sections_items = $course->get_full_sections_and_items_course();

	if ( ! empty( $sections_items ) ) {
		foreach ( $sections_items as $section_items ) {
			if ( empty( $section_items->items ) ) {
				continue;
			}

			foreach ( $section_items->items as $item ) {
				if ( ! $user->has_completed_item( $item->id, $course_id ) && get_post( $item->id ) ) {
					$item_id = $item->id;
					break 2;
				}
			}
		}
	}

	if ( ! $item_id ) {
		$item_id = $course->get_first_item_id();
	}

	if ( $item_id ) {
		$item_slug = get_post_field( 'post_name', $item_id );
		$item_type = get_post_type( $item_id );
		$query_url = '';

		if ( $item_slug && $item_type ) {
			$query_url = add_query_arg(
				array(
					'course-item' => $item_slug,
					'item-type'   => $item_type,
				),
				$course->get_permalink()
			);
		}

		$continue_url = $query_url ? $query_url : $course->get_item_link( $item_id );
	}
}

?>

<?php if ( $continue_url ) : ?>
    <!-- Custom Direct Link for Continue -->
    <a id="aiddata-continue-btn"
       href="<?php echo esc_url( $continue_url ); ?>"
       class="lp-button button button-continue-course"
       style="display: block !important; width: 100%; text-decoration: none; text-align: center; padding: 1rem; border-radius: 6px; font-size: 1rem; font-weight: 600; background-color: #004E38; color: #fff;">
		<?php echo esc_html( apply_filters( 'learn-press/continue-course-button-text', esc_html__( 'Continue', 'learnpress' ) ) ); ?>
    </a>
<?php else : ?>
    <!-- No continue URL found - show message for debugging -->
    <?php if ( current_user_can( 'manage_options' ) ) : ?>
        <p style="color: red; font-size: 12px;">Debug: No continue URL found. Check course curriculum.</p>
    <?php endif; ?>
<?php endif; ?>
