<?php
/**
 * Active Tutorial Interface Template
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
$tutorial_id = $post->ID;
$user_id = get_current_user_id();

// Get managers
$progress_manager = new AidData_LMS_Tutorial_Progress();
$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();

// Verify enrollment
if ( ! $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) ) {
	wp_safe_redirect( get_permalink( $tutorial_id ) );
	exit;
}

// Get tutorial data
$steps = get_post_meta( $tutorial_id, '_tutorial_steps', true );
if ( ! is_array( $steps ) || empty( $steps ) ) {
	echo '<div class="no-steps-message">';
	esc_html_e( 'This tutorial has no steps yet.', 'aiddata-lms' );
	echo '</div>';
	return;
}

// Get progress
$progress = $progress_manager->get_progress( $user_id, $tutorial_id );
$current_step_index = $progress ? absint( $progress->current_step ) : 0;
$completed_steps = $progress && $progress->completed_steps ? explode( ',', $progress->completed_steps ) : array();

// Check for step parameter in URL
if ( isset( $_GET['step'] ) ) {
	$requested_step = absint( $_GET['step'] );
	if ( isset( $steps[ $requested_step ] ) ) {
		$current_step_index = $requested_step;
	}
}

// Ensure valid step index
if ( ! isset( $steps[ $current_step_index ] ) ) {
	$current_step_index = 0;
}

$current_step = $steps[ $current_step_index ];
?>

<div class="active-tutorial-container" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
	
	<!-- Tutorial Header -->
	<div class="tutorial-header">
		<div class="header-left">
			<a href="<?php echo esc_url( get_permalink( $tutorial_id ) ); ?>" class="back-link">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<?php esc_html_e( 'Back to Overview', 'aiddata-lms' ); ?>
			</a>
			<h1 class="tutorial-title"><?php echo esc_html( get_the_title() ); ?></h1>
		</div>
		
		<div class="header-right">
			<div class="progress-indicator">
				<span class="progress-text">
					<?php
					/* translators: 1: current step number 2: total steps */
					printf( esc_html__( 'Step %1$d of %2$d', 'aiddata-lms' ), $current_step_index + 1, count( $steps ) );
					?>
				</span>
				<div class="progress-bar-mini">
					<div class="progress-fill" style="width: <?php echo esc_attr( $progress ? $progress->progress_percent : 0 ); ?>%;"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="tutorial-main-content">
		
		<!-- Sidebar Navigation -->
		<aside class="tutorial-sidebar" id="tutorial-sidebar">
			<div class="sidebar-header">
				<h2><?php esc_html_e( 'Tutorial Steps', 'aiddata-lms' ); ?></h2>
				<button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="<?php esc_attr_e( 'Toggle sidebar', 'aiddata-lms' ); ?>">
					<span class="dashicons dashicons-menu"></span>
				</button>
			</div>
			
			<nav class="steps-navigation" aria-label="<?php esc_attr_e( 'Tutorial steps', 'aiddata-lms' ); ?>">
				<ul class="steps-list">
					<?php foreach ( $steps as $index => $step ) :
						$is_current = ( $index === $current_step_index );
						$is_completed = in_array( (string) $index, $completed_steps, true );
						$is_accessible = ( $index === 0 || in_array( (string) ( $index - 1 ), $completed_steps, true ) );
						
						$classes = array( 'step-item' );
						if ( $is_current ) {
							$classes[] = 'current';
						}
						if ( $is_completed ) {
							$classes[] = 'completed';
						}
						if ( ! $is_accessible ) {
							$classes[] = 'locked';
						}
						?>
						<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-step-index="<?php echo esc_attr( $index ); ?>">
							<button 
								type="button" 
								class="step-link" 
								<?php echo ! $is_accessible ? 'disabled' : ''; ?>
								data-step-index="<?php echo esc_attr( $index ); ?>"
								aria-label="<?php echo esc_attr( sprintf( __( 'Step %d: %s', 'aiddata-lms' ), $index + 1, $step['title'] ) ); ?>"
								<?php if ( $is_current ) echo 'aria-current="step"'; ?>
							>
								<span class="step-number"><?php echo esc_html( $index + 1 ); ?></span>
								<span class="step-title"><?php echo esc_html( $step['title'] ); ?></span>
								<span class="step-status">
									<?php if ( $is_completed ) : ?>
										<span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e( 'Completed', 'aiddata-lms' ); ?>"></span>
									<?php elseif ( ! $is_accessible ) : ?>
										<span class="dashicons dashicons-lock" aria-label="<?php esc_attr_e( 'Locked', 'aiddata-lms' ); ?>"></span>
									<?php endif; ?>
								</span>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
			
			<div class="sidebar-footer">
				<div class="overall-progress">
					<div class="progress-label">
						<span><?php esc_html_e( 'Overall Progress', 'aiddata-lms' ); ?></span>
						<span class="progress-percent"><?php echo esc_html( round( $progress ? $progress->progress_percent : 0 ) ); ?>%</span>
					</div>
					<div class="progress-bar">
						<div class="progress-fill" style="width: <?php echo esc_attr( $progress ? $progress->progress_percent : 0 ); ?>%;"></div>
					</div>
				</div>
			</div>
		</aside>
		
		<!-- Step Content -->
		<main class="step-content-area" id="step-content" role="main">
			<div class="step-content" data-step-index="<?php echo esc_attr( $current_step_index ); ?>">
				<?php
				// Render step content
				$renderer = new AidData_LMS_Step_Renderer();
				echo $renderer->render_step_content( $current_step, $current_step_index, $tutorial_id );
				?>
			</div>
			
			<!-- Navigation Buttons -->
			<div class="step-navigation-buttons">
				<?php if ( $current_step_index > 0 ) : ?>
					<button type="button" class="button button-secondary nav-previous" data-step="<?php echo esc_attr( $current_step_index - 1 ); ?>">
						<span class="dashicons dashicons-arrow-left-alt2"></span>
						<?php esc_html_e( 'Previous Step', 'aiddata-lms' ); ?>
					</button>
				<?php endif; ?>
				
				<?php if ( ! in_array( (string) $current_step_index, $completed_steps, true ) && isset( $current_step['required'] ) && $current_step['required'] ) : ?>
					<button type="button" class="button button-primary mark-complete" data-step="<?php echo esc_attr( $current_step_index ); ?>">
						<?php esc_html_e( 'Mark as Complete', 'aiddata-lms' ); ?>
					</button>
				<?php endif; ?>
				
				<?php if ( $current_step_index < count( $steps ) - 1 ) : ?>
					<button type="button" class="button button-primary nav-next" data-step="<?php echo esc_attr( $current_step_index + 1 ); ?>">
						<?php esc_html_e( 'Next Step', 'aiddata-lms' ); ?>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</button>
				<?php else : ?>
					<button type="button" class="button button-primary finish-tutorial">
						<?php esc_html_e( 'Finish Tutorial', 'aiddata-lms' ); ?>
						<span class="dashicons dashicons-yes"></span>
					</button>
				<?php endif; ?>
			</div>
		</main>
		
	</div>
	
	<!-- Mobile Bottom Navigation -->
	<div class="mobile-bottom-nav">
		<button type="button" class="mob-nav-button sidebar-toggle-mob" aria-label="<?php esc_attr_e( 'View steps', 'aiddata-lms' ); ?>">
			<span class="dashicons dashicons-menu"></span>
			<span><?php esc_html_e( 'Steps', 'aiddata-lms' ); ?></span>
		</button>
		<?php if ( $current_step_index > 0 ) : ?>
			<button type="button" class="mob-nav-button nav-previous-mob" data-step="<?php echo esc_attr( $current_step_index - 1 ); ?>">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<span><?php esc_html_e( 'Previous', 'aiddata-lms' ); ?></span>
			</button>
		<?php endif; ?>
		<?php if ( $current_step_index < count( $steps ) - 1 ) : ?>
			<button type="button" class="mob-nav-button nav-next-mob" data-step="<?php echo esc_attr( $current_step_index + 1 ); ?>">
				<span><?php esc_html_e( 'Next', 'aiddata-lms' ); ?></span>
				<span class="dashicons dashicons-arrow-right-alt2"></span>
			</button>
		<?php endif; ?>
	</div>
	
</div>
