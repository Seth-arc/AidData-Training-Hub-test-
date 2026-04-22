<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

final class AidData_Home_Catalog_Meta_Boxes {
	public const NONCE_ACTION = 'aiddata_home_catalog_save_meta';
	public const NONCE_NAME   = 'aiddata_home_catalog_meta_nonce';
	private const FRONT_PAGE_VISIBILITY_FIELD = 'aiddata_home_catalog_front_page_present';

	public const META_SHOW_ON_FRONT_PAGE = '_aiddata_home_catalog_show_on_front_page';
	public const META_IMAGE_PATH         = '_aiddata_home_catalog_image_path';
	public const META_IMAGE_ALT          = '_aiddata_home_catalog_image_alt';
	public const META_BADGES             = '_aiddata_home_catalog_badges';
	public const META_STAT_ONE           = '_aiddata_home_catalog_stat_one';
	public const META_STAT_TWO           = '_aiddata_home_catalog_stat_two';
	public const META_STAT_THREE         = '_aiddata_home_catalog_stat_three';
	public const META_CTA_LABEL          = '_aiddata_home_catalog_cta_label';
	public const META_CTA_URL            = '_aiddata_home_catalog_cta_url';
	public const META_TRAILER_LABEL      = '_aiddata_home_catalog_trailer_label';
	public const META_TRAILER_URL        = '_aiddata_home_catalog_trailer_url';
	public const META_INFO_LABEL         = '_aiddata_home_catalog_info_button_label';
	public const META_MODAL_TITLE        = '_aiddata_home_catalog_modal_title';
	public const META_MODAL_SUBTITLE     = '_aiddata_home_catalog_modal_subtitle';
	public const META_MODAL_BADGE_PATH   = '_aiddata_home_catalog_modal_badge_path';
	public const META_MODAL_BADGE_ALT    = '_aiddata_home_catalog_modal_badge_alt';

