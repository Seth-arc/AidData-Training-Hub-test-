<?php
/**
 * Phase 2 Validation Tests
 * 
 * Comprehensive testing and validation of all Phase 2 features
 *
 * @package AidData_LMS
 * @subpackage Admin
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Phase 2 Validation Class
 */
class AidData_LMS_Phase_2_Validation {
	
	/**
	 * Run all validation tests
	 *
	 * @return array Validation results
	 */
	public static function run_all_tests(): array {
		$results = array();
		
		$results['tutorial_builder'] = self::test_tutorial_builder();
		$results['admin_list'] = self::test_admin_list();
		$results['frontend_display'] = self::test_frontend_display();
		$results['active_tutorial'] = self::test_active_tutorial();
		$results['progress_persistence'] = self::test_progress_persistence();
		$results['integration'] = self::test_integration();
		$results['security'] = self::test_security();
		$results['performance'] = self::test_performance();
		$results['accessibility'] = self::test_accessibility();
		
		return $results;
	}
	
	/**
	 * Test tutorial builder functionality
	 *
	 * @return array Test results
	 */
	private static function test_tutorial_builder(): array {
		$tests = array();
		
		// Test 1: Meta boxes registration
		$tests['meta_boxes_registered'] = array(
			'name' => 'Meta Boxes Registration',
			'status' => self::check_meta_boxes_exist(),
			'message' => 'Checking if tutorial meta boxes are registered'
		);
		
		// Test 2: Step builder file exists
		$tests['step_builder_exists'] = array(
			'name' => 'Step Builder File',
			'status' => file_exists( AIDDATA_LMS_PATH . 'assets/js/admin/tutorial-step-builder.js' ),
			'message' => 'Checking if step builder JavaScript exists'
		);
		
		// Test 3: Meta boxes class exists
		$tests['meta_boxes_class'] = array(
			'name' => 'Meta Boxes Class',
			'status' => class_exists( 'AidData_LMS_Tutorial_Meta_Boxes' ),
			'message' => 'Checking if meta boxes class is defined'
		);
		
		// Test 4: Admin CSS exists
		$tests['admin_css_exists'] = array(
			'name' => 'Admin CSS Files',
			'status' => file_exists( AIDDATA_LMS_PATH . 'assets/css/admin/tutorial-meta-boxes.css' ),
			'message' => 'Checking if tutorial builder CSS exists'
		);
		
		// Test 5: Views templates exist
		$tests['views_exist'] = array(
			'name' => 'View Templates',
			'status' => file_exists( AIDDATA_LMS_PATH . 'includes/admin/views/tutorial-step-builder.php' ) &&
						file_exists( AIDDATA_LMS_PATH . 'includes/admin/views/step-item.php' ),
			'message' => 'Checking if view templates exist'
		);
		
		return $tests;
	}
	
	/**
	 * Test admin list interface
	 *
	 * @return array Test results
	 */
	private static function test_admin_list(): array {
		$tests = array();
		
		// Test 1: Custom columns filter
		$tests['custom_columns'] = array(
			'name' => 'Custom Columns Filter',
			'status' => has_filter( 'manage_aiddata_tutorial_posts_columns' ),
			'message' => 'Checking if custom columns filter is registered'
		);
		
		// Test 2: Bulk actions filter
		$tests['bulk_actions'] = array(
			'name' => 'Bulk Actions Filter',
			'status' => has_filter( 'bulk_actions-edit-aiddata_tutorial' ),
			'message' => 'Checking if bulk actions filter is registered'
		);
		
		// Test 3: Quick edit action
		$tests['quick_edit'] = array(
			'name' => 'Quick Edit Action',
			'status' => has_action( 'quick_edit_custom_box' ),
			'message' => 'Checking if quick edit action is registered'
		);
		
		// Test 4: Admin filters action
		$tests['admin_filters'] = array(
			'name' => 'Admin Filters Action',
			'status' => has_action( 'restrict_manage_posts' ),
			'message' => 'Checking if admin filters action is registered'
		);
		
		// Test 5: List CSS exists
		$tests['list_css_exists'] = array(
			'name' => 'List CSS File',
			'status' => file_exists( AIDDATA_LMS_PATH . 'assets/css/admin/tutorial-list.css' ),
			'message' => 'Checking if tutorial list CSS exists'
		);
		
		return $tests;
	}
	
