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
define( 'DB_NAME', $env( 'DB_NAME', 'local' ) );

/** Database username */
define( 'DB_USER', $env( 'DB_USER', 'root' ) );

/** Database password */
define( 'DB_PASSWORD', $env( 'DB_PASSWORD', 'root' ) );

/** Database hostname */
define( 'DB_HOST', $env( 'DB_HOST', 'localhost' ) );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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

$wp_home = $env( 'WP_HOME', 'http://localhost:10016' );
define( 'WP_HOME', $wp_home );
define( 'WP_SITEURL', $env( 'WP_SITEURL', $wp_home ) );

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
define( 'AUTH_KEY',          '~A2]C)*&&H[Ur2n(XV&Jj7~h`8H|/6]M(D{iC.QDjJvrY1xq-RDgL=-6F/NtNw7O' );
define( 'SECURE_AUTH_KEY',   '9:=ocj141JVZ`llvt#Vh4*#wtku9HCOXH_#g&l6,UI+(]H7<Z`j5;GLe ~.<CN<r' );
define( 'LOGGED_IN_KEY',     '4/ldzh_kqxAY-o10y197y@JG/e?J$>]hCK)~7-7r}u*!rx$4gf7VCY`%2h^Ba{y4' );
define( 'NONCE_KEY',         'QNf`J{*xTOR!KdmKLniOfio?imUA<Q+yUEKFsjBBi+@BmHH,Bxy{U9&<D.7giT8<' );
define( 'AUTH_SALT',         '(:wrOwIRgw}SJ0&DbDd?p<~RERbIA?]b?7Y(nFiaUmC}%V[@7&dTED=nS`LN=[}N' );
define( 'SECURE_AUTH_SALT',  'Ds;L/>]PV.wnvbL$X2c.%Y^t|Ooq_yfQBbb;PG@F/i+P&JQS=]#fRgJ?=7<}{qr}' );
define( 'LOGGED_IN_SALT',    'YGBE>g@DLY;e523c}RB_,l:VtaB@u Cfv1P7SvrPztKchvYaM-Q~(qZQ8Fj q]*2' );
define( 'NONCE_SALT',        '>zL?Xz!FK<zi-WHc#27D&*/L4scepR,QyyM8 Q/gveX0ct>+v!wn0pdyH&`e)j6V' );
define( 'WP_CACHE_KEY_SALT', '/X5|LbgC`$z~cn)x8Z0/PokYQ_ Y-3<I&EaY@<t}E_C/X#kq=DI:7>_32<!`M{ed' );


/**#@-*/

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
