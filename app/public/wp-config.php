<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
$env = static function ($key, $default = null) {
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : $default;
};


/** The name of the database for WordPress */
define( 'DB_NAME', $env( 'DB_NAME', 'wordpress' ) );

/** Database username */
define( 'DB_USER', $env( 'DB_USER', 'wordpress' ) );

/** Database password */
define( 'DB_PASSWORD', $env( 'DB_PASSWORD', '' ) );

/** Database hostname */
define( 'DB_HOST', $env( 'DB_HOST', 'mysql:3306' ) );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

// Increase PHP memory limit for AidData LMS
ini_set('memory_limit', '512M');

// Fix cURL timeout issues
ini_set('default_socket_timeout', 300);
ini_set('max_execution_time', 300);

// HTTP API restrictions are environment-controlled for deployment flexibility.
define('WP_HTTP_BLOCK_EXTERNAL', filter_var($env('WP_HTTP_BLOCK_EXTERNAL', 'false'), FILTER_VALIDATE_BOOLEAN));
if ( WP_HTTP_BLOCK_EXTERNAL ) {
    define('WP_ACCESSIBLE_HOSTS', $env('WP_ACCESSIBLE_HOSTS', 'api.wordpress.org'));
}

// Handle HTTPS and host behind reverse proxy before URL/cookie constants are set.
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $forwarded_proto = trim(strtolower(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
    if ($forwarded_proto === 'https') {
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = '443';
    }
}
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_HOST'])[0]);
}

$request_host = isset($_SERVER['HTTP_HOST']) ? trim((string) $_SERVER['HTTP_HOST']) : '';
$request_host = preg_replace('/:\d+$/', '', $request_host);
$request_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$request_public_url = $request_host ? ($request_scheme . '://' . $request_host) : '';

// Fix Railway env vars that are missing https:// protocol
$railway_public_domain = $env('RAILWAY_PUBLIC_DOMAIN', '');
$default_public_url = '';

if ($request_public_url) {
    $default_public_url = $request_public_url;
} elseif ($railway_public_domain) {
    $default_public_url = (strpos($railway_public_domain, 'http') === 0)
        ? $railway_public_domain
        : 'https://' . $railway_public_domain;
} else {
    $default_public_url = 'http://localhost:8080';
}

$wp_home = trim((string) $env('WP_HOME', $default_public_url));
$wp_siteurl = trim((string) $env('WP_SITEURL', $default_public_url));

// Add https:// if the env vars are missing the protocol
if (strpos($wp_home, 'http') !== 0) {
    $wp_home = 'https://' . $wp_home;
}
if (strpos($wp_siteurl, 'http') !== 0) {
    $wp_siteurl = 'https://' . $wp_siteurl;
}

define( 'WP_HOME', $wp_home );
define( 'WP_SITEURL', $wp_siteurl );
define( 'COOKIE_DOMAIN', false );
define( 'FORCE_SSL_ADMIN', true );
define( 'COOKIEPATH', '/' );
define( 'SITECOOKIEPATH', '/' );
define( 'ADMIN_COOKIE_PATH', '/' );

define( 'WP_ENVIRONMENT_TYPE', $env( 'WP_ENVIRONMENT_TYPE', 'production' ) );

// Disable SSL verification for local development
// add_filter('https_ssl_verify', '__return_false');
// add_filter('https_local_ssl_verify', '__return_false');

// Increase HTTP request timeout
// add_filter('http_request_timeout', function($timeout) {
//     return 30; // 30 seconds
// });

// Set custom user agent
// add_filter('http_headers_useragent', function($user_agent) {
//     return 'AidData Training Hub/1.0 (WordPress; Local Development)';
// });

// Configure cURL options
// add_action('http_api_curl', function($handle) {
//     curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
//     curl_setopt($handle, CURLOPT_TIMEOUT, 30);
//     curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
//     curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
//     curl_setopt($handle, CURLOPT_MAXREDIRS, 5);
// }, 10);

// Keep updates configurable via environment variables.
define('AUTOMATIC_UPDATER_DISABLED', filter_var($env('AUTOMATIC_UPDATER_DISABLED', 'false'), FILTER_VALIDATE_BOOLEAN));
define('WP_AUTO_UPDATE_CORE', $env('WP_AUTO_UPDATE_CORE', 'minor'));
define('DISALLOW_FILE_EDIT', true);

