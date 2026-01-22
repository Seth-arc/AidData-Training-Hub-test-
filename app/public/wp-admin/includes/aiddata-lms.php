<?php
/**
 * Plugin Name: AidData LMS Tutorial Builder
 * Plugin URI: https://aiddata.org/
 * Description: Comprehensive Learning Management System for creating interactive tutorials with video tracking, quizzes, enrollments, and certificates.
 * Version: 2.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: AidData
 * Author URI: https://aiddata.org/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aiddata-lms
 * Domain Path: /languages
 *
 * @package AidData_LMS
 * @version 2.0.0
 */

// Security check - Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// PHP Version Check
if ( version_compare( PHP_VERSION, '8.1', '<' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>';
		printf(
			/* translators: %s: Required PHP version */
			esc_html__( 'AidData LMS requires PHP version %s or higher. Please upgrade PHP.', 'aiddata-lms' ),
			'8.1'
		);
		echo '</p></div>';
	} );
	return;
}

// WordPress Version Check
global $wp_version;
if ( version_compare( $wp_version, '6.4', '<' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>';
		printf(
			/* translators: %s: Required WordPress version */
			esc_html__( 'AidData LMS requires WordPress version %s or higher. Please upgrade WordPress.', 'aiddata-lms' ),
			'6.4'
		);
		echo '</p></div>';
	} );
	return;
}

// Define Plugin Constants
define( 'AIDDATA_LMS_VERSION', '2.0.0' );
define( 'AIDDATA_LMS_PATH', plugin_dir_path( __FILE__ ) );
define( 'AIDDATA_LMS_URL', plugin_dir_url( __FILE__ ) );
define( 'AIDDATA_LMS_BASENAME', plugin_basename( __FILE__ ) );
define( 'AIDDATA_LMS_FILE', __FILE__ );

// Load text domain for internationalization
function aiddata_lms_load_textdomain() {
	load_plugin_textdomain( 'aiddata-lms', false, dirname( AIDDATA_LMS_BASENAME ) . '/languages' );
}
add_action( 'plugins_loaded', 'aiddata_lms_load_textdomain' );

// Require autoloader
require_once AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-autoloader.php';

// Register autoloader
if ( class_exists( 'AidData_LMS_Autoloader' ) ) {
	AidData_LMS_Autoloader::register();
}

/**
 * Initialize the plugin
 *
 * @return AidData_LMS|null Plugin instance or null if autoloader failed
 */
function aiddata_lms_init() {
	if ( class_exists( 'AidData_LMS' ) ) {
		$plugin = AidData_LMS::instance();
		$plugin->run();
		return $plugin;
	}
	return null;
}
add_action( 'plugins_loaded', 'aiddata_lms_init' );

/**
 * Plugin Activation Hook
 */
function aiddata_lms_activate() {
	// Require install class
	require_once AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-install.php';
	
	if ( class_exists( 'AidData_LMS_Install' ) ) {
		AidData_LMS_Install::install();
	}
}
register_activation_hook( __FILE__, 'aiddata_lms_activate' );

/**
 * Plugin Deactivation Hook
 */
function aiddata_lms_deactivate() {
	// Flush rewrite rules
	flush_rewrite_rules();
	
	// Additional cleanup can be added here
	do_action( 'aiddata_lms_deactivate' );
}
register_deactivation_hook( __FILE__, 'aiddata_lms_deactivate' );

/**
 * Plugin Uninstall Hook
 * Note: Actual uninstall logic should be in uninstall.php
 */
// Uninstall logic is handled by uninstall.php

