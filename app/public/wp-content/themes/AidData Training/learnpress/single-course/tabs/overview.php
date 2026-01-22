<?php
/**
 * Template for displaying overview tab of single course.
 *
 * This template overrides the LearnPress default to ensure proper content display.
 *
 * @author   ThimPress (modified for AidData Training theme)
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

/**
 * @var LP_Course $course
 */
$course = learn_press_get_course();
if ( ! $course ) {
	return;
}

$content = $course->get_content();
?>

<div class="course-description" id="learn-press-course-description">

	<?php
	/**
	 * @deprecated
	 */
	do_action( 'learn_press_begin_single_course_description' );

	/**
	 * @since 3.0.0
	 */
	do_action( 'learn-press/before-single-course-description' );

	if ( ! empty( $content ) ) {
		// Apply the_content filter for proper formatting (shortcodes, embeds, etc.)
		$formatted_content = apply_filters( 'the_content', $content );
		echo wp_kses_post( $formatted_content );
	} else {
		echo '<p class="no-content-message">Course overview will be added soon. Check back later for more details about this course.</p>';
	}

	/**
	 * @since 3.0.0
	 */
	do_action( 'learn-press/after-single-course-description' );

	/**
	 * @deprecated
	 */
	do_action( 'learn_press_end_single_course_description' );
	?>

</div>