// Production-safe debug defaults; enable explicitly per environment.
$debug_enabled = filter_var( $env( 'WP_DEBUG', 'false' ), FILTER_VALIDATE_BOOLEAN );
define( 'WP_DEBUG', $debug_enabled );
define( 'WP_DEBUG_LOG', filter_var( $env( 'WP_DEBUG_LOG', 'false' ), FILTER_VALIDATE_BOOLEAN ) );
define( 'WP_DEBUG_DISPLAY', filter_var( $env( 'WP_DEBUG_DISPLAY', 'false' ), FILTER_VALIDATE_BOOLEAN ) );
define( 'SCRIPT_DEBUG', $debug_enabled );

define(
    'DISABLE_WP_CRON',
    filter_var(
        $env( 'DISABLE_WP_CRON', WP_ENVIRONMENT_TYPE === 'local' ? 'true' : 'false' ),
        FILTER_VALIDATE_BOOLEAN
    )
);

// Memory debugging
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '512M');

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          $env( 'AUTH_KEY', 'change-me-auth-key' ) );
define( 'SECURE_AUTH_KEY',   $env( 'SECURE_AUTH_KEY', 'change-me-secure-auth-key' ) );
define( 'LOGGED_IN_KEY',     $env( 'LOGGED_IN_KEY', 'change-me-logged-in-key' ) );
define( 'NONCE_KEY',         $env( 'NONCE_KEY', 'change-me-nonce-key' ) );
define( 'AUTH_SALT',         $env( 'AUTH_SALT', 'change-me-auth-salt' ) );
define( 'SECURE_AUTH_SALT',  $env( 'SECURE_AUTH_SALT', 'change-me-secure-auth-salt' ) );
define( 'LOGGED_IN_SALT',    $env( 'LOGGED_IN_SALT', 'change-me-logged-in-salt' ) );
define( 'NONCE_SALT',        $env( 'NONCE_SALT', 'change-me-nonce-salt' ) );
define( 'WP_CACHE_KEY_SALT', $env( 'WP_CACHE_KEY_SALT', 'change-me-cache-key-salt' ) );

if ( 'production' === WP_ENVIRONMENT_TYPE ) {
    $required_secret_constants = array(
        'DB_PASSWORD',
        'WP_HOME',
        'WP_SITEURL',
        'AUTH_KEY',
        'SECURE_AUTH_KEY',
        'LOGGED_IN_KEY',
        'NONCE_KEY',
        'AUTH_SALT',
        'SECURE_AUTH_SALT',
        'LOGGED_IN_SALT',
        'NONCE_SALT',
        'WP_CACHE_KEY_SALT',
    );

    foreach ( $required_secret_constants as $constant_name ) {
        $constant_value = defined( $constant_name ) ? (string) constant( $constant_name ) : '';

        if ( '' === $constant_value || 0 === strpos( $constant_value, 'change-me-' ) ) {
            error_log( 'Critical configuration error: missing required secret ' . $constant_name );
            if ( ! headers_sent() ) {
                header( 'HTTP/1.1 500 Internal Server Error' );
                header( 'Content-Type: text/plain; charset=utf-8' );
            }
            exit( 'Configuration error: missing required secrets.' );
        }
    }

    $validated_wp_home = filter_var( WP_HOME, FILTER_VALIDATE_URL );
    $validated_wp_siteurl = filter_var( WP_SITEURL, FILTER_VALIDATE_URL );

    if ( ! $validated_wp_home || ! $validated_wp_siteurl ) {
        error_log( 'Critical configuration error: invalid WP_HOME or WP_SITEURL URL format' );
        if ( ! headers_sent() ) {
            header( 'HTTP/1.1 500 Internal Server Error' );
            header( 'Content-Type: text/plain; charset=utf-8' );
        }
        exit( 'Configuration error: invalid site URL configuration.' );
    }

    if ( 'https' !== parse_url( WP_HOME, PHP_URL_SCHEME ) || 'https' !== parse_url( WP_SITEURL, PHP_URL_SCHEME ) ) {
        error_log( 'Critical configuration error: production requires https URLs for WP_HOME and WP_SITEURL' );
        if ( ! headers_sent() ) {
            header( 'HTTP/1.1 500 Internal Server Error' );
            header( 'Content-Type: text/plain; charset=utf-8' );
        }
        exit( 'Configuration error: production requires https site URLs.' );
    }
}


/**#@-*/

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */
// Optional request debug logging, disabled by default.
if ( defined('WP_DEBUG') && WP_DEBUG && filter_var($env('AIDDATA_RUNTIME_DIAGNOSTICS', 'false'), FILTER_VALIDATE_BOOLEAN) ) {
    error_log('REQUEST_URI: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unset'));
    error_log('HTTP_HOST: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unset'));
}

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false );
}
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
