<?php
/**
 * Phase 2 Validation Admin Page View
 *
 * @package AidData_LMS
 * @subpackage Admin/Views
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Phase 2 Validation Tests', 'aiddata-lms' ); ?></h1>
	
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'About Phase 2 Validation:', 'aiddata-lms' ); ?></strong>
			<?php esc_html_e( 'This page runs comprehensive automated tests to verify that all Phase 2 features are properly implemented and functional.', 'aiddata-lms' ); ?>
		</p>
	</div>
	
	<div class="validation-actions" style="margin: 20px 0;">
		<form method="post" action="">
			<?php wp_nonce_field( 'aiddata_phase_2_validation', 'validation_nonce' ); ?>
			<input type="hidden" name="run_validation" value="1">
			<button type="submit" class="button button-primary button-hero">
				<span class="dashicons dashicons-yes-alt" style="margin-top: 4px;"></span>
				<?php esc_html_e( 'Run Phase 2 Validation Tests', 'aiddata-lms' ); ?>
			</button>
		</form>
		
		<p class="description" style="margin-top: 10px;">
			<?php esc_html_e( 'Click the button above to run all 40 validation tests. This may take a few seconds.', 'aiddata-lms' ); ?>
		</p>
	</div>
	
	<div class="validation-info">
		<h2><?php esc_html_e( 'What Gets Tested', 'aiddata-lms' ); ?></h2>
		<div class="test-categories" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-admin-tools"></span>
					<?php esc_html_e( 'Tutorial Builder', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '5 tests verifying meta boxes, step builder, and admin interfaces.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-list-view"></span>
					<?php esc_html_e( 'Admin List Interface', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '5 tests verifying custom columns, bulk actions, and filters.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-layout"></span>
					<?php esc_html_e( 'Frontend Display', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '5 tests verifying archive, single, and card templates.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-controls-forward"></span>
					<?php esc_html_e( 'Active Tutorial', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '5 tests verifying navigation interface and AJAX handlers.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-chart-line"></span>
					<?php esc_html_e( 'Progress Persistence', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '4 tests verifying progress tracking and milestones.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-networking"></span>
					<?php esc_html_e( 'Integration', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '5 tests verifying Phase 0 & 1 system integration.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-shield"></span>
					<?php esc_html_e( 'Security', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '4 tests verifying nonces, capabilities, and sanitization.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-performance"></span>
					<?php esc_html_e( 'Performance', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '3 tests verifying asset sizes and optimization.', 'aiddata-lms' ); ?></p>
			</div>
			
			<div class="test-category-box" style="border: 1px solid #ccd0d4; padding: 15px; background: #fff;">
				<h3 style="margin-top: 0;">
					<span class="dashicons dashicons-universal-access"></span>
					<?php esc_html_e( 'Accessibility', 'aiddata-lms' ); ?>
				</h3>
				<p><?php esc_html_e( '4 tests verifying WCAG 2.1 AA compliance.', 'aiddata-lms' ); ?></p>
			</div>
		</div>
	</div>
</div>