	/**
	 * Register the meta box.
	 */
	public static function register(): void {
		add_meta_box(
			'aiddata-home-catalog-card-settings',
			__( 'Front-Page Card Settings', 'aiddata-home-catalog' ),
			array( __CLASS__, 'render' ),
			AidData_Home_Catalog_Post_Type::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render the admin UI.
	 */
	public static function render( WP_Post $post ): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		$meta = self::get_meta( $post->ID );
		?>
		<p>
			<input type="hidden" name="<?php echo esc_attr( self::FRONT_PAGE_VISIBILITY_FIELD ); ?>" value="1">
			<label>
				<input type="checkbox" name="<?php echo esc_attr( self::META_SHOW_ON_FRONT_PAGE ); ?>" value="1" <?php checked( self::is_front_page_enabled( $meta[ self::META_SHOW_ON_FRONT_PAGE ] ) ); ?>>
				<?php esc_html_e( 'Show this card on the front page', 'aiddata-home-catalog' ); ?>
			</label>
			<br><span class="description"><?php esc_html_e( 'Published home catalog cards are visible by default. Uncheck this only when you want to hide a card from the front page.', 'aiddata-home-catalog' ); ?></span>
		</p>
		<p>
			<strong><?php esc_html_e( 'Editor mapping', 'aiddata-home-catalog' ); ?></strong><br>
			<?php esc_html_e( 'Title renders as the card title, Excerpt renders as the card summary, and the main editor renders the info modal body.', 'aiddata-home-catalog' ); ?>
		</p>
		<p>
			<label for="<?php echo esc_attr( self::META_IMAGE_PATH ); ?>"><strong><?php esc_html_e( 'Card image path or URL', 'aiddata-home-catalog' ); ?></strong></label><br>
			<input
				type="text"
				class="widefat"
				id="<?php echo esc_attr( self::META_IMAGE_PATH ); ?>"
				name="<?php echo esc_attr( self::META_IMAGE_PATH ); ?>"
				value="<?php echo esc_attr( $meta[ self::META_IMAGE_PATH ] ); ?>"
				placeholder="assets/images/example.png or https://..."
			>
			<span class="description"><?php esc_html_e( 'Use a theme-relative path like assets/images/example.png, a site-relative path like /courses/example/, or a full URL.', 'aiddata-home-catalog' ); ?></span>
		</p>
		<p>
			<label for="<?php echo esc_attr( self::META_IMAGE_ALT ); ?>"><strong><?php esc_html_e( 'Card image alt text', 'aiddata-home-catalog' ); ?></strong></label><br>
			<input
				type="text"
				class="widefat"
				id="<?php echo esc_attr( self::META_IMAGE_ALT ); ?>"
				name="<?php echo esc_attr( self::META_IMAGE_ALT ); ?>"
				value="<?php echo esc_attr( $meta[ self::META_IMAGE_ALT ] ); ?>"
			>
		</p>
		<p>
			<label for="<?php echo esc_attr( self::META_BADGES ); ?>"><strong><?php esc_html_e( 'Card badges', 'aiddata-home-catalog' ); ?></strong></label><br>
			<textarea
				class="widefat"
				rows="4"
				id="<?php echo esc_attr( self::META_BADGES ); ?>"
				name="<?php echo esc_attr( self::META_BADGES ); ?>"
				placeholder="One badge per line"
			><?php echo esc_textarea( $meta[ self::META_BADGES ] ); ?></textarea>
		</p>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
			<p>
				<label for="<?php echo esc_attr( self::META_STAT_ONE ); ?>"><strong><?php esc_html_e( 'Stat 1', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_STAT_ONE ); ?>" name="<?php echo esc_attr( self::META_STAT_ONE ); ?>" value="<?php echo esc_attr( $meta[ self::META_STAT_ONE ] ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_STAT_TWO ); ?>"><strong><?php esc_html_e( 'Stat 2', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_STAT_TWO ); ?>" name="<?php echo esc_attr( self::META_STAT_TWO ); ?>" value="<?php echo esc_attr( $meta[ self::META_STAT_TWO ] ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_STAT_THREE ); ?>"><strong><?php esc_html_e( 'Stat 3', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_STAT_THREE ); ?>" name="<?php echo esc_attr( self::META_STAT_THREE ); ?>" value="<?php echo esc_attr( $meta[ self::META_STAT_THREE ] ); ?>">
			</p>
		</div>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
			<p>
				<label for="<?php echo esc_attr( self::META_CTA_LABEL ); ?>"><strong><?php esc_html_e( 'CTA button label', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_CTA_LABEL ); ?>" name="<?php echo esc_attr( self::META_CTA_LABEL ); ?>" value="<?php echo esc_attr( $meta[ self::META_CTA_LABEL ] ); ?>" placeholder="Start Learning">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_CTA_URL ); ?>"><strong><?php esc_html_e( 'CTA destination', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_CTA_URL ); ?>" name="<?php echo esc_attr( self::META_CTA_URL ); ?>" value="<?php echo esc_attr( $meta[ self::META_CTA_URL ] ); ?>" placeholder="/courses/example/">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_TRAILER_LABEL ); ?>"><strong><?php esc_html_e( 'Trailer button label', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_TRAILER_LABEL ); ?>" name="<?php echo esc_attr( self::META_TRAILER_LABEL ); ?>" value="<?php echo esc_attr( $meta[ self::META_TRAILER_LABEL ] ); ?>" placeholder="Watch Trailer">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_TRAILER_URL ); ?>"><strong><?php esc_html_e( 'Trailer URL', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_TRAILER_URL ); ?>" name="<?php echo esc_attr( self::META_TRAILER_URL ); ?>" value="<?php echo esc_attr( $meta[ self::META_TRAILER_URL ] ); ?>" placeholder="https://... or assets/videos/example.mp4">
			</p>
		</div>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
			<p>
				<label for="<?php echo esc_attr( self::META_INFO_LABEL ); ?>"><strong><?php esc_html_e( 'Info button label', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_INFO_LABEL ); ?>" name="<?php echo esc_attr( self::META_INFO_LABEL ); ?>" value="<?php echo esc_attr( $meta[ self::META_INFO_LABEL ] ); ?>" placeholder="Info">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_MODAL_TITLE ); ?>"><strong><?php esc_html_e( 'Modal title override', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_MODAL_TITLE ); ?>" name="<?php echo esc_attr( self::META_MODAL_TITLE ); ?>" value="<?php echo esc_attr( $meta[ self::META_MODAL_TITLE ] ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_MODAL_SUBTITLE ); ?>"><strong><?php esc_html_e( 'Modal subtitle', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_MODAL_SUBTITLE ); ?>" name="<?php echo esc_attr( self::META_MODAL_SUBTITLE ); ?>" value="<?php echo esc_attr( $meta[ self::META_MODAL_SUBTITLE ] ); ?>">
			</p>
		</div>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
			<p>
				<label for="<?php echo esc_attr( self::META_MODAL_BADGE_PATH ); ?>"><strong><?php esc_html_e( 'Modal badge path or URL', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_MODAL_BADGE_PATH ); ?>" name="<?php echo esc_attr( self::META_MODAL_BADGE_PATH ); ?>" value="<?php echo esc_attr( $meta[ self::META_MODAL_BADGE_PATH ] ); ?>" placeholder="assets/images/certificate.png">
			</p>
			<p>
				<label for="<?php echo esc_attr( self::META_MODAL_BADGE_ALT ); ?>"><strong><?php esc_html_e( 'Modal badge alt text', 'aiddata-home-catalog' ); ?></strong></label><br>
				<input type="text" class="widefat" id="<?php echo esc_attr( self::META_MODAL_BADGE_ALT ); ?>" name="<?php echo esc_attr( self::META_MODAL_BADGE_ALT ); ?>" value="<?php echo esc_attr( $meta[ self::META_MODAL_BADGE_ALT ] ); ?>">
			</p>
		</div>
		<?php
	}

	/**
	 * Persist all supported metadata.
	 */
	public static function save( int $post_id ): void {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) ) {
			return;
		}

