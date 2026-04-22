<?php
/**
 * Plugin Name: AidData Home Catalog
 * Description: Admin-managed front-page categories, cards, info modals, trailers, and start-learning links for the AidData Training Hub.
 * Version: 1.2.0
 * Author: OpenAI Codex
 */

declare(strict_types=1);

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

require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-plugin.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-post-type.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-meta-boxes.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-renderer.php';
require_once AIDDATA_HOME_CATALOG_PATH . '/includes/class-seeder.php';

AidData_Home_Catalog_Plugin::bootstrap( __FILE__ );

if ( ! function_exists( 'aiddata_home_catalog_render_front_page' ) ) {
	/**
	 * Render the front-page home catalog section.
	 *
	 * @param array<string, mixed> $attributes Optional render attributes.
	 */
	function aiddata_home_catalog_render_front_page( array $attributes = array() ): string {
		return AidData_Home_Catalog_Renderer::render_front_page( $attributes );
	}
}
