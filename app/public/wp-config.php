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
define( 'DB_NAME', 'railway' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'mopzmAdFBAdfFWjwhNcznxdyZzNuoFNx' );

/** Database hostname */
define( 'DB_HOST', 'mysql.railway.internal' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

// Increase PHP memory limit for AidData LMS
ini_set('memory_limit', '512M');

// Fix cURL timeout issues
ini_set('default_socket_timeout', 300);
ini_set('max_execution_time', 300);

// HTTP API timeout settings
define('WP_HTTP_BLOCK_EXTERNAL', false);
define('WP_ACCESSIBLE_HOSTS', '*.wordpress.org,*.github.com,*.aiddata.org,*.wm.edu');


define( 'WP_HOME', $env('WP_HOME', 'https://aiddata-training-hub-test-production.up.railway.app') );
define( 'WP_SITEURL', $env('WP_SITEURL', 'https://aiddata-training-hub-test-production.up.railway.app') );

define( 'WP_ENVIRONMENT_TYPE', $env( 'WP_ENVIRONMENT_TYPE', 'local' ) );

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

// Disable problematic WordPress API calls in local environment
if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local') {
    // Disable WordPress.org update checks
    // add_filter('pre_site_transient_update_core', '__return_null');
    // add_filter('pre_site_transient_update_plugins', '__return_null');
    // add_filter('pre_site_transient_update_themes', '__return_null');

    // Disable automatic updates
    define('AUTOMATIC_UPDATER_DISABLED', true);
    define('WP_AUTO_UPDATE_CORE', false);

    // Disable cron for external requests is handled via env config above.
}

// Enable WordPress debugging for memory issues
$debug_enabled = filter_var( $env( 'WP_DEBUG', 'true' ), FILTER_VALIDATE_BOOLEAN );
define( 'WP_DEBUG', $debug_enabled );
define( 'WP_DEBUG_LOG', filter_var( $env( 'WP_DEBUG_LOG', 'true' ), FILTER_VALIDATE_BOOLEAN ) );
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
define( 'AUTH_KEY',          '-|YPrWJ:${SSzHjUk3Ma+cUXLDG<cX_;r<^EP-46VxCK!).]ABOGGCdfiaHGf1{P' );
define( 'SECURE_AUTH_KEY',   'S~|EHhRg03YX=lO;i|ZD?<SiSH5k>@:WiN}|t|FB*T%${/QNySzl5HGiWZ(}c~op' );
define( 'LOGGED_IN_KEY',     'lD]v;Ps]Dw#reuPMt#7-}5w4yz[!eG0}5gQux$[%gg`+<zwS<L(|Kk4)^-qe=L86' );
define( 'NONCE_KEY',         'nc4&1n}h.)ROMEm&X peIb^}/@lf:A|@J**i;:B-v[y9 ?z!=tv-!4IF{`v7zT5W' );
define( 'AUTH_SALT',         'q_T,DQy!.XHe-6l!/S>S0*.I-=n^][lZ!A- cr`Y-`5.Z{kbmE[ctg?zJ)%,HC44' );
define( 'SECURE_AUTH_SALT',  '6o#$*oC_1Mpo$x9o.J|N)EM2!_k7EwY3BLH]P5^l=+2.CI6|Zs|yWGLq&:OO60IV' );
define( 'LOGGED_IN_SALT',    'jy|_Re+s/CK)F0PzP&L>)&Gii6E=J{5ykW%t<(?d|^fssrxK+]VT@lH-(m?qWh[|' );
define( 'NONCE_SALT',        'b^^1*|c2H|2#}@|fM+@mGVJGfZPh>1V|!L6]:]cnWiTQu6>N-WCMOheyJv%5LS$(' );
define( 'WP_CACHE_KEY_SALT', '/X5|LbgC`$z~cn)x8Z0/PokYQ_ Y-3<I&EaY@<t}E_C/X#kq=DI:7>_32<!`M{ed' );


/**#@-*/

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */
// Debug output for Railway path issues
if ( defined('WP_DEBUG') && WP_DEBUG ) {
    error_log('REQUEST_URI: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unset'));
    error_log('HTTP_HOST: ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unset'));
}

// Handle HTTPS and HTTP_HOST behind reverse proxy (Railway, Docker, etc.)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
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
