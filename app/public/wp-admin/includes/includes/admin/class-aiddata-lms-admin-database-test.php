<?php
/**
 * Admin Database Test Page
 *
 * Provides admin interface for running and viewing database tests.
 *
 * @package    AidData_LMS
 * @subpackage Admin
 * @since      2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Admin_Database_Test
 *
 * Admin page for database testing and validation.
 *
 * @since 2.0.0
 */
class AidData_LMS_Admin_Database_Test {

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_post_aiddata_lms_run_database_tests', array( $this, 'handle_run_tests' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add admin menu page
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function add_menu_page(): void {
		add_submenu_page(
			'tools.php',
			__( 'AidData LMS Database Tests', 'aiddata-lms' ),
			__( 'LMS Database Tests', 'aiddata-lms' ),
			'manage_options',
			'aiddata-lms-database-tests',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @since 2.0.0
	 * @param string $hook Current admin page hook
	 * @return void
	 */
	public function enqueue_scripts( string $hook ): void {
		if ( 'tools_page_aiddata-lms-database-tests' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'aiddata-lms-database-tests',
			AIDDATA_LMS_URL . 'assets/css/admin.css',
			array(),
			AIDDATA_LMS_VERSION
		);
	}

	/**
	 * Handle test run request
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function handle_run_tests(): void {
		// Verify nonce and permissions
		if ( ! isset( $_POST['_wpnonce'] ) || 
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'aiddata_lms_database_tests' ) ) {
			wp_die( esc_html__( 'Security check failed', 'aiddata-lms' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions', 'aiddata-lms' ) );
		}

		// Run tests and store results
		$results = AidData_LMS_Database_Test::run_tests();
		set_transient( 'aiddata_lms_test_results', $results, HOUR_IN_SECONDS );

		// Redirect back to test page
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'      => 'aiddata-lms-database-tests',
					'test_run'  => 'success',
				),
				admin_url( 'tools.php' )
			)
		);
		exit;
	}

	/**
	 * Render admin page
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function render_page(): void {
		// Check if we just ran tests
		$test_run = isset( $_GET['test_run'] ) && $_GET['test_run'] === 'success'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		
		// Get test results from transient
		$results = get_transient( 'aiddata_lms_test_results' );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AidData LMS Database Tests', 'aiddata-lms' ); ?></h1>
			
			<?php if ( $test_run && $results ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Tests completed successfully!', 'aiddata-lms' ); ?></p>
				</div>
			<?php endif; ?>

			<div class="aiddata-lms-test-controls" style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
				<h2><?php esc_html_e( 'Test Controls', 'aiddata-lms' ); ?></h2>
				<p><?php esc_html_e( 'Run comprehensive database tests to validate schema, foreign keys, indexes, and data integrity.', 'aiddata-lms' ); ?></p>
				
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="aiddata_lms_run_database_tests" />
					<?php wp_nonce_field( 'aiddata_lms_database_tests' ); ?>
					<?php
					submit_button(
						__( 'Run Database Tests', 'aiddata-lms' ),
						'primary large',
						'submit',
						false
					);
					?>
				</form>

				<?php if ( $results ) : ?>
					<div style="margin-top: 15px;">
						<a href="<?php echo esc_url( add_query_arg( array( 'download_report' => '1', 'page' => 'aiddata-lms-database-tests' ), admin_url( 'tools.php' ) ) ); ?>" class="button">
							<?php esc_html_e( 'Download Report (HTML)', 'aiddata-lms' ); ?>
						</a>
						<a href="<?php echo esc_url( add_query_arg( array( 'download_json' => '1', 'page' => 'aiddata-lms-database-tests' ), admin_url( 'tools.php' ) ) ); ?>" class="button">
							<?php esc_html_e( 'Download Report (JSON)', 'aiddata-lms' ); ?>
						</a>
						<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'aiddata-lms-database-tests', 'clear_results' => '1' ), admin_url( 'tools.php' ) ) ); ?>" class="button">
							<?php esc_html_e( 'Clear Results', 'aiddata-lms' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $results ) : ?>
				<div class="aiddata-lms-test-results" style="margin: 20px 0;">
					<?php
					// Display HTML report
					echo AidData_LMS_Database_Test::generate_html_report( $results ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
			<?php else : ?>
				<div class="notice notice-info">
					<p><?php esc_html_e( 'No test results available. Click "Run Database Tests" to begin.', 'aiddata-lms' ); ?></p>
				</div>
			<?php endif; ?>

			<div class="aiddata-lms-test-info" style="margin: 20px 0; padding: 20px; background: #f9f9f9; border-left: 4px solid #2271b1;">
				<h2><?php esc_html_e( 'About These Tests', 'aiddata-lms' ); ?></h2>
				<p><?php esc_html_e( 'The database tests validate:', 'aiddata-lms' ); ?></p>
				<ul style="list-style: disc; margin-left: 20px;">
					<li><?php esc_html_e( 'Environment requirements (PHP, WordPress, MySQL versions)', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Table existence (all 6 custom tables)', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Schema validation (columns, engine, charset)', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Foreign key constraints (referential integrity)', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Index validation (performance optimization)', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Data integrity (orphaned records detection)', 'aiddata-lms' ); ?></li>
				</ul>
				<p><strong><?php esc_html_e( 'Note:', 'aiddata-lms' ); ?></strong> <?php esc_html_e( 'Tests are read-only and will not modify your database.', 'aiddata-lms' ); ?></p>
			</div>

			<?php $this->render_database_info(); ?>
		</div>
		<?php

		// Handle downloads
		$this->handle_downloads( $results );

		// Handle clear results
		if ( isset( $_GET['clear_results'] ) && $_GET['clear_results'] === '1' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			delete_transient( 'aiddata_lms_test_results' );
			wp_safe_redirect(
				add_query_arg(
					array( 'page' => 'aiddata-lms-database-tests' ),
					admin_url( 'tools.php' )
				)
			);
			exit;
		}
	}

	/**
	 * Render database information
	 *
	 * @since 2.0.0
	 * @return void
	 */
	private function render_database_info(): void {
		// Get database statistics if helper class exists
		if ( class_exists( 'AidData_LMS_Database' ) ) {
			$stats = AidData_LMS_Database::get_statistics();

			?>
			<div class="aiddata-lms-db-stats" style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
				<h2><?php esc_html_e( 'Database Statistics', 'aiddata-lms' ); ?></h2>
				<table class="widefat">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Table', 'aiddata-lms' ); ?></th>
							<th><?php esc_html_e( 'Rows', 'aiddata-lms' ); ?></th>
							<th><?php esc_html_e( 'Size (MB)', 'aiddata-lms' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $stats['tables'] as $table => $data ) : ?>
							<tr>
								<td><?php echo esc_html( $table ); ?></td>
								<td><?php echo esc_html( number_format( $data['rows'] ) ); ?></td>
								<td><?php echo esc_html( number_format( $data['size_mb'], 2 ) ); ?></td>
							</tr>
						<?php endforeach; ?>
						<tr style="font-weight: bold; background: #f0f0f0;">
							<td><?php esc_html_e( 'Total', 'aiddata-lms' ); ?></td>
							<td><?php echo esc_html( number_format( $stats['total_rows'] ) ); ?></td>
							<td><?php echo esc_html( number_format( $stats['total_size_mb'], 2 ) ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php
		}
	}

	/**
	 * Handle report downloads
	 *
	 * @since 2.0.0
	 * @param array|false $results Test results
	 * @return void
	 */
	private function handle_downloads( $results ): void {
		if ( ! $results ) {
			return;
		}

		// HTML Download
		if ( isset( $_GET['download_report'] ) && $_GET['download_report'] === '1' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$html = AidData_LMS_Database_Test::generate_html_report( $results );
			
			header( 'Content-Type: text/html' );
			header( 'Content-Disposition: attachment; filename="aiddata-lms-test-report-' . gmdate( 'Y-m-d-H-i-s' ) . '.html"' );
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			exit;
		}

		// JSON Download
		if ( isset( $_GET['download_json'] ) && $_GET['download_json'] === '1' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			header( 'Content-Type: application/json' );
			header( 'Content-Disposition: attachment; filename="aiddata-lms-test-results-' . gmdate( 'Y-m-d-H-i-s' ) . '.json"' );
			echo wp_json_encode( $results, JSON_PRETTY_PRINT );
			exit;
		}
	}
}

// Initialize admin page
new AidData_LMS_Admin_Database_Test();

