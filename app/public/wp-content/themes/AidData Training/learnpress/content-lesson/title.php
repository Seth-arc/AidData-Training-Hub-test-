<?php
/**
 * Template for displaying title of lesson.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $lesson ) ) {
	return;
}

$title = $lesson->get_title( 'display' );

if ( ! $title ) {
	return;
}
?>

<?php
// Title hidden - customized for AidData Training theme
// Original: <h1 class="course-item-title lesson-title"><?php echo esc_html( $title ); ?></h1>

