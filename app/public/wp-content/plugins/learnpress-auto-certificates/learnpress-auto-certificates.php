<?php
/**
 * Plugin Name: LearnPress Auto Certificates
 * Description: Issue certificates automatically when a LearnPress quiz score >= threshold.
 * Version: 4.0.0
 * Require_LP_Version: 4.0.0
 * Tested_LP_Version: 4.2.7
 */


// If LearnPress is not active, do nothing
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Define constants.
 */
define( 'LP_ADDON_AUTO_CERTIFICATES_FILE', __FILE__ );
define( 'LP_ADDON_AUTO_CERTIFICATES_PATH', plugin_dir_path( __FILE__ ) );
define( 'LP_ADDON_AUTO_CERTIFICATES_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load Addon
 */
add_action( 'learn-press/ready', 'lp_load_addon_auto_certificates' );

function lp_load_addon_auto_certificates() {
    require_once 'inc/class-lp-addon-auto-certificates.php';
    LP_Addon::load( 'LP_Addon_Auto_Certificates', 'inc/class-lp-addon-auto-certificates.php', __FILE__ );
}

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'lpac_activation');
function lpac_activation() {
    // Ensure LearnPress is effectively active and loaded
    if ( ! class_exists( 'LP_Addon' ) ) {
        // You might want to display an error or notice here, but for now just return to avoid fatal error
        return;
    }
    // We can't use LP_Addon::load here because this runs on activation, potentially before LP is ready or loaded
    require_once 'inc/class-lp-addon-auto-certificates.php';
    LP_Addon_Auto_Certificates::create_table();
}

