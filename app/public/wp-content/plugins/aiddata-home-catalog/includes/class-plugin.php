<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

final class AidData_Home_Catalog_Plugin {
	/**
	 * Prevent duplicate bootstrap work.
	 */
	private static bool $bootstrapped = false;

	/**
	 * Cache the front-page catalog markup so the output-buffer callback never
	 * needs to invoke rendering functions while PHP is flushing buffers.
	 */
	private static string $front_page_catalog_markup = '';

	/**
	 * Register hooks.
	 */
	public static function bootstrap( string $plugin_file ): void {
		if ( self::$bootstrapped ) {
			return;
		}

		self::$bootstrapped = true;

		add_action( 'init', array( AidData_Home_Catalog_Post_Type::class, 'register' ) );
		add_action( 'init', array( AidData_Home_Catalog_Renderer::class, 'register_shortcode' ) );
		add_action( 'init', array( AidData_Home_Catalog_Seeder::class, 'maybe_upgrade' ) );
		add_filter( 'template_include', array( __CLASS__, 'force_front_page_template' ), 50 );
		add_action( 'template_redirect', array( __CLASS__, 'begin_front_page_catalog_capture' ), 20 );
		add_action( 'add_meta_boxes', array( AidData_Home_Catalog_Meta_Boxes::class, 'register' ) );
		add_action(
			'save_post_' . AidData_Home_Catalog_Post_Type::POST_TYPE,
			array( AidData_Home_Catalog_Meta_Boxes::class, 'save' )
		);
		add_action(
			AidData_Home_Catalog_Post_Type::TAXONOMY . '_add_form_fields',
			array( AidData_Home_Catalog_Post_Type::class, 'render_add_term_fields' )
		);
		add_action(
			AidData_Home_Catalog_Post_Type::TAXONOMY . '_edit_form_fields',
			array( AidData_Home_Catalog_Post_Type::class, 'render_edit_term_fields' )
		);
		add_action(
			'created_' . AidData_Home_Catalog_Post_Type::TAXONOMY,
			array( AidData_Home_Catalog_Post_Type::class, 'save_term_meta' )
		);
		add_action(
			'edited_' . AidData_Home_Catalog_Post_Type::TAXONOMY,
			array( AidData_Home_Catalog_Post_Type::class, 'save_term_meta' )
		);
		add_filter(
			'manage_edit-' . AidData_Home_Catalog_Post_Type::POST_TYPE . '_columns',
			array( AidData_Home_Catalog_Meta_Boxes::class, 'register_columns' )
		);
		add_action(
			'manage_' . AidData_Home_Catalog_Post_Type::POST_TYPE . '_posts_custom_column',
			array( AidData_Home_Catalog_Meta_Boxes::class, 'render_column' ),
			10,
			2
		);
		add_action( 'pre_get_posts', array( AidData_Home_Catalog_Meta_Boxes::class, 'sort_admin_list' ) );

		register_activation_hook( $plugin_file, array( __CLASS__, 'activate' ) );
	}

	/**
	 * Seed the default catalog on first activation.
	 */
	public static function activate(): void {
		AidData_Home_Catalog_Post_Type::register();
		AidData_Home_Catalog_Renderer::register_shortcode();
		AidData_Home_Catalog_Seeder::seed_defaults();
		flush_rewrite_rules();
	}

	/**
	 * Route the live site homepage through the catalog-aware front-page template.
	 *
	 * Block themes can bypass front-page.php unless the homepage is explicitly
	 * assigned that template. The catalog is rendered inside front-page.php, so
	 * force that template for the actual site front page when it is available.
	 */
	public static function force_front_page_template( string $template ): string {
		if ( is_admin() || ! is_front_page() ) {
			return $template;
		}

		$normalized_template = wp_normalize_path( $template );
		if (
			str_ends_with( $normalized_template, '/front-page.php' )
			|| str_ends_with( $normalized_template, '/new-front-page/template-new-front-page.php' )
		) {
			return $template;
		}

		$candidates = array_unique(
			array(
				wp_normalize_path( trailingslashit( get_stylesheet_directory() ) . 'front-page.php' ),
				wp_normalize_path( trailingslashit( get_template_directory() ) . 'front-page.php' ),
			)
		);

		foreach ( $candidates as $candidate ) {
			if ( file_exists( $candidate ) ) {
				return $candidate;
			}
		}

		return $template;
	}

	/**
	 * Start a response buffer that can replace legacy homepage catalogs.
	 *
	 * Some homepage variants are stored in the database or rendered from block
	 * templates that never touch the plugin-aware PHP template files. Buffer the
	 * final front-page HTML so the catalog can still be replaced or injected.
	 */
	public static function begin_front_page_catalog_capture(): void {
		if ( is_admin() || ! is_front_page() || is_feed() ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) ) {
			return;
		}

		self::$front_page_catalog_markup = AidData_Home_Catalog_Renderer::render_front_page();

		ob_start( array( __CLASS__, 'inject_catalog_into_front_page_html' ) );
	}

	/**
	 * Replace the legacy homepage catalog markup with the plugin-rendered catalog.
	 */
	public static function inject_catalog_into_front_page_html( string $html ): string {
		if ( '' === trim( $html ) || false === stripos( $html, '<html' ) ) {
			return $html;
		}

		if ( false !== strpos( $html, 'data-aiddata-home-catalog="1"' ) ) {
			return $html;
		}

		$catalog_markup = self::$front_page_catalog_markup;
		if ( '' === trim( $catalog_markup ) ) {
			return $html;
		}

		$legacy_catalog_pattern = '/<section\b[^>]*class=(["\'])[^"\']*\bfeatured-content\b[^"\']*\1[^>]*>.*?<\/section>/is';
		$updated_html           = preg_replace( $legacy_catalog_pattern, $catalog_markup, $html, 1, $replacement_count );

		if ( is_string( $updated_html ) && $replacement_count > 0 ) {
			return $updated_html;
		}

		$injected_html = preg_replace( '/<\/main>/i', $catalog_markup . '</main>', $html, 1, $main_replacement_count );
		if ( is_string( $injected_html ) && $main_replacement_count > 0 ) {
			return $injected_html;
		}

		return $html . $catalog_markup;
	}
}
