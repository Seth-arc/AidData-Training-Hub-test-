<?php
/**
 * Plugin Name: AidData Home Catalog
 * Description: Admin-managed front-page categories, cards, info modals, trailers, and start-learning links for the AidData Training Hub.
 * Version: 1.2.0
 * Author: OpenAI Codex
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'AIDDATA_HOME_CATALOG_VERSION' ) ) {
	define( 'AIDDATA_HOME_CATALOG_VERSION', '1.2.0' );
}

if ( ! defined( 'AIDDATA_HOME_CATALOG_FILE' ) ) {
	define( 'AIDDATA_HOME_CATALOG_FILE', __FILE__ );
}

if ( ! defined( 'AIDDATA_HOME_CATALOG_PATH' ) ) {
	define( 'AIDDATA_HOME_CATALOG_PATH', __DIR__ );
}

if ( ! defined( 'AIDDATA_HOME_CATALOG_MIN_PHP' ) ) {
	define( 'AIDDATA_HOME_CATALOG_MIN_PHP', '7.1.0' );
}

/**
 * Keep a no-op renderer available even when the runtime is disabled.
 *
 * The theme checks for this function before requiring the plugin bootstrap.
 * Defining it here prevents secondary includes and avoids hard fatals if the
 * catalog runtime is temporarily disabled for site recovery.
 *
 * @param array $attributes Optional render attributes.
 * @return string
 */
if ( ! function_exists( 'aiddata_home_catalog_render_front_page' ) ) {
	function aiddata_home_catalog_render_front_page( $attributes = array() ) {
		unset( $attributes );

		return '';
	}
}

if ( ! defined( 'AIDDATA_HOME_CATALOG_ENABLE_RUNTIME' ) ) {
	define( 'AIDDATA_HOME_CATALOG_ENABLE_RUNTIME', false );
}

/**
 * Gate the plugin before any runtime files are loaded.
 *
 * The plugin was introduced in a single large commit. If the site is on an
 * older PHP runtime, requiring the plugin classes can fatal before WordPress
 * can render either wp-admin or the public site. Keep the runtime disabled by
 * default until the catalog code is verified in the target environment.
 *
 * @return bool
 */
function aiddata_home_catalog_runtime_supported() {
	if ( ! AIDDATA_HOME_CATALOG_ENABLE_RUNTIME ) {
		return false;
	}

	return version_compare( PHP_VERSION, AIDDATA_HOME_CATALOG_MIN_PHP, '>=' );
}

/**
 * Explain why the catalog plugin runtime was skipped.
 */
function aiddata_home_catalog_admin_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p>
			<?php
			if ( ! AIDDATA_HOME_CATALOG_ENABLE_RUNTIME ) {
				echo esc_html( 'AidData Home Catalog runtime is temporarily disabled in code to keep the site bootable while the new catalog implementation is stabilized.' );
			} else {
				echo esc_html(
					sprintf(
						'AidData Home Catalog was not loaded because the current PHP runtime (%s) is below the required version (%s).',
						PHP_VERSION,
						AIDDATA_HOME_CATALOG_MIN_PHP
					)
				);
			}
			?>
		</p>
	</div>
	<?php
}

if ( ! aiddata_home_catalog_runtime_supported() ) {
	if ( function_exists( 'add_action' ) ) {
		add_action( 'admin_notices', 'aiddata_home_catalog_admin_notice' );
	}

	return;
}

require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-compat.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-plugin.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-post-type.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-meta-boxes.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-renderer.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-seeder.php';

AidData_Home_Catalog_Plugin::bootstrap( __FILE__ );