	/**
	 * Test frontend display
	 *
	 * @return array Test results
	 */
	private static function test_frontend_display(): array {
		$tests = array();
		
		// Test 1: Archive template exists
		$tests['archive_template'] = array(
			'name' => 'Archive Template',
			'status' => file_exists( AIDDATA_LMS_PATH . 'templates/archive-aiddata_tutorial.php' ),
			'message' => 'Checking if archive template exists'
		);
		
		// Test 2: Single template exists
		$tests['single_template'] = array(
			'name' => 'Single Template',
			'status' => file_exists( AIDDATA_LMS_PATH . 'templates/single-aiddata_tutorial.php' ),
			'message' => 'Checking if single template exists'
		);
		
		// Test 3: Tutorial card template exists
		$tests['card_template'] = array(
			'name' => 'Tutorial Card Template',
			'status' => file_exists( AIDDATA_LMS_PATH . 'templates/template-parts/content-tutorial-card.php' ),
			'message' => 'Checking if tutorial card template exists'
		);
		
		// Test 4: Enrollment button template exists
		$tests['enrollment_button'] = array(
			'name' => 'Enrollment Button Template',
			'status' => file_exists( AIDDATA_LMS_PATH . 'templates/template-parts/enrollment-button.php' ),
			'message' => 'Checking if enrollment button template exists'
		);
		
		// Test 5: Frontend CSS exists
		$tests['frontend_css'] = array(
			'name' => 'Frontend CSS Files',
			'status' => file_exists( AIDDATA_LMS_PATH . 'assets/css/frontend/tutorial-display.css' ) ||
						file_exists( AIDDATA_LMS_PATH . 'assets/css/frontend.css' ),
			'message' => 'Checking if frontend CSS exists'
		);
		
		return $tests;
	}
	
	/**
	 * Test active tutorial interface
	 *
	 * @return array Test results
	 */
	private static function test_active_tutorial(): array {
		$tests = array();
		
		// Test 1: Active tutorial template exists
		$tests['active_template'] = array(
			'name' => 'Active Tutorial Template',
			'status' => file_exists( AIDDATA_LMS_PATH . 'templates/template-parts/active-tutorial.php' ),
			'message' => 'Checking if active tutorial template exists'
		);
		
		// Test 2: Navigation JavaScript exists
		$tests['navigation_js'] = array(
			'name' => 'Navigation JavaScript',
			'status' => file_exists( AIDDATA_LMS_PATH . 'assets/js/frontend/tutorial-navigation.js' ) ||
						file_exists( AIDDATA_LMS_PATH . 'assets/js/frontend.js' ),
			'message' => 'Checking if navigation JavaScript exists'
		);
		
		// Test 3: Navigation CSS exists
		$tests['navigation_css'] = array(
			'name' => 'Navigation CSS',
			'status' => file_exists( AIDDATA_LMS_PATH . 'assets/css/frontend/tutorial-navigation.css' ) ||
						file_exists( AIDDATA_LMS_PATH . 'assets/css/frontend.css' ),
			'message' => 'Checking if navigation CSS exists'
		);
		
		// Test 4: AJAX handler registered
		$tests['ajax_handler'] = array(
			'name' => 'AJAX Load Step Handler',
			'status' => self::check_ajax_action( 'aiddata_lms_load_step' ),
			'message' => 'Checking if step loading AJAX handler is registered'
		);
		
		// Test 5: Progress update handler
		$tests['progress_handler'] = array(
			'name' => 'Progress Update Handler',
			'status' => self::check_ajax_action( 'aiddata_lms_update_step_progress' ),
			'message' => 'Checking if progress update handler is registered'
		);
		
		return $tests;
	}
	
