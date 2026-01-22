<?php
/**
 * Tutorial Step Builder View
 *
 * Renders the step builder interface for creating and managing tutorial steps.
 *
 * @package AidData_LMS
 * @subpackage Admin/Views
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ensure $steps variable exists
$steps = isset( $steps ) ? $steps : array();
?>

<div class="aiddata-step-builder">
	<div class="step-builder-header">
		<button type="button" class="button button-primary" id="add-step">
			<span class="dashicons dashicons-plus"></span>
			<?php esc_html_e( 'Add Step', 'aiddata-lms' ); ?>
		</button>
		
		<div class="step-templates">
			<label><?php esc_html_e( 'Quick Add:', 'aiddata-lms' ); ?></label>
			<button type="button" class="button add-step-template" data-type="video">
				<span class="dashicons dashicons-video-alt3"></span>
				<?php esc_html_e( 'Video', 'aiddata-lms' ); ?>
			</button>
			<button type="button" class="button add-step-template" data-type="text">
				<span class="dashicons dashicons-text-page"></span>
				<?php esc_html_e( 'Text', 'aiddata-lms' ); ?>
			</button>
			<button type="button" class="button add-step-template" data-type="interactive">
				<span class="dashicons dashicons-welcome-widgets-menus"></span>
				<?php esc_html_e( 'Interactive', 'aiddata-lms' ); ?>
			</button>
			<button type="button" class="button add-step-template" data-type="resource">
				<span class="dashicons dashicons-download"></span>
				<?php esc_html_e( 'Resource', 'aiddata-lms' ); ?>
			</button>
			<button type="button" class="button add-step-template" data-type="quiz">
				<span class="dashicons dashicons-list-view"></span>
				<?php esc_html_e( 'Quiz', 'aiddata-lms' ); ?>
			</button>
		</div>
	</div>
	
	<div class="step-builder-container">
		<?php if ( empty( $steps ) ) : ?>
			<div class="no-steps-message">
				<p><?php esc_html_e( 'No steps added yet. Click "Add Step" to begin building your tutorial.', 'aiddata-lms' ); ?></p>
			</div>
		<?php else : ?>
			<div class="steps-list" id="sortable-steps">
				<?php foreach ( $steps as $index => $step ) : ?>
					<?php include AIDDATA_LMS_PATH . 'includes/admin/views/step-item.php'; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	
	<input type="hidden" name="tutorial_steps" id="tutorial-steps-data" value="<?php echo esc_attr( wp_json_encode( $steps ) ); ?>">
</div>

<!-- Step Editor Modal -->
<div id="step-editor-modal" class="aiddata-modal" style="display:none;">
	<div class="modal-overlay"></div>
	<div class="modal-content">
		<div class="modal-header">
			<h2 id="step-editor-title"><?php esc_html_e( 'Edit Step', 'aiddata-lms' ); ?></h2>
			<button type="button" class="modal-close">&times;</button>
		</div>
		<div class="modal-body" id="step-editor-body">
			<!-- Step editor form will be loaded here -->
		</div>
		<div class="modal-footer">
			<button type="button" class="button" id="cancel-step-edit">
				<?php esc_html_e( 'Cancel', 'aiddata-lms' ); ?>
			</button>
			<button type="button" class="button button-primary" id="save-step-edit">
				<?php esc_html_e( 'Save Step', 'aiddata-lms' ); ?>
			</button>
		</div>
	</div>
</div>

