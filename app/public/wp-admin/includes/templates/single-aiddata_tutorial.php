<?php
/**
 * Single Tutorial Template
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	
	$tutorial_id = get_the_ID();
	$user_id     = get_current_user_id();
	
	// Initialize managers
	$enrollment_manager = null;
	$progress_manager   = null;
	$is_enrolled        = false;
	$enrollment         = null;
	$progress           = null;
	
	if ( class_exists( 'AidData_LMS_Tutorial_Enrollment' ) ) {
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		$is_enrolled        = $user_id > 0 ? $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) : false;
		$enrollment         = $user_id > 0 ? $enrollment_manager->get_enrollment( $user_id, $tutorial_id ) : null;
	}
	
	if ( class_exists( 'AidData_LMS_Tutorial_Progress' ) && $is_enrolled ) {
		$progress_manager = new AidData_LMS_Tutorial_Progress();
		$progress         = $progress_manager->get_progress( $user_id, $tutorial_id );
	}
	
	// Get metadata
	$short_desc        = get_post_meta( $tutorial_id, '_tutorial_short_description', true );
	$full_desc         = get_post_meta( $tutorial_id, '_tutorial_full_description', true );
	$duration          = get_post_meta( $tutorial_id, '_tutorial_duration', true );
	$outcomes          = get_post_meta( $tutorial_id, '_tutorial_outcomes', true );
	$prerequisites_ids = get_post_meta( $tutorial_id, '_tutorial_prerequisites', true );
	$steps             = get_post_meta( $tutorial_id, '_tutorial_steps', true );
	
	// Check if user should continue or start
	$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
	if ( $is_enrolled && 'continue' === $action ) {
		// Load active tutorial interface
		if ( file_exists( AIDDATA_LMS_PATH . 'templates/template-parts/active-tutorial.php' ) ) {
			include AIDDATA_LMS_PATH . 'templates/template-parts/active-tutorial.php';
			get_footer();
			return;
		}
	}
	?>
	
	<article id="tutorial-<?php the_ID(); ?>" <?php post_class( 'single-tutorial' ); ?>>
		
		<!-- Hero Section -->
		<div class="tutorial-hero">
			<div class="hero-content">
				<div class="hero-breadcrumbs">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'aiddata_tutorial' ) ); ?>">
						<?php esc_html_e( 'Tutorials', 'aiddata-lms' ); ?>
					</a>
					<?php
					$categories = get_the_terms( $tutorial_id, 'aiddata_tutorial_cat' );
					if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
						foreach ( $categories as $category ) {
							echo '<span class="separator">/</span>';
							printf(
								'<a href="%s">%s</a>',
								esc_url( get_term_link( $category ) ),
								esc_html( $category->name )
							);
						}
					}
					?>
				</div>
				
				<h1 class="tutorial-title"><?php the_title(); ?></h1>
				
				<?php if ( $short_desc ) : ?>
					<p class="tutorial-tagline"><?php echo esc_html( $short_desc ); ?></p>
				<?php endif; ?>
				
				<div class="tutorial-meta-bar">
					<?php
					$difficulty_terms = get_the_terms( $tutorial_id, 'aiddata_tutorial_difficulty' );
					if ( ! empty( $difficulty_terms ) && ! is_wp_error( $difficulty_terms ) ) :
						?>
						<span class="meta-item difficulty">
							<strong><?php esc_html_e( 'Level:', 'aiddata-lms' ); ?></strong>
							<?php echo esc_html( $difficulty_terms[0]->name ); ?>
						</span>
					<?php endif; ?>
					
					<?php if ( $duration ) : ?>
						<span class="meta-item duration">
							<span class="dashicons dashicons-clock"></span>
							<?php
							printf(
								/* translators: %d: duration in minutes */
								esc_html__( '%d minutes', 'aiddata-lms' ),
								absint( $duration )
							);
							?>
						</span>
					<?php endif; ?>
					
					<?php if ( is_array( $steps ) && ! empty( $steps ) ) : ?>
						<span class="meta-item steps">
							<span class="dashicons dashicons-list-view"></span>
							<?php
							printf(
								/* translators: %d: number of steps */
								esc_html( _n( '%d step', '%d steps', count( $steps ), 'aiddata-lms' ) ),
								count( $steps )
							);
							?>
						</span>
					<?php endif; ?>
					
					<?php if ( $enrollment_manager ) : ?>
						<span class="meta-item enrollments">
							<span class="dashicons dashicons-groups"></span>
							<?php
							$enrollment_count = $enrollment_manager->get_enrollment_count( $tutorial_id, 'active' );
							printf(
								/* translators: %d: number of enrolled users */
								esc_html( _n( '%d enrolled', '%d enrolled', $enrollment_count, 'aiddata-lms' ) ),
								$enrollment_count
							);
							?>
						</span>
					<?php endif; ?>
				</div>
				
				<?php if ( $is_enrolled && $progress && $progress->progress_percent > 0 && $progress->progress_percent < 100 ) : ?>
					<div class="tutorial-progress-banner">
						<div class="progress-bar-container">
							<div class="progress-bar" style="width: <?php echo esc_attr( $progress->progress_percent ); ?>%;"></div>
						</div>
						<p class="progress-text">
							<?php
							printf(
								/* translators: %d: completion percentage */
								esc_html__( '%d%% Complete', 'aiddata-lms' ),
								round( $progress->progress_percent )
							);
							?>
						</p>
						<a href="<?php echo esc_url( add_query_arg( 'action', 'continue', get_permalink() ) ); ?>" class="button button-large button-primary">
							<?php esc_html_e( 'Continue Learning', 'aiddata-lms' ); ?>
						</a>
					</div>
				<?php elseif ( ! $is_enrolled ) : ?>
					<div class="tutorial-enrollment-section">
						<?php
						// Use template part if it exists in theme, otherwise use plugin template
						if ( locate_template( 'template-parts/enrollment-button.php' ) ) {
							get_template_part( 'template-parts/enrollment-button' );
						} elseif ( file_exists( AIDDATA_LMS_PATH . 'templates/template-parts/enrollment-button.php' ) ) {
							include AIDDATA_LMS_PATH . 'templates/template-parts/enrollment-button.php';
						}
						?>
					</div>
				<?php endif; ?>
			</div>
			
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="hero-image">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>
		</div>
		
		<!-- Main Content -->
		<div class="tutorial-content-wrapper">
			<div class="tutorial-main">
				
				<!-- What You'll Learn -->
				<?php if ( ! empty( $outcomes ) && is_array( $outcomes ) ) : ?>
					<section class="tutorial-section outcomes-section">
						<h2><?php esc_html_e( 'What You\'ll Learn', 'aiddata-lms' ); ?></h2>
						<ul class="outcomes-list">
							<?php foreach ( $outcomes as $outcome ) : ?>
								<li>
									<span class="dashicons dashicons-yes"></span>
									<?php echo esc_html( $outcome ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
				
				<!-- Description -->
				<?php if ( $full_desc || get_the_content() ) : ?>
					<section class="tutorial-section description-section">
						<h2><?php esc_html_e( 'About This Tutorial', 'aiddata-lms' ); ?></h2>
						<div class="tutorial-description">
							<?php echo wp_kses_post( $full_desc ? $full_desc : get_the_content() ); ?>
						</div>
					</section>
				<?php endif; ?>
				
				<!-- Steps Overview -->
				<?php if ( ! empty( $steps ) && is_array( $steps ) ) : ?>
					<section class="tutorial-section steps-section">
						<h2><?php esc_html_e( 'Tutorial Content', 'aiddata-lms' ); ?></h2>
						<div class="steps-accordion">
							<?php
							$completed_steps = array();
							if ( $is_enrolled && $progress && ! empty( $progress->completed_steps ) ) {
								$completed_steps = explode( ',', $progress->completed_steps );
							}
							
							foreach ( $steps as $index => $step ) :
								$is_completed = in_array( (string) $index, $completed_steps, true );
								$step_number  = $index + 1;
								$step_title   = isset( $step['title'] ) ? $step['title'] : sprintf( __( 'Step %d', 'aiddata-lms' ), $step_number );
								?>
								<div class="step-accordion-item <?php echo $is_completed ? 'completed' : ''; ?>">
									<div class="step-header">
										<span class="step-number"><?php echo esc_html( $step_number ); ?></span>
										<h3 class="step-title"><?php echo esc_html( $step_title ); ?></h3>
										<?php if ( ! empty( $step['estimated_time'] ) ) : ?>
											<span class="step-duration">
												<?php
												printf(
													/* translators: %d: duration in minutes */
													esc_html__( '%d min', 'aiddata-lms' ),
													absint( $step['estimated_time'] )
												);
												?>
											</span>
										<?php endif; ?>
										<?php if ( $is_completed ) : ?>
											<span class="step-check"><span class="dashicons dashicons-yes"></span></span>
										<?php elseif ( ! $is_enrolled ) : ?>
											<span class="step-lock"><span class="dashicons dashicons-lock"></span></span>
										<?php endif; ?>
									</div>
									<?php if ( ! empty( $step['description'] ) ) : ?>
										<div class="step-description">
											<?php echo esc_html( $step['description'] ); ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>
				
				<!-- Prerequisites -->
				<?php if ( ! empty( $prerequisites_ids ) && is_array( $prerequisites_ids ) ) : ?>
					<section class="tutorial-section prerequisites-section">
						<h2><?php esc_html_e( 'Prerequisites', 'aiddata-lms' ); ?></h2>
						<p><?php esc_html_e( 'Before starting this tutorial, you should complete:', 'aiddata-lms' ); ?></p>
						<ul class="prerequisites-list">
							<?php
							foreach ( $prerequisites_ids as $prereq_id ) :
								$prereq = get_post( $prereq_id );
								if ( ! $prereq || 'aiddata_tutorial' !== $prereq->post_type ) {
									continue;
								}
								
								$prereq_is_completed = false;
								if ( $enrollment_manager && $user_id > 0 ) {
									$prereq_enrollment   = $enrollment_manager->get_enrollment( $user_id, $prereq_id );
									$prereq_is_completed = $prereq_enrollment && 'completed' === $prereq_enrollment->status;
								}
								?>
								<li class="<?php echo $prereq_is_completed ? 'completed' : ''; ?>">
									<?php if ( $prereq_is_completed ) : ?>
										<span class="dashicons dashicons-yes"></span>
									<?php else : ?>
										<span class="dashicons dashicons-minus"></span>
									<?php endif; ?>
									<a href="<?php echo esc_url( get_permalink( $prereq_id ) ); ?>">
										<?php echo esc_html( $prereq->post_title ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
				
				<!-- Tags -->
				<?php
				$tags = get_the_terms( $tutorial_id, 'aiddata_tutorial_tag' );
				if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) :
					?>
					<section class="tutorial-section tags-section">
						<h3><?php esc_html_e( 'Tags', 'aiddata-lms' ); ?></h3>
						<div class="tutorial-tags">
							<?php foreach ( $tags as $tag ) : ?>
								<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="tag">
									<?php echo esc_html( $tag->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>
				
			</div>
			
			<!-- Sidebar -->
			<aside class="tutorial-sidebar">
				<?php if ( ! $is_enrolled ) : ?>
					<div class="sidebar-widget enrollment-widget">
						<?php
						if ( locate_template( 'template-parts/enrollment-button.php' ) ) {
							get_template_part( 'template-parts/enrollment-button' );
						} elseif ( file_exists( AIDDATA_LMS_PATH . 'templates/template-parts/enrollment-button.php' ) ) {
							include AIDDATA_LMS_PATH . 'templates/template-parts/enrollment-button.php';
						}
						?>
					</div>
				<?php endif; ?>
				
				<!-- Tutorial Info Widget -->
				<div class="sidebar-widget info-widget">
					<h3><?php esc_html_e( 'Tutorial Information', 'aiddata-lms' ); ?></h3>
					<ul class="tutorial-info-list">
						<?php if ( $duration ) : ?>
							<li>
								<span class="dashicons dashicons-clock"></span>
								<strong><?php esc_html_e( 'Duration:', 'aiddata-lms' ); ?></strong>
								<?php
								printf(
									/* translators: %d: duration in minutes */
									esc_html__( '%d minutes', 'aiddata-lms' ),
									absint( $duration )
								);
								?>
							</li>
						<?php endif; ?>
						
						<?php if ( is_array( $steps ) && ! empty( $steps ) ) : ?>
							<li>
								<span class="dashicons dashicons-list-view"></span>
								<strong><?php esc_html_e( 'Steps:', 'aiddata-lms' ); ?></strong>
								<?php echo esc_html( count( $steps ) ); ?>
							</li>
						<?php endif; ?>
						
						<li>
							<span class="dashicons dashicons-calendar"></span>
							<strong><?php esc_html_e( 'Last Updated:', 'aiddata-lms' ); ?></strong>
							<?php echo esc_html( get_the_modified_date() ); ?>
						</li>
					</ul>
				</div>
				
				<!-- Share Widget -->
				<div class="sidebar-widget share-widget">
					<h3><?php esc_html_e( 'Share This Tutorial', 'aiddata-lms' ); ?></h3>
					<div class="share-buttons">
						<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . urlencode( get_permalink() ) . '&text=' . urlencode( get_the_title() ) ); ?>" target="_blank" rel="noopener" class="share-button twitter">
							<span class="dashicons dashicons-twitter"></span>
						</a>
						<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener" class="share-button facebook">
							<span class="dashicons dashicons-facebook"></span>
						</a>
						<a href="<?php echo esc_url( 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode( get_permalink() ) . '&title=' . urlencode( get_the_title() ) ); ?>" target="_blank" rel="noopener" class="share-button linkedin">
							<span class="dashicons dashicons-linkedin"></span>
						</a>
					</div>
				</div>
			</aside>
		</div>
		
	</article>
	
<?php
endwhile;

get_footer();