	/**
	 * Test progress persistence
	 *
	 * @return array Test results
	 */
	private static function test_progress_persistence(): array {
		$tests = array();
		
		// Test 1: Progress tracking class exists
		$tests['progress_class'] = array(
			'name' => 'Progress Tracking Class',
			'status' => class_exists( 'AidData_LMS_Tutorial_Progress' ),
			'message' => 'Checking if progress tracking class exists'
		);
		
		// Test 2: Milestone class exists
		$tests['milestone_class'] = array(
			'name' => 'Progress Milestones Class',
			'status' => class_exists( 'AidData_LMS_Progress_Milestones' ),
			'message' => 'Checking if progress milestones class exists (optional)'
		);
		
		// Test 3: Time tracking handler
		$tests['time_tracking'] = array(
			'name' => 'Time Tracking Handler',
			'status' => self::check_ajax_action( 'aiddata_lms_update_time_spent' ),
			'message' => 'Checking if time tracking handler is registered'
		);
		
		// Test 4: Progress database table
		$tests['progress_table'] = array(
			'name' => 'Progress Database Table',
			'status' => self::check_table_exists( 'aiddata_lms_tutorial_progress' ),
			'message' => 'Checking if progress table exists'
		);
		
		return $tests;
	}
	
	/**
	 * Test integration with other systems
	 *
	 * @return array Test results
	 */
	private static function test_integration(): array {
		$tests = array();
		
		// Test 1: Enrollment system integration
		$tests['enrollment_integration'] = array(
			'name' => 'Enrollment System',
			'status' => class_exists( 'AidData_LMS_Tutorial_Enrollment' ),
			'message' => 'Checking enrollment system integration'
		);
		
		// Test 2: Post type registered
		$tests['post_type'] = array(
			'name' => 'Tutorial Post Type',
			'status' => post_type_exists( 'aiddata_tutorial' ),
			'message' => 'Checking if tutorial post type is registered'
		);
		
		// Test 3: Taxonomies registered
		$tests['taxonomies'] = array(
			'name' => 'Tutorial Taxonomies',
			'status' => taxonomy_exists( 'aiddata_tutorial_cat' ) && taxonomy_exists( 'aiddata_tutorial_difficulty' ),
			'message' => 'Checking if tutorial taxonomies are registered'
		);
		
		// Test 4: Email system available
		$tests['email_system'] = array(
			'name' => 'Email System',
			'status' => class_exists( 'AidData_LMS_Email_Queue' ) || class_exists( 'AidData_LMS_Email_Templates' ),
			'message' => 'Checking email system availability'
		);
		
		// Test 5: Analytics tracking available
		$tests['analytics'] = array(
			'name' => 'Analytics System',
			'status' => class_exists( 'AidData_LMS_Analytics' ),
			'message' => 'Checking analytics system availability'
		);
		
		return $tests;
	}
	
	/**
	 * Test security implementation
	 *
	 * @return array Test results
	 */
	private static function test_security(): array {
		$tests = array();
		
		// Test 1: Nonce verification in meta boxes
		$tests['nonce_verification'] = array(
			'name' => 'Nonce Verification',
			'status' => self::check_nonce_usage(),
			'message' => 'Checking for nonce verification in code'
		);
		
		// Test 2: Capability checks
		$tests['capability_checks'] = array(
			'name' => 'Capability Checks',
			'status' => self::check_capability_usage(),
			'message' => 'Checking for capability checks in code'
		);
		
		// Test 3: Input sanitization
		$tests['sanitization'] = array(
			'name' => 'Input Sanitization',
			'status' => self::check_sanitization_usage(),
			'message' => 'Checking for input sanitization'
		);
		
		// Test 4: Output escaping
		$tests['output_escaping'] = array(
			'name' => 'Output Escaping',
			'status' => self::check_escaping_usage(),
			'message' => 'Checking for output escaping'
		);
		
		return $tests;
	}
	
