<?php
/**
 * Admin Reports Page
 *
 * Displays analytics reports and provides data export functionality.
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Admin_Reports
 *
 * Manages the admin reports page for analytics and statistics.
 *
 * @since 1.0.0
 */
class AidData_LMS_Admin_Reports {

	/**
	 * Constructor.
	 *
	 * Registers admin menu and handles export actions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'handle_export' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Add reports submenu page.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_page(): void {
		add_submenu_page(
			'edit.php?post_type=aiddata_tutorial',
			__( 'Reports', 'aiddata-lms' ),
			__( 'Reports', 'aiddata-lms' ),
			'manage_options',
			'aiddata-lms-reports',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue_styles( string $hook_suffix ): void {
		// Only load on reports page.
		if ( 'aiddata_tutorial_page_aiddata-lms-reports' !== $hook_suffix ) {
			return;
		}

		// Enqueue Chart.js for visualizations.
		wp_enqueue_script(
			'chartjs',
			'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js',
			array(),
			'3.9.1',
			true
		);
	}

	/**
	 * Handle CSV export.
	 *
	 * @since 1.0.0
	 */
	public function handle_export(): void {
		// Check if export action is requested.
		if ( ! isset( $_GET['action'] ) || 'export_csv' !== $_GET['action'] ) {
			return;
		}

		// Check if on reports page.
		if ( ! isset( $_GET['page'] ) || 'aiddata-lms-reports' !== $_GET['page'] ) {
			return;
		}

		// Verify user capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export reports.', 'aiddata-lms' ) );
		}

		// Verify nonce.
		check_admin_referer( 'aiddata_lms_export_csv' );

		// Get date range.
		$date_range = array(
			'start' => isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : date( 'Y-m-d', strtotime( '-30 days' ) ),
			'end'   => isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : date( 'Y-m-d' ),
		);

