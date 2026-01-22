<?php
/**
 * Admin Dashboard Widgets
 *
 * Registers and renders dashboard widgets for LMS statistics.
 *
 * @package AidData_LMS
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Admin_Dashboard
 *
 * Manages WordPress dashboard widgets for displaying LMS statistics.
 *
 * @since 1.0.0
 */
class AidData_LMS_Admin_Dashboard {

	/**
	 * Constructor.
	 *
	 * Registers dashboard widgets.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register dashboard widgets.
	 *
	 * @since 1.0.0
	 */
	public function register_widgets(): void {
		// Only show to users with capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Enrollments widget.
		wp_add_dashboard_widget(
			'aiddata_lms_enrollments',
			__( 'Tutorial Enrollments', 'aiddata-lms' ),
			array( $this, 'render_enrollments_widget' )
		);

		// Popular tutorials widget.
		wp_add_dashboard_widget(
			'aiddata_lms_popular_tutorials',
			__( 'Popular Tutorials', 'aiddata-lms' ),
			array( $this, 'render_popular_tutorials_widget' )
		);

		// Completion stats widget.
		wp_add_dashboard_widget(
			'aiddata_lms_completion_stats',
			__( 'Completion Statistics', 'aiddata-lms' ),
			array( $this, 'render_completion_stats_widget' )
		);

		// Recent activity widget.
		wp_add_dashboard_widget(
			'aiddata_lms_recent_activity',
			__( 'Recent Learning Activity', 'aiddata-lms' ),
			array( $this, 'render_recent_activity_widget' )
		);
	}

	/**
	 * Render enrollments widget.
	 *
	 * @since 1.0.0
	 */
	public function render_enrollments_widget(): void {
		$stats = $this->get_enrollment_stats();
		?>
		<div class="aiddata-lms-dashboard-widget">
			<div class="aiddata-lms-stats-grid">
				<div class="stat-box">
					<div class="stat-value"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Total Enrollments', 'aiddata-lms' ); ?></div>
				</div>

				<div class="stat-box">
					<div class="stat-value stat-positive">
						+<?php echo esc_html( number_format_i18n( $stats['today'] ) ); ?>
					</div>
					<div class="stat-label"><?php esc_html_e( 'Today', 'aiddata-lms' ); ?></div>
				</div>

				<div class="stat-box">
					<div class="stat-value"><?php echo esc_html( number_format_i18n( $stats['active'] ) ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Active Learners', 'aiddata-lms' ); ?></div>
				</div>

				<div class="stat-box">
					<div class="stat-value stat-success">
						<?php echo esc_html( number_format_i18n( $stats['completed'] ) ); ?>
					</div>
					<div class="stat-label"><?php esc_html_e( 'Completed', 'aiddata-lms' ); ?></div>
				</div>
			</div>

			<div class="widget-footer">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=aiddata_tutorial&page=aiddata-lms-reports' ) ); ?>">
					<?php esc_html_e( 'View Full Report', 'aiddata-lms' ); ?> →
				</a>
			</div>
		</div>

		<style>
			.aiddata-lms-stats-grid {
				display: grid;
				grid-template-columns: repeat(2, 1fr);
				gap: 15px;
				margin-bottom: 15px;
			}

			.stat-box {
				background: #f9f9f9;
				padding: 15px;
				border-radius: 4px;
				text-align: center;
			}

			.stat-value {
				font-size: 32px;
				font-weight: bold;
				color: #333;
				line-height: 1;
				margin-bottom: 8px;
			}

			.stat-positive {
				color: #28a745;
			}

			.stat-success {
				color: #0073aa;
			}

			.stat-label {
				font-size: 13px;
				color: #666;
			}

			.widget-footer {
				padding-top: 10px;
				border-top: 1px solid #ddd;
				text-align: right;
			}

			.widget-footer a {
				text-decoration: none;
			}
		</style>
		<?php
	}

	/**
	 * Render popular tutorials widget.
	 *
	 * @since 1.0.0
	 */
	public function render_popular_tutorials_widget(): void {
		$tutorials = $this->get_popular_tutorials( 5 );

		if ( empty( $tutorials ) ) {
			echo '<p>' . esc_html__( 'No tutorial data available yet.', 'aiddata-lms' ) . '</p>';
			return;
		}
		?>
		<div class="aiddata-lms-popular-tutorials">
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Tutorial', 'aiddata-lms' ); ?></th>
						<th><?php esc_html_e( 'Enrollments', 'aiddata-lms' ); ?></th>
						<th><?php esc_html_e( 'Completion Rate', 'aiddata-lms' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tutorials as $tutorial ) : ?>
						<tr>
							<td>
								<a href="<?php echo esc_url( get_edit_post_link( $tutorial['tutorial_id'] ) ); ?>">
									<?php echo esc_html( get_the_title( $tutorial['tutorial_id'] ) ); ?>
								</a>
							</td>
							<td><?php echo esc_html( number_format_i18n( $tutorial['enrollment_count'] ) ); ?></td>
							<td>
								<?php
								$rate  = round( $tutorial['completion_rate'], 1 );
								$color = $rate >= 50 ? '#28a745' : '#ffc107';
								?>
								<span class="completion-rate" style="color: <?php echo esc_attr( $color ); ?>">
									<?php echo esc_html( $rate ); ?>%
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render completion stats widget.
	 *
	 * @since 1.0.0
	 */
	public function render_completion_stats_widget(): void {
		$stats = $this->get_completion_stats();
		?>
		<div class="aiddata-lms-completion-stats">
			<div class="stat-item">
				<span class="stat-label"><?php esc_html_e( 'Average Completion Rate:', 'aiddata-lms' ); ?></span>
				<span class="stat-value"><?php echo esc_html( round( $stats['avg_completion_rate'], 1 ) ); ?>%</span>
			</div>

			<div class="stat-item">
				<span class="stat-label"><?php esc_html_e( 'Tutorials Completed This Week:', 'aiddata-lms' ); ?></span>
				<span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['completed_this_week'] ) ); ?></span>
			</div>

			<div class="stat-item">
				<span class="stat-label"><?php esc_html_e( 'Tutorials Completed This Month:', 'aiddata-lms' ); ?></span>
				<span class="stat-value"><?php echo esc_html( number_format_i18n( $stats['completed_this_month'] ) ); ?></span>
			</div>

			<div class="stat-item">
				<span class="stat-label"><?php esc_html_e( 'Average Time to Complete:', 'aiddata-lms' ); ?></span>
				<span class="stat-value"><?php echo esc_html( $this->format_time( $stats['avg_time_spent'] ) ); ?></span>
			</div>
		</div>

		<style>
			.aiddata-lms-completion-stats {
				padding: 10px 0;
			}

			.stat-item {
				display: flex;
				justify-content: space-between;
				padding: 8px 0;
				border-bottom: 1px solid #f0f0f0;
			}

			.stat-item:last-child {
				border-bottom: none;
			}

			.stat-item .stat-label {
				color: #666;
				font-size: 13px;
			}

			.stat-item .stat-value {
				font-weight: bold;
				color: #0073aa;
				font-size: 14px;
			}
		</style>
		<?php
	}

