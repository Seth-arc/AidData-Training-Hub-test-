<?php
/**
 * Plugin Name: Disable External Requests
 * Description: Prevents WordPress from making external HTTP requests that can cause timeouts in production
 * Version: 1.0
 * Author: Auto-generated for Railway deployment
 */

// Disable all update checks to prevent timeouts
add_filter('pre_site_transient_update_core', '__return_null');
add_filter('pre_site_transient_update_plugins', '__return_null');
add_filter('pre_site_transient_update_themes', '__return_null');

// Disable plugin/theme editor
defined('DISALLOW_FILE_EDIT') || define('DISALLOW_FILE_EDIT', true);

// Remove update nag
add_action('admin_menu', function() {
    remove_action('admin_notices', 'update_nag', 3);
});

// Disable dashboard widgets that make external requests
add_action('wp_dashboard_setup', function() {
    remove_meta_box('dashboard_primary', 'dashboard', 'side');      // WordPress Events and News
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');    // Other WordPress News
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); // Recent Drafts
}, 999);