		// Generate CSV.
		$this->generate_csv_export( $date_range );
		exit;
	}

	/**
	 * Generate and output CSV export.
	 *
	 * @since 1.0.0
	 *
	 * @param array $date_range Date range for export.
	 */
	private function generate_csv_export( array $date_range ): void {
		$analytics = new AidData_LMS_Analytics();
		$stats     = $analytics->get_platform_analytics( $date_range );

		// Set headers for file download.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=aiddata-lms-report-' . date( 'Y-m-d' ) . '.csv' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Create output stream.
		$output = fopen( 'php://output', 'w' );

		// Add BOM for UTF-8.
		fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

		// Write headers.
		fputcsv( $output, array( 'AidData LMS Analytics Report' ) );
		fputcsv( $output, array( 'Generated:', date( 'Y-m-d H:i:s' ) ) );
		fputcsv( $output, array( 'Date Range:', $date_range['start'] . ' to ' . $date_range['end'] ) );
		fputcsv( $output, array() ); // Empty line.

		// Platform Statistics.
		fputcsv( $output, array( 'Platform Statistics' ) );
		fputcsv( $output, array( 'Total Events', $stats['total_events'] ) );
		fputcsv( $output, array( 'Unique Users', $stats['unique_users'] ) );
		fputcsv( $output, array( 'Unique Tutorials', $stats['unique_tutorials'] ) );
		fputcsv( $output, array() ); // Empty line.

		// Top Events.
		fputcsv( $output, array( 'Top Events' ) );
		fputcsv( $output, array( 'Event Type', 'Count' ) );
		foreach ( $stats['top_events'] as $event ) {
			fputcsv( $output, array( $event['event_type'], $event['count'] ) );
		}
		fputcsv( $output, array() ); // Empty line.

		// Top Tutorials.
		fputcsv( $output, array( 'Top Tutorials' ) );
		fputcsv( $output, array( 'Tutorial ID', 'Tutorial Title', 'Event Count', 'User Count' ) );
		foreach ( $stats['top_tutorials'] as $tutorial ) {
			fputcsv(
				$output,
				array(
					$tutorial['tutorial_id'],
					get_the_title( $tutorial['tutorial_id'] ),
					$tutorial['event_count'],
					$tutorial['user_count'],
				)
			);
		}

		fclose( $output );
	}

	/**
	 * Render reports page.
	 *
	 * @since 1.0.0
	 */
	public function render_page(): void {
		// Get date range from query params.
		$date_range = array(
			'start' => isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : date( 'Y-m-d', strtotime( '-30 days' ) ),
			'end'   => isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : date( 'Y-m-d' ),
		);

		$analytics = new AidData_LMS_Analytics();
		$stats     = $analytics->get_platform_analytics( $date_range );

		// Get enrollment stats.
		$enrollment_stats = $this->get_enrollment_stats();
		?>
		<div class="wrap aiddata-lms-reports">
			<h1><?php esc_html_e( 'Tutorial Analytics & Reports', 'aiddata-lms' ); ?></h1>

			<div class="aiddata-lms-reports-filters">
				<form method="get">
					<input type="hidden" name="post_type" value="aiddata_tutorial">
					<input type="hidden" name="page" value="aiddata-lms-reports">

					<label for="start_date"><?php esc_html_e( 'Start Date:', 'aiddata-lms' ); ?></label>
					<input type="date" name="start_date" id="start_date" value="<?php echo esc_attr( $date_range['start'] ); ?>">

					<label for="end_date"><?php esc_html_e( 'End Date:', 'aiddata-lms' ); ?></label>
					<input type="date" name="end_date" id="end_date" value="<?php echo esc_attr( $date_range['end'] ); ?>">

					<button type="submit" class="button"><?php esc_html_e( 'Apply', 'aiddata-lms' ); ?></button>

					<?php
					$export_url = wp_nonce_url(
						add_query_arg(
							array(
								'action'     => 'export_csv',
								'start_date' => $date_range['start'],
								'end_date'   => $date_range['end'],
							)
						),
						'aiddata_lms_export_csv'
					);
					?>
					<a href="<?php echo esc_url( $export_url ); ?>" class="button button-primary">
						<?php esc_html_e( 'Export CSV', 'aiddata-lms' ); ?>
					</a>
				</form>
			</div>

			<div class="aiddata-lms-stats-cards">
				<div class="stats-card">
					<h3><?php esc_html_e( 'Total Events', 'aiddata-lms' ); ?></h3>
					<div class="stat-number"><?php echo esc_html( number_format_i18n( $stats['total_events'] ) ); ?></div>
				</div>

				<div class="stats-card">
					<h3><?php esc_html_e( 'Unique Users', 'aiddata-lms' ); ?></h3>
					<div class="stat-number"><?php echo esc_html( number_format_i18n( $stats['unique_users'] ) ); ?></div>
				</div>

				<div class="stats-card">
					<h3><?php esc_html_e( 'Active Tutorials', 'aiddata-lms' ); ?></h3>
					<div class="stat-number"><?php echo esc_html( number_format_i18n( $stats['unique_tutorials'] ) ); ?></div>
				</div>

				<div class="stats-card">
					<h3><?php esc_html_e( 'Total Enrollments', 'aiddata-lms' ); ?></h3>
					<div class="stat-number"><?php echo esc_html( number_format_i18n( $enrollment_stats['total'] ) ); ?></div>
				</div>
			</div>

			<div class="aiddata-lms-reports-grid">
				<!-- Top Events Chart -->
				<div class="report-section">
					<h2><?php esc_html_e( 'Top Event Types', 'aiddata-lms' ); ?></h2>
					<?php if ( ! empty( $stats['top_events'] ) ) : ?>
						<canvas id="topEventsChart" width="400" height="200"></canvas>
						<script>
							document.addEventListener('DOMContentLoaded', function() {
								const ctx = document.getElementById('topEventsChart').getContext('2d');
								new Chart(ctx, {
									type: 'bar',
									data: {
										labels: <?php echo wp_json_encode( array_column( $stats['top_events'], 'event_type' ) ); ?>,
										datasets: [{
											label: '<?php esc_html_e( 'Event Count', 'aiddata-lms' ); ?>',
											data: <?php echo wp_json_encode( array_column( $stats['top_events'], 'count' ) ); ?>,
											backgroundColor: 'rgba(0, 115, 170, 0.6)',
											borderColor: 'rgba(0, 115, 170, 1)',
											borderWidth: 1
										}]
									},
									options: {
										responsive: true,
										maintainAspectRatio: false,
										scales: {
											y: {
												beginAtZero: true
											}
										}
									}
								});
							});
						</script>
					<?php else : ?>
						<p><?php esc_html_e( 'No event data available.', 'aiddata-lms' ); ?></p>
					<?php endif; ?>
				</div>

				<!-- Top Tutorials Table -->
				<div class="report-section">
					<h2><?php esc_html_e( 'Top Tutorials', 'aiddata-lms' ); ?></h2>
					<?php if ( ! empty( $stats['top_tutorials'] ) ) : ?>
						<table class="widefat striped">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Tutorial', 'aiddata-lms' ); ?></th>
									<th><?php esc_html_e( 'Event Count', 'aiddata-lms' ); ?></th>
									<th><?php esc_html_e( 'Unique Users', 'aiddata-lms' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $stats['top_tutorials'] as $tutorial ) : ?>
									<tr>
										<td>
											<a href="<?php echo esc_url( get_edit_post_link( $tutorial['tutorial_id'] ) ); ?>">
												<?php echo esc_html( get_the_title( $tutorial['tutorial_id'] ) ); ?>
											</a>
										</td>
										<td><?php echo esc_html( number_format_i18n( $tutorial['event_count'] ) ); ?></td>
										<td><?php echo esc_html( number_format_i18n( $tutorial['user_count'] ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p><?php esc_html_e( 'No tutorial data available.', 'aiddata-lms' ); ?></p>
					<?php endif; ?>
				</div>

				<!-- Enrollment Trends -->
				<div class="report-section full-width">
					<h2><?php esc_html_e( 'Enrollment Overview', 'aiddata-lms' ); ?></h2>
					<div class="enrollment-stats-grid">
						<div class="enrollment-stat">
							<div class="stat-value"><?php echo esc_html( number_format_i18n( $enrollment_stats['total'] ) ); ?></div>
							<div class="stat-label"><?php esc_html_e( 'Total Enrollments', 'aiddata-lms' ); ?></div>
						</div>
						<div class="enrollment-stat">
							<div class="stat-value"><?php echo esc_html( number_format_i18n( $enrollment_stats['active'] ) ); ?></div>
							<div class="stat-label"><?php esc_html_e( 'Active Learners', 'aiddata-lms' ); ?></div>
						</div>
						<div class="enrollment-stat">
							<div class="stat-value"><?php echo esc_html( number_format_i18n( $enrollment_stats['completed'] ) ); ?></div>
							<div class="stat-label"><?php esc_html_e( 'Completed', 'aiddata-lms' ); ?></div>
						</div>
						<div class="enrollment-stat">
							<div class="stat-value"><?php echo esc_html( round( $enrollment_stats['completion_rate'], 1 ) ); ?>%</div>
							<div class="stat-label"><?php esc_html_e( 'Completion Rate', 'aiddata-lms' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<style>
			.aiddata-lms-reports {
				max-width: 1400px;
			}

			.aiddata-lms-reports-filters {
				background: #fff;
				padding: 20px;
				margin: 20px 0;
				border: 1px solid #ccd0d4;
				box-shadow: 0 1px 1px rgba(0,0,0,.04);
			}

			.aiddata-lms-reports-filters form {
				display: flex;
				align-items: center;
				gap: 15px;
				flex-wrap: wrap;
			}

			.aiddata-lms-reports-filters label {
				font-weight: 600;
			}

			.aiddata-lms-reports-filters input[type="date"] {
				padding: 5px 10px;
			}

			.aiddata-lms-stats-cards {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
				gap: 20px;
				margin: 20px 0;
			}

			.stats-card {
				background: #fff;
				padding: 20px;
				border: 1px solid #ccd0d4;
				box-shadow: 0 1px 1px rgba(0,0,0,.04);
				text-align: center;
			}

			.stats-card h3 {
				margin: 0 0 10px 0;
				font-size: 14px;
				color: #666;
				font-weight: 600;
				text-transform: uppercase;
			}

			.stats-card .stat-number {
				font-size: 36px;
				font-weight: bold;
				color: #0073aa;
			}

			.aiddata-lms-reports-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
				gap: 20px;
				margin: 20px 0;
			}

			.report-section {
				background: #fff;
				padding: 20px;
				border: 1px solid #ccd0d4;
				box-shadow: 0 1px 1px rgba(0,0,0,.04);
			}

			.report-section.full-width {
				grid-column: 1 / -1;
			}

			.report-section h2 {
				margin-top: 0;
				font-size: 18px;
				border-bottom: 1px solid #ddd;
				padding-bottom: 10px;
			}

			.report-section canvas {
				max-height: 300px;
			}

			.enrollment-stats-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
				gap: 20px;
				margin-top: 20px;
			}

			.enrollment-stat {
				text-align: center;
				padding: 20px;
				background: #f9f9f9;
				border-radius: 4px;
			}

			.enrollment-stat .stat-value {
				font-size: 32px;
				font-weight: bold;
				color: #0073aa;
				margin-bottom: 8px;
			}

			.enrollment-stat .stat-label {
				font-size: 14px;
				color: #666;
			}

			@media (max-width: 768px) {
				.aiddata-lms-reports-grid {
					grid-template-columns: 1fr;
				}

				.aiddata-lms-stats-cards {
					grid-template-columns: 1fr;
				}
			}
		</style>
		<?php
	}

	/**
	 * Get enrollment statistics.
	 *
	 * @since 1.0.0
	 *
	 * @return array Enrollment statistics.
	 */
	private function get_enrollment_stats(): array {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'enrollments' );

		// Total enrollments.
		$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );

		// Active learners.
		$active = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$table_name}
			 WHERE status = 'active' AND completed_at IS NULL"
		);

		// Completed.
		$completed = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$table_name}
			 WHERE completed_at IS NOT NULL"
		);

		// Completion rate.
		$completion_rate = $total > 0 ? ( $completed / $total ) * 100 : 0;

		return array(
			'total'           => (int) $total,
			'active'          => (int) $active,
			'completed'       => (int) $completed,
			'completion_rate' => (float) $completion_rate,
		);
	}
}