	/**
	 * Render recent activity widget.
	 *
	 * @since 1.0.0
	 */
	public function render_recent_activity_widget(): void {
		$activities = $this->get_recent_activities( 5 );

		if ( empty( $activities ) ) {
			echo '<p>' . esc_html__( 'No recent activity.', 'aiddata-lms' ) . '</p>';
			return;
		}
		?>
		<div class="aiddata-lms-recent-activity">
			<ul class="activity-list">
				<?php foreach ( $activities as $activity ) : ?>
					<li class="activity-item">
						<div class="activity-icon">
							<?php
							$icon = $this->get_activity_icon( $activity['status'] );
							echo wp_kses_post( $icon );
							?>
						</div>
						<div class="activity-details">
							<div class="activity-user">
								<?php
								$user = get_userdata( $activity['user_id'] );
								echo esc_html( $user ? $user->display_name : __( 'Unknown User', 'aiddata-lms' ) );
								?>
							</div>
							<div class="activity-action">
								<?php echo esc_html( $this->get_activity_text( $activity['status'] ) ); ?>
								<strong><?php echo esc_html( get_the_title( $activity['tutorial_id'] ) ); ?></strong>
							</div>
							<div class="activity-time">
								<?php
								/* translators: %s: human-readable time difference */
								printf(
									esc_html__( '%s ago', 'aiddata-lms' ),
									esc_html( human_time_diff( strtotime( $activity['enrolled_at'] ) ) )
								);
								?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<style>
			.activity-list {
				margin: 0;
				padding: 0;
				list-style: none;
			}

			.activity-item {
				display: flex;
				padding: 10px 0;
				border-bottom: 1px solid #f0f0f0;
			}

			.activity-item:last-child {
				border-bottom: none;
			}

			.activity-icon {
				flex-shrink: 0;
				width: 32px;
				height: 32px;
				margin-right: 10px;
				display: flex;
				align-items: center;
				justify-content: center;
				background: #f0f0f0;
				border-radius: 50%;
				font-size: 16px;
			}

			.activity-details {
				flex: 1;
			}

			.activity-user {
				font-weight: 600;
				font-size: 13px;
				color: #333;
			}

			.activity-action {
				font-size: 13px;
				color: #666;
				margin: 2px 0;
			}

			.activity-time {
				font-size: 12px;
				color: #999;
			}
		</style>
		<?php
	}

	/**
	 * Get enrollment statistics.
	 *
	 * @since 1.0.0
	 *
	 * @return array Statistics including total, today, active, and completed counts.
	 */
	private function get_enrollment_stats(): array {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'enrollments' );