		$nonce = wp_unslash( $_POST[ self::NONCE_NAME ] );
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$checkbox_fields = array(
			self::META_SHOW_ON_FRONT_PAGE,
		);

		foreach ( $checkbox_fields as $field ) {
			if ( self::META_SHOW_ON_FRONT_PAGE === $field ) {
				if ( ! isset( $_POST[ self::FRONT_PAGE_VISIBILITY_FIELD ] ) ) {
					continue;
				}

				update_post_meta( $post_id, $field, isset( $_POST[ $field ] ) ? '1' : '0' );
				continue;
			}

			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, $field, '1' );
			} else {
				delete_post_meta( $post_id, $field );
			}
		}

		$text_fields = array(
			self::META_IMAGE_PATH,
			self::META_IMAGE_ALT,
			self::META_BADGES,
			self::META_STAT_ONE,
			self::META_STAT_TWO,
			self::META_STAT_THREE,
			self::META_CTA_LABEL,
			self::META_CTA_URL,
			self::META_TRAILER_LABEL,
			self::META_TRAILER_URL,
			self::META_INFO_LABEL,
			self::META_MODAL_TITLE,
			self::META_MODAL_SUBTITLE,
			self::META_MODAL_BADGE_PATH,
			self::META_MODAL_BADGE_ALT,
		);

		foreach ( $text_fields as $field ) {
			$value = isset( $_POST[ $field ] ) ? wp_unslash( $_POST[ $field ] ) : '';
			$value = self::sanitize_field_value( $field, $value );

			if ( '' === $value ) {
				delete_post_meta( $post_id, $field );
				continue;
			}

			update_post_meta( $post_id, $field, $value );
		}
	}

	/**
	 * Customize the list table columns.
	 *
	 * @param array<string, string> $columns Existing columns.
	 * @return array<string, string>
	 */
	public static function register_columns( array $columns ): array {
		$updated = array();

		foreach ( $columns as $key => $label ) {
			$updated[ $key ] = $label;

			if ( 'title' === $key ) {
				$updated['home_catalog_front_page'] = __( 'Front Page', 'aiddata-home-catalog' );
				$updated['home_catalog_order']      = __( 'Order', 'aiddata-home-catalog' );
			}
		}

		return $updated;
	}

	/**
	 * Render list table cells.
	 */
	public static function render_column( string $column, int $post_id ): void {
		if ( 'home_catalog_front_page' === $column ) {
			$value = (string) get_post_meta( $post_id, self::META_SHOW_ON_FRONT_PAGE, true );
			echo self::is_front_page_enabled( $value ) ? esc_html__( 'Yes', 'aiddata-home-catalog' ) : esc_html__( 'No', 'aiddata-home-catalog' );
			return;
		}

		if ( 'home_catalog_order' === $column ) {
			echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
		}
	}

	/**
	 * Keep the admin list ordered like the front page.
	 */
	public static function sort_admin_list( WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( AidData_Home_Catalog_Post_Type::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		if ( $query->get( 'orderby' ) ) {
			return;
		}

		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			)
		);
	}

	/**
	 * Fetch the meta payload for the editor.
	 *
	 * @return array<string, string>
	 */
	private static function get_meta( int $post_id ): array {
		$keys = array(
			self::META_SHOW_ON_FRONT_PAGE,
			self::META_IMAGE_PATH,
			self::META_IMAGE_ALT,
			self::META_BADGES,
			self::META_STAT_ONE,
			self::META_STAT_TWO,
			self::META_STAT_THREE,
			self::META_CTA_LABEL,
			self::META_CTA_URL,
			self::META_TRAILER_LABEL,
			self::META_TRAILER_URL,
			self::META_INFO_LABEL,
			self::META_MODAL_TITLE,
			self::META_MODAL_SUBTITLE,
			self::META_MODAL_BADGE_PATH,
			self::META_MODAL_BADGE_ALT,
		);

		$meta = array();
		foreach ( $keys as $key ) {
			$meta[ $key ] = (string) get_post_meta( $post_id, $key, true );
		}

		return $meta;
	}

	/**
	 * Apply field-specific sanitization.
	 */
	private static function sanitize_field_value( string $field, string $value ): string {
		$value = trim( $value );

		if ( self::META_BADGES === $field ) {
			$lines = preg_split( '/\r\n|\r|\n/', $value ) ?: array();
			$lines = array_filter(
				array_map(
					static function ( string $line ): string {
						return sanitize_text_field( $line );
					},
					$lines
				)
			);

			return implode( "\n", $lines );
		}

		if ( in_array( $field, array( self::META_MODAL_SUBTITLE ), true ) ) {
			return sanitize_text_field( $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Treat missing front-page visibility meta as enabled by default.
	 */
	private static function is_front_page_enabled( string $value ): bool {
		return '0' !== $value;
	}
}
