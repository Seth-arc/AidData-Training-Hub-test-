<?php
/**
 * Internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since 2.0.0
 */
class AidData_LMS_i18n {

	/**
	 * Load the plugin text domain for translation
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			'aiddata-lms',
			false,
			dirname( AIDDATA_LMS_BASENAME ) . '/languages/'
		);
	}

	/**
	 * Get the text domain
	 *
	 * @since  2.0.0
	 * @return string The text domain used for translations
	 */
	public function get_text_domain(): string {
		return 'aiddata-lms';
	}

	/**
	 * Get the path to the languages directory
	 *
	 * @since  2.0.0
	 * @return string Path to the languages directory
	 */
	public function get_languages_path(): string {
		return dirname( AIDDATA_LMS_BASENAME ) . '/languages/';
	}

	/**
	 * Check if the plugin text domain is loaded
	 *
	 * @since  2.0.0
	 * @return bool True if text domain is loaded, false otherwise
	 */
	public function is_text_domain_loaded(): bool {
		return is_textdomain_loaded( 'aiddata-lms' );
	}
}

