<?php
/**
 * Tutorial Archive Template
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="aiddata-tutorials-archive">
	<div class="archive-header">
		<h1 class="archive-title">
			<?php
			if ( is_tax() ) {
				single_term_title();
			} else {
				esc_html_e( 'All Tutorials', 'aiddata-lms' );
			}
			?>
		</h1>
		
		<?php if ( term_description() ) : ?>
			<div class="archive-description">
				<?php echo wp_kses_post( term_description() ); ?>
			</div>
		<?php endif; ?>
	</div>
	
	<div class="archive-filters">
		<?php echo do_shortcode( '[aiddata_tutorial_filters]' ); ?>
	</div>
	
	<div class="archive-content">
		<?php if ( have_posts() ) : ?>
			<div class="tutorials-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					
					// Check if template part exists in theme first
					if ( locate_template( 'template-parts/content-tutorial-card.php' ) ) {
						get_template_part( 'template-parts/content', 'tutorial-card' );
					} else {
						// Use plugin template
						include AIDDATA_LMS_PATH . 'templates/template-parts/content-tutorial-card.php';
					}
				endwhile;
				?>
			</div>
			
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => __( '&laquo; Previous', 'aiddata-lms' ),
					'next_text' => __( 'Next &raquo;', 'aiddata-lms' ),
				)
			);
			?>
			
		<?php else : ?>
			<div class="no-tutorials">
				<p><?php esc_html_e( 'No tutorials found.', 'aiddata-lms' ); ?></p>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<p>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=aiddata_tutorial' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Create First Tutorial', 'aiddata-lms' ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
