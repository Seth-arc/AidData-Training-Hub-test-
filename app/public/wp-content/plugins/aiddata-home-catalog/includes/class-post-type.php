<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

final class AidData_Home_Catalog_Post_Type {
	public const POST_TYPE            = 'aiddata_home_card';
	public const TAXONOMY             = 'aiddata_home_category';
	public const TERM_ORDER_META_KEY  = '_aiddata_home_category_order';
	public const TERM_ORDER_FIELD_KEY = 'aiddata_home_category_order';

	/**
	 * Register the content model.
	 */
	public static function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'               => __( 'Home Catalog Cards', 'aiddata-home-catalog' ),
					'singular_name'      => __( 'Home Catalog Card', 'aiddata-home-catalog' ),
					'add_new_item'       => __( 'Add Home Catalog Card', 'aiddata-home-catalog' ),
					'edit_item'          => __( 'Edit Home Catalog Card', 'aiddata-home-catalog' ),
					'new_item'           => __( 'New Home Catalog Card', 'aiddata-home-catalog' ),
					'view_item'          => __( 'View Home Catalog Card', 'aiddata-home-catalog' ),
					'search_items'       => __( 'Search Home Catalog Cards', 'aiddata-home-catalog' ),
					'not_found'          => __( 'No home catalog cards found.', 'aiddata-home-catalog' ),
					'not_found_in_trash' => __( 'No home catalog cards found in Trash.', 'aiddata-home-catalog' ),
					'menu_name'          => __( 'Home Catalog', 'aiddata-home-catalog' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-screenoptions',
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
				'has_archive'         => false,
				'rewrite'             => false,
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Home Catalog Categories', 'aiddata-home-catalog' ),
					'singular_name' => __( 'Home Catalog Category', 'aiddata-home-catalog' ),
					'search_items'  => __( 'Search Categories', 'aiddata-home-catalog' ),
					'all_items'     => __( 'All Categories', 'aiddata-home-catalog' ),
					'edit_item'     => __( 'Edit Category', 'aiddata-home-catalog' ),
					'update_item'   => __( 'Update Category', 'aiddata-home-catalog' ),
					'add_new_item'  => __( 'Add New Category', 'aiddata-home-catalog' ),
					'new_item_name' => __( 'New Category Name', 'aiddata-home-catalog' ),
					'menu_name'     => __( 'Categories', 'aiddata-home-catalog' ),
				),
				'public'            => false,
				'publicly_queryable'=> false,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => false,
			)
		);
	}

	/**
	 * Render add-term fields.
	 */
	public static function render_add_term_fields(): void {
		?>
		<div class="form-field term-order-wrap">
			<label for="<?php echo esc_attr( self::TERM_ORDER_FIELD_KEY ); ?>"><?php esc_html_e( 'Display Order', 'aiddata-home-catalog' ); ?></label>
			<input
				type="number"
				min="0"
				step="1"
				name="<?php echo esc_attr( self::TERM_ORDER_FIELD_KEY ); ?>"
				id="<?php echo esc_attr( self::TERM_ORDER_FIELD_KEY ); ?>"
				value="100"
			>
			<p><?php esc_html_e( 'Lower numbers render first in the front-page filter bar.', 'aiddata-home-catalog' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render edit-term fields.
	 */
	public static function render_edit_term_fields( WP_Term $term ): void {
		$order = (int) get_term_meta( $term->term_id, self::TERM_ORDER_META_KEY, true );
		?>
		<tr class="form-field term-order-wrap">
			<th scope="row">
				<label for="<?php echo esc_attr( self::TERM_ORDER_FIELD_KEY ); ?>"><?php esc_html_e( 'Display Order', 'aiddata-home-catalog' ); ?></label>
			</th>
			<td>
				<input
					type="number"
					min="0"
					step="1"
					name="<?php echo esc_attr( self::TERM_ORDER_FIELD_KEY ); ?>"
					id="<?php echo esc_attr( self::TERM_ORDER_FIELD_KEY ); ?>"
					value="<?php echo esc_attr( (string) $order ); ?>"
				>
				<p class="description"><?php esc_html_e( 'Lower numbers render first in the front-page filter bar.', 'aiddata-home-catalog' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Persist term ordering.
	 */
	public static function save_term_meta( int $term_id ): void {
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}

		$order = isset( $_POST[ self::TERM_ORDER_FIELD_KEY ] )
			? (int) wp_unslash( $_POST[ self::TERM_ORDER_FIELD_KEY ] )
			: 100;

		update_term_meta( $term_id, self::TERM_ORDER_META_KEY, $order );
	}
}
