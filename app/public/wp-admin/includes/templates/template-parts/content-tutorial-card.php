<?php
/**
 * Tutorial Card Template Part
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tutorial_id = get_the_ID();
$user_id     = get_current_user_id();

// Initialize managers
$enrollment_manager = null;
$is_enrolled        = false;
$enrollment_count   = 0;

if ( class_exists( 'AidData_LMS_Tutorial_Enrollment' ) ) {
	$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
	$is_enrolled        = $user_id > 0 ? $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) : false;
	$enrollment_count   = $enrollment_manager->get_enrollment_count( $tutorial_id, 'active' );
}

// Get tutorial metadata
$duration    = get_post_meta( $tutorial_id, '_tutorial_duration', true );
$step_count  = get_post_meta( $tutorial_id, '_tutorial_step_count', true );
$short_desc  = get_post_meta( $tutorial_id, '_tutorial_short_description', true );

// Get difficulty
$difficulty_terms = get_the_terms( $tutorial_id, 'aiddata_tutorial_difficulty' );
$difficulty       = ! empty( $difficulty_terms ) && ! is_wp_error( $difficulty_terms ) ? $difficulty_terms[0]->name : '';
?>

<article id="tutorial-<?php the_ID(); ?>" <?php post_class( 'tutorial-card' ); ?>>
	<div class="tutorial-card-inner">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="tutorial-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'medium_large' ); ?>
				</a>
				<?php if ( $is_enrolled ) : ?>
					<span class="enrolled-badge"><?php esc_html_e( 'Enrolled', 'aiddata-lms' ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<div class="tutorial-content">
			<div class="tutorial-meta">
				<?php if ( $difficulty ) : ?>
					<span class="difficulty difficulty-<?php echo esc_attr( sanitize_title( $difficulty ) ); ?>">
						<?php echo esc_html( $difficulty ); ?>
					</span>
				<?php endif; ?>
				
				<?php
				$categories = get_the_terms( $tutorial_id, 'aiddata_tutorial_cat' );
				if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
					?>
					<span class="category">
						<a href="<?php echo esc_url( get_term_link( $categories[0] ) ); ?>">
							<?php echo esc_html( $categories[0]->name ); ?>
						</a>
					</span>
				<?php endif; ?>
			</div>
			
			<h2 class="tutorial-title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
			
			<?php if ( $short_desc ) : ?>
				<p class="tutorial-excerpt"><?php echo esc_html( $short_desc ); ?></p>
			<?php elseif ( has_excerpt() ) : ?>
				<p class="tutorial-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			
			<div class="tutorial-stats">
				<?php if ( $step_count ) : ?>
					<span class="stat steps">
						<span class="dashicons dashicons-list-view"></span>
						<?php
						printf(
							/* translators: %d: number of steps */
							esc_html( _n( '%d Step', '%d Steps', absint( $step_count ), 'aiddata-lms' ) ),
							absint( $step_count )
						);
						?>
					</span>
				<?php endif; ?>
				
				<?php if ( $duration ) : ?>
					<span class="stat duration">
						<span class="dashicons dashicons-clock"></span>
						<?php
						printf(
							/* translators: %d: duration in minutes */
							esc_html__( '%d min', 'aiddata-lms' ),
							absint( $duration )
						);
						?>
					</span>
				<?php endif; ?>
				
				<span class="stat enrollments">
					<span class="dashicons dashicons-groups"></span>
					<?php
					printf(
						/* translators: %d: number of enrolled users */
						esc_html( _n( '%d enrolled', '%d enrolled', absint( $enrollment_count ), 'aiddata-lms' ) ),
						absint( $enrollment_count )
					);
					?>
				</span>
			</div>
			
			<div class="tutorial-actions">
				<?php if ( $is_enrolled ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'action', 'continue', get_permalink() ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Continue Learning', 'aiddata-lms' ); ?>
					</a>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>" class="button button-secondary">
						<?php esc_html_e( 'Learn More', 'aiddata-lms' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>