	/**
	 * Test performance benchmarks
	 *
	 * @return array Test results
	 */
	private static function test_performance(): array {
		$tests = array();
		
		// Test 1: Asset files size check
		$js_size = 0;
		$css_size = 0;
		
		if ( file_exists( AIDDATA_LMS_PATH . 'assets/js/admin/tutorial-step-builder.js' ) ) {
			$js_size += filesize( AIDDATA_LMS_PATH . 'assets/js/admin/tutorial-step-builder.js' );
		}
		if ( file_exists( AIDDATA_LMS_PATH . 'assets/css/admin/tutorial-meta-boxes.css' ) ) {
			$css_size += filesize( AIDDATA_LMS_PATH . 'assets/css/admin/tutorial-meta-boxes.css' );
		}
		
		$tests['asset_size'] = array(
			'name' => 'Asset File Sizes',
			'status' => ( $js_size < 100000 && $css_size < 50000 ), // Under 100KB JS, 50KB CSS
			'message' => sprintf( 'JS: %.2f KB, CSS: %.2f KB', $js_size / 1024, $css_size / 1024 )
		);
		
		// Test 2: Database query optimization
		$tests['db_queries'] = array(
			'name' => 'Database Queries',
			'status' => self::check_query_optimization(),
			'message' => 'Checking for query optimization (prepared statements)'
		);
		
		// Test 3: Caching implementation
		$tests['caching'] = array(
			'name' => 'Caching Usage',
			'status' => self::check_cache_usage(),
			'message' => 'Checking for caching implementation'
		);
		
		return $tests;
	}
	
	/**
	 * Test accessibility compliance
	 *
	 * @return array Test results
	 */
	private static function test_accessibility(): array {
		$tests = array();
		
		// Test 1: ARIA labels in templates
		$tests['aria_labels'] = array(
			'name' => 'ARIA Labels',
			'status' => self::check_aria_usage(),
			'message' => 'Checking for ARIA labels in templates'
		);
		
		// Test 2: Form labels
		$tests['form_labels'] = array(
			'name' => 'Form Labels',
			'status' => self::check_form_labels(),
			'message' => 'Checking for proper form labels'
		);
		
		// Test 3: Keyboard navigation
		$tests['keyboard_nav'] = array(
			'name' => 'Keyboard Navigation Support',
			'status' => self::check_keyboard_support(),
			'message' => 'Checking for keyboard navigation support'
		);
		
		// Test 4: Alt text on images
		$tests['alt_text'] = array(
			'name' => 'Image Alt Text',
			'status' => self::check_alt_text(),
			'message' => 'Checking for image alt text'
		);
		
		return $tests;
	}
	
