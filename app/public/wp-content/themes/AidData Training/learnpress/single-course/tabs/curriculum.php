<?php
/**
 * Template for displaying curriculum tab of single course.
 *
 * Custom override for AidData Training theme - removes hooks that may cause duplicate content.
 *
 * @author  ThimPress (modified for AidData Training theme)
 * @package  Learnpress/Templates
 * @version  4.0.1
 */

defined( 'ABSPATH' ) || exit();

$course = learn_press_get_course();
$user   = learn_press_get_current_user();

if ( ! $course || ! $user ) {
	return;
}

$can_view_content_course = $user->can_view_content_course( $course->get_id() );
?>

<div class="course-curriculum aiddata-curriculum" id="aiddata-course-curriculum">
	<div class="curriculum-scrollable">

		<?php
		// Note: Removed do_action( 'learn-press/before-single-course-curriculum' ) to prevent duplicate content

		$curriculum  = $course->get_curriculum();
		$user_course = $user->get_course_data( get_the_ID() );
		$user        = learn_press_get_current_user();

		if ( $curriculum ) :
			?>
			<ul class="curriculum-sections">
				<?php
				foreach ( $curriculum as $section ) {
					$args = [
						'section'                 => $section,
						'can_view_content_course' => $can_view_content_course,
						'user_course'             => $user_course,
						'user'                    => $user,
					];

					learn_press_get_template( 'single-course/loop-section.php', $args );
				}
				?>
			</ul>

		<?php else : ?>
			<?php
			echo wp_kses_post(
				apply_filters(
					'learnpress/course/curriculum/empty',
					esc_html__( 'The curriculum is empty', 'learnpress' )
				)
			);
			?>
		<?php endif ?>

		<?php // Note: Removed do_action( 'learn-press/after-single-course-curriculum' ) to prevent duplicate content ?>

	</div>
</div>
