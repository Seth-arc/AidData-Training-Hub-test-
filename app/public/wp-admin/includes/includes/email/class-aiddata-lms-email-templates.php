<?php
/**
 * Email Template Manager
 *
 * Handles email template loading, variable replacement, HTML formatting,
 * and template management for the AidData LMS system.
 *
 * @package AidData_LMS
 * @subpackage Email
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email Template Manager Class
 *
 * Manages email templates including loading, rendering, variable replacement,
 * and template validation.
 *
 * @since 1.0.0
 */
class AidData_LMS_Email_Templates {

	/**
	 * Render email template
	 *
	 * Loads template file, applies filters, and replaces variables with values.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_id Template identifier (without .html extension).
	 * @param array  $variables   Associative array of variables to replace.
	 * @return string Rendered template HTML or empty string on failure.
	 */
	public function render_template( string $template_id, array $variables = [] ): string {
		$content = $this->load_template_file( $template_id );

		if ( empty( $content ) ) {
			error_log( sprintf( 'Email template not found: %s', $template_id ) );
			return '';
		}

		// Apply filters for customization.
		$content   = apply_filters( 'aiddata_lms_email_template_content', $content, $template_id );
		$variables = apply_filters( 'aiddata_lms_email_template_variables', $variables, $template_id );

		// Replace variables.
		$content = $this->replace_variables( $content, $variables );

		return $content;
	}

	/**
	 * Get template content
	 *
	 * Retrieves raw template content without variable replacement.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_id Template identifier.
	 * @return string Template content or empty string if not found.
	 */
	public function get_template_content( string $template_id ): string {
		return $this->load_template_file( $template_id );
	}

	/**
	 * Replace variables in template
	 *
	 * Replaces template variables with actual values. Variables should be
	 * in {variable_name} format.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content   Template content with variables.
	 * @param array  $variables Associative array of variable values.
	 * @return string Content with variables replaced.
	 */
	public function replace_variables( string $content, array $variables ): string {
		$defaults  = $this->get_default_variables();
		$variables = array_merge( $defaults, $variables );

		foreach ( $variables as $key => $value ) {
			// Ensure key has curly braces.
			if ( strpos( $key, '{' ) !== 0 ) {
				$key = '{' . $key . '}';
			}

			// Convert value to string.
			$value = (string) $value;

			$content = str_replace( $key, $value, $content );
		}

		return $content;
	}

	/**
	 * Get available variables
	 *
	 * Returns list of all available template variables with descriptions.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of variable names and descriptions.
	 */
	public function get_available_variables(): array {
		return array(
			'{user_name}'           => __( 'User display name', 'aiddata-lms' ),
			'{user_email}'          => __( 'User email address', 'aiddata-lms' ),
			'{user_first_name}'     => __( 'User first name', 'aiddata-lms' ),
			'{user_last_name}'      => __( 'User last name', 'aiddata-lms' ),
			'{tutorial_title}'      => __( 'Tutorial title', 'aiddata-lms' ),
			'{tutorial_url}'        => __( 'Tutorial URL', 'aiddata-lms' ),
			'{tutorial_description}' => __( 'Tutorial description', 'aiddata-lms' ),
			'{progress_percent}'    => __( 'Progress percentage', 'aiddata-lms' ),
			'{completion_date}'     => __( 'Tutorial completion date', 'aiddata-lms' ),
			'{enrolled_date}'       => __( 'Enrollment date', 'aiddata-lms' ),
			'{certificate_url}'     => __( 'Certificate download URL', 'aiddata-lms' ),
			'{certificate_id}'      => __( 'Certificate ID', 'aiddata-lms' ),
			'{quiz_score}'          => __( 'Quiz score', 'aiddata-lms' ),
			'{quiz_attempts}'       => __( 'Quiz attempts', 'aiddata-lms' ),
			'{quiz_passing_score}'  => __( 'Quiz passing score', 'aiddata-lms' ),
			'{site_name}'           => __( 'Site name', 'aiddata-lms' ),
			'{site_url}'            => __( 'Site URL', 'aiddata-lms' ),
			'{site_admin_email}'    => __( 'Site admin email', 'aiddata-lms' ),
			'{current_date}'        => __( 'Current date', 'aiddata-lms' ),
			'{current_year}'        => __( 'Current year', 'aiddata-lms' ),
		);
	}

	/**
	 * Get available templates
	 *
	 * Returns list of available email templates.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of template IDs with descriptions.
	 */
	public function get_available_templates(): array {
		$templates = array(
			'enrollment-confirmation'  => __( 'Enrollment Confirmation', 'aiddata-lms' ),
			'progress-reminder'        => __( 'Progress Reminder', 'aiddata-lms' ),
			'completion-congratulations' => __( 'Completion Congratulations', 'aiddata-lms' ),
		);

		return apply_filters( 'aiddata_lms_available_templates', $templates );
	}

	/**
	 * Validate template
	 *
	 * Checks if template content is valid HTML and contains required structure.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Template content to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public function validate_template( string $content ): bool {
		if ( empty( $content ) ) {
			return false;
		}

		// Check for basic HTML structure.
		if ( stripos( $content, '<html' ) === false || stripos( $content, '</html>' ) === false ) {
			return false;
		}

		if ( stripos( $content, '<body' ) === false || stripos( $content, '</body>' ) === false ) {
			return false;
		}

		return true;
	}

	/**
	 * Load template file
	 *
	 * Loads template from plugin directory or theme override.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_id Template identifier.
	 * @return string Template content or empty string if not found.
	 */
	private function load_template_file( string $template_id ): string {
		// Check for theme override first.
		$theme_template = get_stylesheet_directory() . '/aiddata-lms/email/' . $template_id . '.html';

		if ( file_exists( $theme_template ) ) {
			return file_get_contents( $theme_template );
		}

		// Fall back to plugin template.
		$plugin_template = AIDDATA_LMS_PATH . 'assets/templates/email/' . $template_id . '.html';

		if ( file_exists( $plugin_template ) ) {
			return file_get_contents( $plugin_template );
		}

		// Template not found.
		return '';
	}

	/**
	 * Get default variables
	 *
	 * Returns default values for all template variables.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of default variable values.
	 */
	private function get_default_variables(): array {
		return array(
			'{user_name}'           => '',
			'{user_email}'          => '',
			'{user_first_name}'     => '',
			'{user_last_name}'      => '',
			'{tutorial_title}'      => '',
			'{tutorial_url}'        => '',
			'{tutorial_description}' => '',
			'{progress_percent}'    => '0',
			'{completion_date}'     => '',
			'{enrolled_date}'       => '',
			'{certificate_url}'     => '',
			'{certificate_id}'      => '',
			'{quiz_score}'          => '',
			'{quiz_attempts}'       => '',
			'{quiz_passing_score}'  => '',
			'{site_name}'           => get_bloginfo( 'name' ),
			'{site_url}'            => home_url(),
			'{site_admin_email}'    => get_option( 'admin_email' ),
			'{current_date}'        => wp_date( get_option( 'date_format' ) ),
			'{current_year}'        => wp_date( 'Y' ),
		);
	}
}