	/**
	 * Generate comprehensive validation report
	 *
	 * @param array $results Validation results
	 * @return string HTML report
	 */
	public static function generate_report( array $results ): string {
		$total_tests = 0;
		$passed_tests = 0;
		$failed_tests = 0;
		
		// Count tests
		foreach ( $results as $category => $tests ) {
			foreach ( $tests as $test ) {
				$total_tests++;
				if ( $test['status'] ) {
					$passed_tests++;
				} else {
					$failed_tests++;
				}
			}
		}
		
		$pass_rate = $total_tests > 0 ? round( ( $passed_tests / $total_tests ) * 100, 2 ) : 0;
		
		ob_start();
		?>
		<div class="aiddata-validation-report">
			<div class="validation-summary">
				<h2><?php esc_html_e( 'Phase 2 Validation Report', 'aiddata-lms' ); ?></h2>
				<div class="summary-stats">
					<div class="stat-box <?php echo $pass_rate >= 80 ? 'stat-success' : 'stat-warning'; ?>">
						<div class="stat-value"><?php echo esc_html( $pass_rate ); ?>%</div>
						<div class="stat-label"><?php esc_html_e( 'Pass Rate', 'aiddata-lms' ); ?></div>
					</div>
					<div class="stat-box">
						<div class="stat-value"><?php echo esc_html( $passed_tests ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Tests Passed', 'aiddata-lms' ); ?></div>
					</div>
					<div class="stat-box">
						<div class="stat-value"><?php echo esc_html( $failed_tests ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Tests Failed', 'aiddata-lms' ); ?></div>
					</div>
					<div class="stat-box">
						<div class="stat-value"><?php echo esc_html( $total_tests ); ?></div>
						<div class="stat-label"><?php esc_html_e( 'Total Tests', 'aiddata-lms' ); ?></div>
					</div>
				</div>
			</div>
			
			<div class="validation-results">
				<?php foreach ( $results as $category => $tests ) : ?>
					<div class="validation-category">
						<h3><?php echo esc_html( ucwords( str_replace( '_', ' ', $category ) ) ); ?></h3>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<tr>
									<th width="5%"><?php esc_html_e( 'Status', 'aiddata-lms' ); ?></th>
									<th width="35%"><?php esc_html_e( 'Test Name', 'aiddata-lms' ); ?></th>
									<th width="60%"><?php esc_html_e( 'Message', 'aiddata-lms' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $tests as $test_key => $test ) : ?>
									<tr>
										<td class="test-status">
											<?php if ( $test['status'] ) : ?>
												<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
											<?php else : ?>
												<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
											<?php endif; ?>
										</td>
										<td class="test-name"><?php echo esc_html( $test['name'] ); ?></td>
										<td class="test-message"><?php echo esc_html( $test['message'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endforeach; ?>
			</div>
			
			<div class="validation-recommendations">
				<h3><?php esc_html_e( 'Recommendations', 'aiddata-lms' ); ?></h3>
				<?php if ( $pass_rate >= 90 ) : ?>
					<div class="notice notice-success">
						<p><strong><?php esc_html_e( 'Excellent!', 'aiddata-lms' ); ?></strong> <?php esc_html_e( 'Phase 2 implementation is in great shape. All critical features are functional.', 'aiddata-lms' ); ?></p>
					</div>
				<?php elseif ( $pass_rate >= 75 ) : ?>
					<div class="notice notice-warning">
						<p><strong><?php esc_html_e( 'Good Progress', 'aiddata-lms' ); ?></strong> <?php esc_html_e( 'Most features are implemented. Address failing tests before proceeding to Phase 3.', 'aiddata-lms' ); ?></p>
					</div>
				<?php else : ?>
					<div class="notice notice-error">
						<p><strong><?php esc_html_e( 'Action Required', 'aiddata-lms' ); ?></strong> <?php esc_html_e( 'Several critical features are missing or non-functional. Complete Phase 2 implementation before validation.', 'aiddata-lms' ); ?></p>
					</div>
				<?php endif; ?>
				
				<h4><?php esc_html_e( 'Next Steps:', 'aiddata-lms' ); ?></h4>
				<ul>
					<?php if ( $failed_tests > 0 ) : ?>
						<li><?php esc_html_e( 'Review and fix all failing tests', 'aiddata-lms' ); ?></li>
						<li><?php esc_html_e( 'Implement missing features or files', 'aiddata-lms' ); ?></li>
					<?php endif; ?>
					<li><?php esc_html_e( 'Perform manual testing of all user workflows', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Run cross-browser compatibility tests', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Test mobile responsiveness', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Verify accessibility with screen readers', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Check performance with Query Monitor', 'aiddata-lms' ); ?></li>
					<li><?php esc_html_e( 'Review documentation completeness', 'aiddata-lms' ); ?></li>
				</ul>
			</div>
			
			<div class="validation-timestamp">
				<p><em><?php printf( esc_html__( 'Report generated on: %s', 'aiddata-lms' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ); ?></em></p>
			</div>
		</div>
		
		<style>
		.aiddata-validation-report {
			max-width: 1200px;
			margin: 20px auto;
		}
		.validation-summary {
			background: #fff;
			padding: 20px;
			border: 1px solid #ccd0d4;
			margin-bottom: 20px;
		}
		.summary-stats {
			display: flex;
			gap: 20px;
			margin-top: 20px;
		}
		.stat-box {
			flex: 1;
			text-align: center;
			padding: 20px;
			background: #f0f0f1;
			border-radius: 4px;
		}
		.stat-box.stat-success {
			background: #d4edda;
			border: 1px solid #c3e6cb;
		}
		.stat-box.stat-warning {
			background: #fff3cd;
			border: 1px solid #ffeaa7;
		}
		.stat-value {
			font-size: 32px;
			font-weight: 700;
			color: #1d2327;
		}
		.stat-label {
			margin-top: 5px;
			font-size: 14px;
			color: #50575e;
		}
		.validation-category {
			background: #fff;
			padding: 20px;
			border: 1px solid #ccd0d4;
			margin-bottom: 20px;
		}
		.validation-category h3 {
			margin-top: 0;
			padding-bottom: 10px;
			border-bottom: 2px solid #2271b1;
		}
		.test-status {
			text-align: center;
		}
		.validation-recommendations {
			background: #fff;
			padding: 20px;
			border: 1px solid #ccd0d4;
			margin-bottom: 20px;
		}
		.validation-timestamp {
			text-align: center;
			color: #50575e;
		}
		</style>
		<?php
		
		return ob_get_clean();
	}
	
	/**
	 * Helper: Check if meta boxes exist
	 *
	 * @return bool
	 */
	private static function check_meta_boxes_exist(): bool {
		global $wp_meta_boxes;
		
		if ( ! isset( $wp_meta_boxes['aiddata_tutorial'] ) ) {
			return false;
		}
		
		// Check for any registered meta boxes for tutorial post type
		foreach ( $wp_meta_boxes['aiddata_tutorial'] as $context => $priorities ) {
			foreach ( $priorities as $priority => $boxes ) {
				if ( ! empty( $boxes ) ) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Helper: Check if AJAX action is registered
	 *
	 * @param string $action Action name without wp_ajax_ prefix
	 * @return bool
	 */
	private static function check_ajax_action( string $action ): bool {
		return has_action( 'wp_ajax_' . $action ) || has_action( 'wp_ajax_nopriv_' . $action );
	}
	
	/**
	 * Helper: Check if database table exists
	 *
	 * @param string $table Table name without prefix
	 * @return bool
	 */
	private static function check_table_exists( string $table ): bool {
		global $wpdb;
		
		$table_name = $wpdb->prefix . $table;
		$result = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		
		return $result === $table_name;
	}
	
	/**
	 * Helper: Check for nonce usage in code
	 *
	 * @return bool
	 */
	private static function check_nonce_usage(): bool {
		$meta_boxes_file = AIDDATA_LMS_PATH . 'includes/admin/class-aiddata-lms-tutorial-meta-boxes.php';
		
		if ( ! file_exists( $meta_boxes_file ) ) {
			return false;
		}
		
		$content = file_get_contents( $meta_boxes_file );
		return strpos( $content, 'wp_verify_nonce' ) !== false || strpos( $content, 'wp_nonce_field' ) !== false;
	}
	
	/**
	 * Helper: Check for capability checks in code
	 *
	 * @return bool
	 */
	private static function check_capability_usage(): bool {
		$meta_boxes_file = AIDDATA_LMS_PATH . 'includes/admin/class-aiddata-lms-tutorial-meta-boxes.php';
		
		if ( ! file_exists( $meta_boxes_file ) ) {
			return false;
		}
		
		$content = file_get_contents( $meta_boxes_file );
		return strpos( $content, 'current_user_can' ) !== false;
	}
	
	/**
	 * Helper: Check for sanitization usage
	 *
	 * @return bool
	 */
	private static function check_sanitization_usage(): bool {
		$meta_boxes_file = AIDDATA_LMS_PATH . 'includes/admin/class-aiddata-lms-tutorial-meta-boxes.php';
		
		if ( ! file_exists( $meta_boxes_file ) ) {
			return false;
		}
		
		$content = file_get_contents( $meta_boxes_file );
		return strpos( $content, 'sanitize_' ) !== false;
	}
	
	/**
	 * Helper: Check for output escaping
	 *
	 * @return bool
	 */
	private static function check_escaping_usage(): bool {
		$template_files = glob( AIDDATA_LMS_PATH . 'templates/**/*.php' );
		
		if ( empty( $template_files ) ) {
			return false;
		}
		
		foreach ( $template_files as $file ) {
			$content = file_get_contents( $file );
			if ( strpos( $content, 'esc_html' ) !== false || 
				 strpos( $content, 'esc_attr' ) !== false || 
				 strpos( $content, 'esc_url' ) !== false ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Helper: Check for query optimization
	 *
	 * @return bool
	 */
	private static function check_query_optimization(): bool {
		$files = glob( AIDDATA_LMS_PATH . 'includes/**/*.php' );
		
		if ( empty( $files ) ) {
			return false;
		}
		
		foreach ( $files as $file ) {
			$content = file_get_contents( $file );
			if ( strpos( $content, '$wpdb->prepare' ) !== false ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Helper: Check for cache usage
	 *
	 * @return bool
	 */
	private static function check_cache_usage(): bool {
		$files = glob( AIDDATA_LMS_PATH . 'includes/**/*.php' );
		
		if ( empty( $files ) ) {
			return false;
		}
		
		foreach ( $files as $file ) {
			$content = file_get_contents( $file );
			if ( strpos( $content, 'wp_cache_get' ) !== false || 
				 strpos( $content, 'wp_cache_set' ) !== false ||
				 strpos( $content, 'get_transient' ) !== false ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Helper: Check for ARIA usage
	 *
	 * @return bool
	 */
	private static function check_aria_usage(): bool {
		$template_files = glob( AIDDATA_LMS_PATH . 'templates/**/*.php' );
		
		if ( empty( $template_files ) ) {
			return false;
		}
		
		foreach ( $template_files as $file ) {
			$content = file_get_contents( $file );
			if ( strpos( $content, 'aria-' ) !== false ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Helper: Check for form labels
	 *
	 * @return bool
	 */
	private static function check_form_labels(): bool {
		$meta_boxes_file = AIDDATA_LMS_PATH . 'includes/admin/views/tutorial-step-builder.php';
		
		if ( ! file_exists( $meta_boxes_file ) ) {
			return false;
		}
		
		$content = file_get_contents( $meta_boxes_file );
		return strpos( $content, '<label' ) !== false;
	}
	
	/**
	 * Helper: Check for keyboard support
	 *
	 * @return bool
	 */
	private static function check_keyboard_support(): bool {
		$js_files = glob( AIDDATA_LMS_PATH . 'assets/js/**/*.js' );
		
		if ( empty( $js_files ) ) {
			return false;
		}
		
		foreach ( $js_files as $file ) {
			$content = file_get_contents( $file );
			if ( strpos( $content, 'keydown' ) !== false || 
				 strpos( $content, 'keypress' ) !== false ||
				 strpos( $content, 'keyboard' ) !== false ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Helper: Check for alt text
	 *
	 * @return bool
	 */
	private static function check_alt_text(): bool {
		$template_files = glob( AIDDATA_LMS_PATH . 'templates/**/*.php' );
		
		if ( empty( $template_files ) ) {
			return false;
		}
		
		foreach ( $template_files as $file ) {
			$content = file_get_contents( $file );
			if ( strpos( $content, 'alt=' ) !== false ) {
				return true;
			}
		}
		
		return false;
	}
}

