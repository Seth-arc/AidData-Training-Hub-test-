<?php
/**
 * Admin Validation Page Handler
 *
 * Registers and handles the Phase 2 validation admin page.
 *
 * @package AidData_LMS
 * @subpackage Admin
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Validation Page Class
 */
class AidData_LMS_Admin_Validation_Page {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_validation_page' ) );
	}

	/**
	 * Add validation page to admin menu
	 */
	public function add_validation_page(): void {
		add_submenu_page(
			'edit.php?post_type=aiddata_tutorial',
			__( 'Phase 2 Validation', 'aiddata-lms' ),
			__( 'Phase 2 Validation', 'aiddata-lms' ),
			'manage_options',
			'aiddata-lms-phase-2-validation',
			array( $this, 'render_validation_page' )
		);
	}

	/**
	 * Render validation page
	 */
	public function render_validation_page(): void {
		// Check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'aiddata-lms' ) );
		}

		// Handle form submission
		if ( isset( $_POST['run_validation'] ) ) {
			// Verify nonce
			if ( ! isset( $_POST['validation_nonce'] ) || ! wp_verify_nonce( $_POST['validation_nonce'], 'aiddata_phase_2_validation' ) ) {
				wp_die( esc_html__( 'Security check failed.', 'aiddata-lms' ) );
			}

			// Run validation tests
			$this->run_and_display_validation();
		} else {
			// Show validation form
			include AIDDATA_LMS_PATH . 'includes/admin/views/phase-2-validation.php';
		}
	}

	/**
	 * Run validation tests and display results
	 */
	private function run_and_display_validation(): void {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Phase 2 Validation Results', 'aiddata-lms' ) . '</h1>';

		// Show loading message
		echo '<div class="notice notice-info"><p>';
		echo esc_html__( 'Running validation tests...', 'aiddata-lms' );
		echo '</p></div>';

		// Run tests
		$start_time = microtime( true );
		
		// Ensure validation class is loaded
		if ( ! class_exists( 'AidData_LMS_Phase_2_Validation' ) ) {
			require_once AIDDATA_LMS_PATH . 'includes/admin/class-aiddata-lms-phase-2-validation.php';
		}

		$results = AidData_LMS_Phase_2_Validation::run_all_tests();
		$end_time = microtime( true );
		$execution_time = round( $end_time - $start_time, 2 );

		// Generate and display report
		$report = AidData_LMS_Phase_2_Validation::generate_report( $results );
		echo $report;

		// Show execution time
		echo '<div class="validation-footer" style="text-align: center; margin: 20px 0; color: #666;">';
		echo '<p>';
		printf(
			/* translators: %s: execution time in seconds */
			esc_html__( 'Tests completed in %s seconds', 'aiddata-lms' ),
			'<strong>' . esc_html( $execution_time ) . '</strong>'
		);
		echo '</p>';
		echo '</div>';

		// Add button to run again
		echo '<div style="text-align: center; margin: 20px 0;">';
		echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=aiddata_tutorial&page=aiddata-lms-phase-2-validation' ) ) . '" class="button button-primary">';
		echo esc_html__( 'Run Tests Again', 'aiddata-lms' );
		echo '</a>';
		echo ' ';
		echo '<a href="' . esc_url( $this->get_export_url() ) . '" class="button button-secondary">';
		echo esc_html__( 'Export Results as Text', 'aiddata-lms' );
		echo '</a>';
		echo '</div>';

		echo '</div>'; // .wrap
	}

	/**
	 * Get export URL for validation results
	 *
	 * @return string Export URL
	 */
	private function get_export_url(): string {
		return add_query_arg(
			array(
				'action'    => 'aiddata_export_validation',
				'_wpnonce'  => wp_create_nonce( 'export_validation' ),
			),
			admin_url( 'admin-ajax.php' )
		);
	}
}
