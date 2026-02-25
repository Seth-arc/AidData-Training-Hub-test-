<?php
/**
 * Plugin Name: AidData Auth Runtime Guards
 * Description: Runtime toggles for auth behavior on Railway.
 */

$aiddata_bool_env = static function ($name, $default = false) {
    $value = getenv($name);
    if ($value === false || $value === '') {
        return $default;
    }

    return in_array(strtolower((string) $value), array('1', 'true', 'yes', 'on'), true);
};

// Optional emergency switch to disable WP Cassify without dashboard access.
if ($aiddata_bool_env('AIDDATA_DISABLE_CASSIFY', false)) {
    add_filter('option_active_plugins', static function ($plugins) {
        if (!is_array($plugins)) {
            return $plugins;
        }

        return array_values(array_filter($plugins, static function ($plugin) {
            return $plugin !== 'wp-cassify/wp-cassify.php';
        }));
    }, 1);

    add_filter('site_option_active_sitewide_plugins', static function ($plugins) {
        if (!is_array($plugins)) {
            return $plugins;
        }

        unset($plugins['wp-cassify/wp-cassify.php']);
        return $plugins;
    }, 1);
}

