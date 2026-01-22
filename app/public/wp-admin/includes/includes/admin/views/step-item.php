<?php
/**
 * Single Step Item Template
 *
 * Displays a single step in the step builder list.
 *
 * @package AidData_LMS
 * @subpackage Admin/Views
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Extract step data
$step_id       = isset( $step['id'] ) ? $step['id'] : uniqid();
$step_type     = isset( $step['type'] ) ? $step['type'] : 'text';
$step_title    = isset( $step['title'] ) ? $step['title'] : __( 'Untitled Step', 'aiddata-lms' );
$step_required = isset( $step['required'] ) && $step['required'];
$step_time     = isset( $step['estimated_time'] ) ? $step['estimated_time'] : 0;

// Type icons mapping
$type_icons = array(
	'video'       => 'video-alt3',
	'text'        => 'text-page',
	'interactive' => 'welcome-widgets-menus',
	'resource'    => 'download',
	'quiz'        => 'list-view',
);

$icon = isset( $type_icons[ $step_type ] ) ? $type_icons[ $step_type ] : 'admin-page';
?>

<div class="step-item" data-step-id="<?php echo esc_attr( $step_id ); ?>" data-step-type="<?php echo esc_attr( $step_type ); ?>">
	<div class="step-handle">
		<span class="dashicons dashicons-menu"></span>
	</div>
	
	<div class="step-icon">
		<span class="dashicons dashicons-<?php echo esc_attr( $icon ); ?>"></span>
	</div>
	
	<div class="step-content">
		<div class="step-title">
			<?php echo esc_html( $step_title ); ?>
			<?php if ( $step_required ) : ?>
				<span class="step-required-badge"><?php esc_html_e( 'Required', 'aiddata-lms' ); ?></span>
			<?php endif; ?>
		</div>
		<div class="step-meta">
			<span class="step-type"><?php echo esc_html( ucfirst( $step_type ) ); ?></span>
			<?php if ( $step_time > 0 ) : ?>
				<span class="step-time">
					<span class="dashicons dashicons-clock"></span>
					<?php
					/* translators: %d: number of minutes */
					printf( esc_html__( '%d min', 'aiddata-lms' ), $step_time );
					?>
				</span>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="step-actions">
		<button type="button" class="button button-small edit-step" title="<?php esc_attr_e( 'Edit Step', 'aiddata-lms' ); ?>">
			<span class="dashicons dashicons-edit"></span>
		</button>
		<button type="button" class="button button-small duplicate-step" title="<?php esc_attr_e( 'Duplicate Step', 'aiddata-lms' ); ?>">
			<span class="dashicons dashicons-admin-page"></span>
		</button>
		<button type="button" class="button button-small delete-step" title="<?php esc_attr_e( 'Delete Step', 'aiddata-lms' ); ?>">
			<span class="dashicons dashicons-trash"></span>
		</button>
	</div>
</div>