		// Total enrollments.
		$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );

		// Today's enrollments.
		$today = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$table_name}
			 WHERE DATE(enrolled_at) = CURDATE()"
		);

		// Active learners (enrolled but not completed).
		$active = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$table_name}
			 WHERE status = 'active' AND completed_at IS NULL"
		);

		// Completed.
		$completed = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$table_name}
			 WHERE completed_at IS NOT NULL"
		);

		return array(
			'total'     => (int) $total,
			'today'     => (int) $today,
			'active'    => (int) $active,
			'completed' => (int) $completed,
		);
	}

	/**
	 * Get popular tutorials.
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit Maximum number of tutorials to return.
	 * @return array Array of popular tutorials with enrollment and completion data.
	 */
	private function get_popular_tutorials( int $limit = 5 ): array {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'enrollments' );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
					tutorial_id,
					COUNT(*) as enrollment_count,
					SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed_count,
					(SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*)) * 100 as completion_rate
				 FROM {$table_name}
				 GROUP BY tutorial_id
				 ORDER BY enrollment_count DESC
				 LIMIT %d",
				$limit
			),
			ARRAY_A
		);

		return $results ?: array();
	}

	/**
	 * Get completion statistics.
	 *
	 * @since 1.0.0
	 *
	 * @return array Completion statistics including rates and time data.
	 */
	private function get_completion_stats(): array {
		global $wpdb;
		$enrollments_table = AidData_LMS_Database::get_table_name( 'enrollments' );
		$progress_table    = AidData_LMS_Database::get_table_name( 'progress' );

		// Average completion rate.
		$avg_rate = $wpdb->get_var(
			"SELECT AVG(progress_percent)
			 FROM {$progress_table}"
		);

		// Completed this week.
		$this_week = $wpdb->get_var(
			"SELECT COUNT(*)
			 FROM {$enrollments_table}
			 WHERE completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
		);

		// Completed this month.
		$this_month = $wpdb->get_var(
			"SELECT COUNT(*)
			 FROM {$enrollments_table}
			 WHERE completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
		);

		// Average time spent on completed tutorials.
		$avg_time = $wpdb->get_var(
			"SELECT AVG(time_spent)
			 FROM {$progress_table}
			 WHERE status = 'completed'"
		);

		return array(
			'avg_completion_rate'  => (float) $avg_rate,
			'completed_this_week'  => (int) $this_week,
			'completed_this_month' => (int) $this_month,
			'avg_time_spent'       => (int) $avg_time,
		);
	}

	/**
	 * Get recent activities.
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit Maximum number of activities to return.
	 * @return array Array of recent enrollment activities.
	 */
	private function get_recent_activities( int $limit = 5 ): array {
		global $wpdb;
		$table_name = AidData_LMS_Database::get_table_name( 'enrollments' );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id, tutorial_id, enrolled_at, status
				 FROM {$table_name}
				 ORDER BY enrolled_at DESC
				 LIMIT %d",
				$limit
			),
			ARRAY_A
		);

		return $results ?: array();
	}

	/**
	 * Get activity icon based on status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Activity status.
	 * @return string Icon HTML.
	 */
	private function get_activity_icon( string $status ): string {
		$icons = array(
			'active'    => '<span class="dashicons dashicons-book-alt" style="color: #0073aa;"></span>',
			'completed' => '<span class="dashicons dashicons-yes-alt" style="color: #28a745;"></span>',
			'cancelled' => '<span class="dashicons dashicons-dismiss" style="color: #dc3545;"></span>',
		);

		return $icons[ $status ] ?? '<span class="dashicons dashicons-book"></span>';
	}

	/**
	 * Get activity text based on status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Activity status.
	 * @return string Activity description.
	 */
	private function get_activity_text( string $status ): string {
		$texts = array(
			'active'    => __( 'enrolled in', 'aiddata-lms' ),
			'completed' => __( 'completed', 'aiddata-lms' ),
			'cancelled' => __( 'unenrolled from', 'aiddata-lms' ),
		);

		return $texts[ $status ] ?? __( 'interacted with', 'aiddata-lms' );
	}

	/**
	 * Format time in seconds to human-readable format.
	 *
	 * @since 1.0.0
	 *
	 * @param int $seconds Time in seconds.
	 * @return string Formatted time string.
	 */
	private function format_time( int $seconds ): string {
		if ( $seconds < 60 ) {
			/* translators: %d: number of seconds */
			return sprintf( _n( '%d second', '%d seconds', $seconds, 'aiddata-lms' ), $seconds );
		}

		$minutes = floor( $seconds / 60 );
		if ( $minutes < 60 ) {
			/* translators: %d: number of minutes */
			return sprintf( _n( '%d minute', '%d minutes', $minutes, 'aiddata-lms' ), $minutes );
		}

		$hours = floor( $minutes / 60 );
		/* translators: %d: number of hours */
		return sprintf( _n( '%d hour', '%d hours', $hours, 'aiddata-lms' ), $hours );
	}
}

