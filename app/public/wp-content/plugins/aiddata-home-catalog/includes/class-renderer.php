<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

final class AidData_Home_Catalog_Renderer {
	public const SHORTCODE = 'aiddata_home_catalog';

	/**
	 * Register the shortcode.
	 */
	public static function register_shortcode(): void {
		add_shortcode( self::SHORTCODE, array( __CLASS__, 'handle_shortcode' ) );
	}

	/**
	 * Shortcode entry point.
	 *
	 * @param array<string, mixed> $attributes Shortcode attributes.
	 */
	public static function handle_shortcode( array $attributes = array() ): string {
		return self::render_front_page( $attributes );
	}

	/**
	 * Render the home catalog section and its info modals.
	 *
	 * @param array<string, mixed> $attributes Optional render attributes.
	 */
	public static function render_front_page( array $attributes = array() ): string {
		unset( $attributes );

		$cards = get_posts(
			array(
				'post_type'      => AidData_Home_Catalog_Post_Type::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE,
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'   => AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE,
						'value' => '1',
					),
				),
			)
		);

		$terms = self::get_render_terms();

		ob_start();
		?>
		<section class="featured-content aiddata-home-catalog-root" data-aiddata-home-catalog="1">
			<div class="filter-section">
				<div class="filter-container">
					<div class="filter-buttons">
						<button type="button" class="filter-btn active" data-filter="all">
							<?php esc_html_e( 'All', 'aiddata-home-catalog' ); ?>
						</button>
						<?php foreach ( $terms as $term ) : ?>
							<button type="button" class="filter-btn" data-filter="<?php echo esc_attr( $term->slug ); ?>">
								<?php echo esc_html( $term->name ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="featured-grid">
				<div class="empty-state<?php echo empty( $cards ) ? ' visible' : ''; ?>">
					<div class="empty-state-animation">
						<div class="thinking-animation">
							<div class="bubble"></div>
							<div class="bubble"></div>
							<div class="bubble"></div>
						</div>
						<svg class="hero-character" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="8" r="7"/>
							<path d="M12 15v6"/>
							<path d="M8 21h8"/>
							<circle cx="9" cy="7" r="1" fill="currentColor"/>
							<circle cx="15" cy="7" r="1" fill="currentColor"/>
							<path d="M8 12s2 1 4 1 4-1 4-1"/>
						</svg>
					</div>
					<h3 class="empty-message"><?php esc_html_e( 'Hmm... This section is as empty as a developer\'s coffee cup at 9 AM!', 'aiddata-home-catalog' ); ?></h3>
					<p class="empty-description"><?php esc_html_e( 'Don\'t worry, we\'re brewing up some amazing content for this category.', 'aiddata-home-catalog' ); ?></p>
				</div>

				<?php foreach ( $cards as $card ) : ?>
					<?php echo self::render_card( $card ); ?>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $cards as $card ) : ?>
				<?php echo self::render_modal( $card ); ?>
			<?php endforeach; ?>
		</section>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Render a single front-page card.
	 */
	private static function render_card( WP_Post $card ): string {
		$card_id        = (int) $card->ID;
		$terms          = self::get_sorted_card_terms( $card_id );
		$primary_term   = $terms[0] ?? null;
		$category_slugs = array_values(
			array_filter(
				array_map(
					static function ( WP_Term $term ): string {
						return $term->slug;
					},
					$terms
				)
			)
		);
		$badges         = self::get_badges( $card_id, $terms );
		$stats          = array_values(
			array_filter(
				array(
					(string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_STAT_ONE, true ),
					(string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_STAT_TWO, true ),
					(string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_STAT_THREE, true ),
				)
			)
		);
		$summary        = trim( (string) $card->post_excerpt );
		$cta_label      = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_CTA_LABEL, true ) );
		$cta_url        = self::resolve_resource_url(
			(string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_CTA_URL, true )
		);
		$trailer_label  = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_TRAILER_LABEL, true ) );
		$trailer_url    = self::resolve_resource_url(
			(string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_TRAILER_URL, true )
		);
		$has_modal      = self::has_modal_content( $card );
		$info_label     = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_INFO_LABEL, true ) );
		$info_button_id = self::get_modal_id( $card_id );

		if ( '' === $cta_label ) {
			$cta_label = __( 'Start Learning', 'aiddata-home-catalog' );
		}

		if ( '' === $trailer_label ) {
			$trailer_label = __( 'Watch Trailer', 'aiddata-home-catalog' );
		}

		if ( '' === $info_label ) {
			$info_label = __( 'Info', 'aiddata-home-catalog' );
		}

		$button_count = 0;
		if ( '' !== $cta_url ) {
			++$button_count;
		}
		if ( '' !== $trailer_url ) {
			++$button_count;
		}
		if ( $has_modal ) {
			++$button_count;
		}

		ob_start();
		?>
		<div
			class="featured-course"
			data-type="<?php echo esc_attr( $primary_term ? $primary_term->slug : 'all' ); ?>"
			data-categories="<?php echo esc_attr( implode( ' ', $category_slugs ) ); ?>"
		>
			<div class="course-preview">
				<?php echo self::render_card_image( $card ); ?>
				<div class="preview-overlay">
					<div class="preview-content">
						<h3><?php echo esc_html( get_the_title( $card ) ); ?></h3>
						<?php if ( ! empty( $badges ) ) : ?>
							<div class="course-categories">
								<?php foreach ( $badges as $index => $badge ) : ?>
									<?php
									$classes = array(
										'category-tag',
										'category-tag--' . sanitize_html_class( sanitize_title( $badge ) ),
									);

									if ( 0 === $index && $primary_term ) {
										$classes[] = sanitize_html_class( $primary_term->slug );
									}
									?>
									<span class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
										<?php echo esc_html( $badge ); ?>
									</span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php if ( '' !== $summary ) : ?>
							<p><?php echo esc_html( $summary ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $stats ) ) : ?>
							<div class="course-stats">
								<?php foreach ( $stats as $stat ) : ?>
									<span class="stat">
										<?php echo self::render_stat_icon_markup(); ?>
										<?php echo esc_html( $stat ); ?>
									</span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="course-actions">
				<?php if ( '' !== $cta_url ) : ?>
					<button type="button" class="primary-button start-learning" data-course="<?php echo esc_url( $cta_url ); ?>">
						<?php echo esc_html( $cta_label ); ?>
					</button>
				<?php endif; ?>
				<?php if ( '' !== $trailer_url ) : ?>
					<button type="button" class="trailer-button" data-video="<?php echo esc_url( $trailer_url ); ?>">
						<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
							<circle cx="12" cy="12" r="10"/>
							<polygon points="10 8 16 12 10 16" fill="currentColor"/>
						</svg>
						<?php echo esc_html( $trailer_label ); ?>
					</button>
				<?php endif; ?>
				<?php if ( $has_modal ) : ?>
					<?php $info_classes = 'secondary-button'; ?>
					<?php if ( 1 === $button_count ) : ?>
						<?php $info_classes .= ' full-width-button'; ?>
					<?php endif; ?>
					<button type="button" class="<?php echo esc_attr( $info_classes ); ?>" data-modal="<?php echo esc_attr( $info_button_id ); ?>">
						<?php echo esc_html( $info_label ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Render an info modal when content exists.
	 */
	private static function render_modal( WP_Post $card ): string {
		if ( ! self::has_modal_content( $card ) ) {
			return '';
		}

		$card_id       = (int) $card->ID;
		$modal_id      = self::get_modal_id( $card_id );
		$title_id      = $modal_id . '-title';
		$title         = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_MODAL_TITLE, true ) );
		$subtitle      = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_MODAL_SUBTITLE, true ) );
		$badge_path    = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_PATH, true ) );
		$badge_alt     = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_ALT, true ) );
		$content       = self::prepare_modal_content( $card );

		if ( '' === $title ) {
			$title = get_the_title( $card );
		}

		ob_start();
		?>
		<div class="info-modal" id="<?php echo esc_attr( $modal_id ); ?>" role="dialog" aria-hidden="true" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
			<div class="info-container">
				<button type="button" class="close-info" aria-label="<?php esc_attr_e( 'Close', 'aiddata-home-catalog' ); ?>">&times;</button>
				<div class="info-content">
					<div class="title-section">
						<?php echo self::render_modal_badge( $badge_path, $badge_alt ); ?>
						<h3 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $title ); ?></h3>
					</div>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="home-catalog-modal-subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
					<?php echo $content; ?>
				</div>
			</div>
		</div>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Render the card artwork.
	 */
	private static function render_card_image( WP_Post $card ): string {
		if ( has_post_thumbnail( $card ) ) {
			return (string) get_the_post_thumbnail(
				$card,
				'large',
				array(
					'class'    => 'preview-image',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		}

		$card_id    = (int) $card->ID;
		$image_path = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH, true ) );
		$image_alt  = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT, true ) );

		return self::render_image_markup(
			$image_path,
			$image_alt,
			array(
				'class'    => 'preview-image',
				'loading'  => 'lazy',
				'decoding' => 'async',
			),
			array(
				'picture_class' => 'preview-image-picture',
			)
		);
	}

	/**
	 * Render an image with theme-aware fallbacks.
	 *
	 * @param array<string, string> $attributes HTML attributes.
	 * @param array<string, string> $options    Extra render options.
	 */
	private static function render_image_markup( string $path, string $alt, array $attributes = array(), array $options = array() ): string {
		$path = trim( $path );
		$alt  = '' !== $alt ? $alt : __( 'Catalog image', 'aiddata-home-catalog' );

		if ( '' === $path ) {
			return '';
		}

		if ( self::is_theme_asset_path( $path ) && function_exists( 'aiddata_get_responsive_theme_image_markup' ) ) {
			$args = $attributes;
			if ( ! isset( $args['loading'] ) ) {
				$args['loading'] = 'lazy';
			}
			if ( ! isset( $args['decoding'] ) ) {
				$args['decoding'] = 'async';
			}

			return (string) aiddata_get_responsive_theme_image_markup( ltrim( $path, '/' ), $alt, $args, $options );
		}

		$url = self::resolve_resource_url( $path );
		if ( '' === $url ) {
			return '';
		}

		$attribute_markup = '';
		foreach ( $attributes as $name => $value ) {
			if ( '' === $value ) {
				continue;
			}

			$attribute_markup .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}

		return sprintf( '<img src="%1$s" alt="%2$s"%3$s>', esc_url( $url ), esc_attr( $alt ), $attribute_markup );
	}

	/**
	 * Render the modal title badge.
	 */
	private static function render_modal_badge( string $path, string $alt ): string {
		$markup = self::render_image_markup(
			$path,
			'' !== $alt ? $alt : __( 'Catalog badge', 'aiddata-home-catalog' ),
			array(
				'class'    => 'course-badge',
				'loading'  => 'lazy',
				'decoding' => 'async',
				'width'    => '35',
				'height'   => '35',
			)
		);

		if ( '' !== $markup ) {
			return $markup;
		}

		return '<svg viewBox="0 0 24 24" width="35" height="35" fill="none" stroke="#115740" stroke-width="2" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>';
	}

	/**
	 * Build the term list used by the filter bar.
	 *
	 * @return WP_Term[]
	 */
	private static function get_render_terms(): array {
		$terms = get_terms(
			array(
				'taxonomy'   => AidData_Home_Catalog_Post_Type::TAXONOMY,
				'hide_empty' => false,
				'orderby'    => 'name',
			)
		);

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		if ( empty( $terms ) ) {
			return array();
		}

		return self::sort_terms( $terms );
	}

	/**
	 * Load and sort terms assigned to a single card.
	 *
	 * @return WP_Term[]
	 */
	private static function get_sorted_card_terms( int $card_id ): array {
		$terms = get_the_terms( $card_id, AidData_Home_Catalog_Post_Type::TAXONOMY );
		if ( false === $terms || is_wp_error( $terms ) ) {
			return array();
		}

		return self::sort_terms( $terms );
	}

	/**
	 * Sort filter terms by configured order and then alphabetically.
	 *
	 * @param WP_Term[] $terms Unsigned terms.
	 * @return WP_Term[]
	 */
	private static function sort_terms( array $terms ): array {
		usort(
			$terms,
			static function ( WP_Term $left, WP_Term $right ): int {
				$left_order  = (int) get_term_meta( $left->term_id, AidData_Home_Catalog_Post_Type::TERM_ORDER_META_KEY, true );
				$right_order = (int) get_term_meta( $right->term_id, AidData_Home_Catalog_Post_Type::TERM_ORDER_META_KEY, true );

				if ( $left_order === $right_order ) {
					return strcasecmp( $left->name, $right->name );
				}

				return $left_order <=> $right_order;
			}
		);

		return $terms;
	}

	/**
	 * Resolve badge labels from explicit meta, then fallback to assigned terms.
	 *
	 * @param WP_Term[] $terms Assigned filter terms.
	 * @return string[]
	 */
	private static function get_badges( int $card_id, array $terms ): array {
		$raw = trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_BADGES, true ) );
		if ( '' !== $raw ) {
			$lines = preg_split( '/\r\n|\r|\n/', $raw ) ?: array();
			$lines = array_values(
				array_filter(
					array_map(
						static function ( string $line ): string {
							return trim( $line );
						},
						$lines
					)
				)
			);

			if ( ! empty( $lines ) ) {
				return $lines;
			}
		}

		return array_values(
			array_filter(
				array_map(
					static function ( WP_Term $term ): string {
						return $term->name;
					},
					$terms
				)
			)
		);
	}

	/**
	 * Determine whether a modal button should be shown.
	 */
	private static function has_modal_content( WP_Post $card ): bool {
		$card_id = (int) $card->ID;

		return '' !== trim( (string) $card->post_content )
			|| '' !== trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_MODAL_SUBTITLE, true ) )
			|| '' !== trim( (string) get_post_meta( $card_id, AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_PATH, true ) );
	}

	/**
	 * Build the rendered modal HTML body.
	 */
	private static function prepare_modal_content( WP_Post $card ): string {
		$content = (string) $card->post_content;
		$content = str_replace(
			array(
				'{{theme_url}}',
				'{{home_url}}',
			),
			array(
				trailingslashit( get_stylesheet_directory_uri() ),
				trailingslashit( home_url() ),
			),
			$content
		);

		$content = apply_filters( 'the_content', $content );

		return (string) $content;
	}

	/**
	 * Resolve a theme-relative, site-relative, or absolute URL.
	 */
	private static function resolve_resource_url( string $value ): string {
		$value = trim( $value );

		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '#^https?://#i', $value ) ) {
			return $value;
		}

		if ( AidData_Home_Catalog_Compat::starts_with( $value, '//' ) ) {
			return ( is_ssl() ? 'https:' : 'http:' ) . $value;
		}

		if ( self::is_theme_asset_path( $value ) ) {
			return trailingslashit( get_stylesheet_directory_uri() ) . ltrim( $value, '/' );
		}

		if ( AidData_Home_Catalog_Compat::starts_with( $value, '/' ) ) {
			return home_url( $value );
		}

		return home_url( '/' . ltrim( $value, '/' ) );
	}

	/**
	 * Check whether a path points into the active theme.
	 */
	private static function is_theme_asset_path( string $value ): bool {
		$value = ltrim( $value, '/' );

		return AidData_Home_Catalog_Compat::starts_with( $value, 'assets/' )
			|| AidData_Home_Catalog_Compat::starts_with( $value, 'new-front-page/' );
	}

	/**
	 * Build a stable modal DOM id.
	 */
	private static function get_modal_id( int $card_id ): string {
		return 'aiddataHomeCatalogInfo-' . $card_id;
	}

	/**
	 * Render a generic stat icon.
	 */
	private static function render_stat_icon_markup(): string {
		return '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"></circle><polyline points="12 6 12 12 16 14" fill="none" stroke="currentColor" stroke-width="2"></polyline></svg>';
	}
}
