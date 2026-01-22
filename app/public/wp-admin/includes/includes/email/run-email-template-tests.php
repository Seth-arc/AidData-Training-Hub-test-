<?php
/**
 * Email Template Test Runner
 *
 * Admin page for running email template system tests.
 *
 * @package AidData_LMS
 * @subpackage Email/Tests
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check user permissions.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'aiddata-lms' ) );
}

// Load test class.
require_once AIDDATA_LMS_PATH . 'includes/email/class-aiddata-lms-email-templates-test.php';

// Run tests if requested.
$tests_run = false;
if ( isset( $_GET['run_tests'] ) && $_GET['run_tests'] === '1' ) {
	// Verify nonce.
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'run_email_template_tests' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'aiddata-lms' ) );
	}

	$tests_run = true;
	$test      = new AidData_LMS_Email_Templates_Test();
	$results   = $test->run_tests();
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Email Template System Tests', 'aiddata-lms' ); ?></h1>
	
	<div class="card">
		<h2><?php esc_html_e( 'Test Suite Information', 'aiddata-lms' ); ?></h2>
		<p><?php esc_html_e( 'This test suite validates the email template system including:', 'aiddata-lms' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'Template loading and rendering', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Variable replacement', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Email notification triggers', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Milestone tracking', 'aiddata-lms' ); ?></li>
			<li><?php esc_html_e( 'Template filters', 'aiddata-lms' ); ?></li>
		</ul>
		
		<p>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'run_tests', '1' ), 'run_email_template_tests' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Run Tests', 'aiddata-lms' ); ?>
			</a>
		</p>
	</div>

	<?php
	if ( $tests_run ) {
		echo '<h2>' . esc_html__( 'Test Results', 'aiddata-lms' ) . '</h2>';
		$test->display_results();
	}
	?>
	
	<div class="card" style="margin-top: 20px;">
		<h2><?php esc_html_e( 'Template Files', 'aiddata-lms' ); ?></h2>
		<p><?php esc_html_e( 'Email templates are located in:', 'aiddata-lms' ); ?></p>
		<ul>
			<li><code>assets/templates/email/enrollment-confirmation.html</code></li>
			<li><code>assets/templates/email/progress-reminder.html</code></li>
			<li><code>assets/templates/email/completion-congratulations.html</code></li>
		</ul>
		<p><?php esc_html_e( 'You can override these templates by copying them to your theme:', 'aiddata-lms' ); ?></p>
		<p><code>your-theme/aiddata-lms/email/template-name.html</code></p>
	</div>
	
	<div class="card" style="margin-top: 20px;">
		<h2><?php esc_html_e( 'Available Variables', 'aiddata-lms' ); ?></h2>
		<?php
		$template_manager = new AidData_LMS_Email_Templates();
		$variables        = $template_manager->get_available_variables();
		?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Variable', 'aiddata-lms' ); ?></th>
					<th><?php esc_html_e( 'Description', 'aiddata-lms' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $variables as $var => $desc ) : ?>
				<tr>
					<td><code><?php echo esc_html( $var ); ?></code></td>
					<td><?php echo esc_html( $desc ); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

