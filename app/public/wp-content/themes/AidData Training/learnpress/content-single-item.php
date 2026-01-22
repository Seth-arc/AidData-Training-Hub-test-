<?php
/**
 * Template for displaying content of single course with curriculum and
 * item's content inside it.
 *
 * This template has been customized to force the actual lesson content to load,
 * avoiding the hidden 'popup' behavior that LearnPress often defaults to
 * when 'Content-Single-Item' is invoked in a non-standard way.
 *
 * @author  ThimPress (Modified for AidData Training)
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit();

/**
 * Setup the course item (lesson/quiz) early so LearnPress can enqueue
 * the curriculum assets before wp_head runs.
 */
global $lp_course_item;

if ( ! $lp_course_item ) {
	$item_slug = get_query_var( 'course-item' );
	if ( ! $item_slug && isset( $_GET['course-item'] ) ) {
		$item_slug = sanitize_title( wp_unslash( $_GET['course-item'] ) );
	}

	$item_type = get_query_var( 'item-type' );
	if ( ! $item_type && isset( $_GET['item-type'] ) ) {
		$item_type = sanitize_key( wp_unslash( $_GET['item-type'] ) );
	}
	if ( ! $item_type ) {
		$item_type = LP_LESSON_CPT;
	}

	if ( $item_slug ) {
		$course = learn_press_get_course();
		if ( $course ) {
			$post_item = learn_press_get_post_by_name( $item_slug, $item_type );
			if ( $post_item ) {
				$lp_course_item = $course->get_item( $post_item->ID );
			}
		}
	}
}

$course       = learn_press_get_course();
$course_title = $course ? $course->get_title() : get_the_title();
$lesson_title = $lp_course_item ? $lp_course_item->get_title() : get_the_title();
$course_link  = $course ? $course->get_permalink() : get_permalink();

get_header(); // Load standard header
?>
<!-- Hero Section (Reused from single-course to maintain context) -->
<style>
	.aiddata-lesson-hero {
		display: block !important;
		margin-top: 70px;
	}
</style>

<section class="hero aiddata-lesson-hero">
	<div class="hero-content">
		<h1 class="tutorial-title"><?php echo esc_html( $course_title ); ?></h1>
		<div class="breadcrumb">
			<a href="<?php echo esc_url( $course_link ); ?>">ƒ+? Back to Course Overview</a>
		</div>
		<h2 class="lesson-title"><?php echo esc_html( $lesson_title ); ?></h2>
	</div>
</section>

<div id="popup-course" class="course-summary">
	<?php
	// Render LearnPress curriculum + item content layout.
	do_action( 'learn-press/single-item-summary' );
	?>
</div>

<?php
get_footer();
?>
