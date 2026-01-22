<?php
/**
 * Frontend Asset Loader
 *
 * Handles enqueueing of frontend scripts and styles
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 * @since      2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend Asset Loader Class
 *
 * Enqueues scripts and styles for the public-facing side of the site
 *
 * @since 2.0.0
 */
class AidData_LMS_Frontend_Assets {

	/**
	 * Constructor
	 *
	 * Register hooks for enqueueing scripts and styles
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );
	}

	/**
	 * Enqueue frontend styles
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function enqueue_styles(): void {
		// Enqueue tutorial display styles on tutorial pages
		if ( is_singular( 'aiddata_tutorial' ) || is_post_type_archive( 'aiddata_tutorial' ) || is_tax( array( 'aiddata_tutorial_cat', 'aiddata_tutorial_tag', 'aiddata_tutorial_difficulty' ) ) ) {
			wp_enqueue_style(
				'aiddata-lms-tutorial-display',
				AIDDATA_LMS_URL . 'assets/css/frontend/tutorial-display.css',
				array(),
				AIDDATA_LMS_VERSION,
				'all'
			);
		}

		// Enqueue enrollment styles on tutorial pages
		if ( is_singular( 'aiddata_tutorial' ) ) {
			wp_enqueue_style(
				'aiddata-lms-enrollment',
				AIDDATA_LMS_URL . 'assets/css/frontend/enrollment.css',
				array(),
				AIDDATA_LMS_VERSION,
				'all'
			);
		}

		// Enqueue tutorial navigation styles for active tutorial interface
		if ( is_singular( 'aiddata_tutorial' ) && isset( $_GET['action'] ) && 'continue' === $_GET['action'] ) {
			wp_enqueue_style(
				'aiddata-lms-tutorial-navigation',
				AIDDATA_LMS_URL . 'assets/css/frontend/tutorial-navigation.css',
				array(),
				AIDDATA_LMS_VERSION,
				'all'
			);
		}
	}

	/**
	 * Enqueue frontend scripts
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function enqueue_scripts(): void {
		// Only enqueue on tutorial pages
		if ( is_singular( 'aiddata_tutorial' ) ) {
			// Enqueue enrollment script
			wp_enqueue_script(
				'aiddata-lms-enrollment',
				AIDDATA_LMS_URL . 'assets/js/frontend/enrollment.js',
				array( 'jquery' ),
				AIDDATA_LMS_VERSION,
				true
			);

			// Localize script with AJAX URL and nonces
			wp_localize_script(
				'aiddata-lms-enrollment',
				'aiddataLMS',
				array(
					'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
					'enrollmentNonce'  => wp_create_nonce( 'aiddata-lms-enrollment' ),
					'progressNonce'    => wp_create_nonce( 'aiddata-lms-progress' ),
					'tutorialId'       => get_the_ID(),
					'tutorialUrl'      => get_permalink( get_the_ID() ),
					'confirmUnenroll'  => __( 'Are you sure you want to unenroll? Your progress will be saved but you will need to re-enroll to continue.', 'aiddata-lms' ),
					'strings'          => array(
						'confirmFinish' => __( 'Are you sure you want to finish this tutorial?', 'aiddata-lms' ),
					),
				)
			);

			// Enqueue tutorial navigation script for active tutorial interface
			if ( isset( $_GET['action'] ) && 'continue' === $_GET['action'] ) {
				wp_enqueue_script(
					'aiddata-lms-tutorial-navigation',
					AIDDATA_LMS_URL . 'assets/js/frontend/tutorial-navigation.js',
					array( 'jquery' ),
					AIDDATA_LMS_VERSION,
					true
				);

				wp_enqueue_style(
					'aiddata-lms-tutorial-navigation',
					AIDDATA_LMS_URL . 'assets/css/frontend/tutorial-navigation.css',
					array(),
					AIDDATA_LMS_VERSION
				);
			}

			// Always enqueue progress persistence styles on tutorial pages
			wp_enqueue_style(
				'aiddata-lms-progress-persistence',
				AIDDATA_LMS_URL . 'assets/css/frontend/progress-persistence.css',
				array(),
				AIDDATA_LMS_VERSION
			);
		}
	}

	/**
	 * Template hierarchy integration
	 *
	 * Load plugin templates for tutorials, falling back to theme templates if they exist.
	 *
	 * @since 2.0.0
	 * @param string $template The path to the template.
	 * @return string Modified template path.
	 */
	public function template_include( string $template ): string {
		// Single tutorial template
		if ( is_singular( 'aiddata_tutorial' ) ) {
			// Check if theme has override
			$theme_template = locate_template( 'single-aiddata_tutorial.php' );
			if ( $theme_template ) {
				return $theme_template;
			}

			// Use plugin template
			$plugin_template = AIDDATA_LMS_PATH . 'templates/single-aiddata_tutorial.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}

		// Archive template (including taxonomies)
		if ( is_post_type_archive( 'aiddata_tutorial' ) || is_tax( array( 'aiddata_tutorial_cat', 'aiddata_tutorial_tag', 'aiddata_tutorial_difficulty' ) ) ) {
			// Check if theme has override
			$theme_template = locate_template( 'archive-aiddata_tutorial.php' );
			if ( $theme_template ) {
				return $theme_template;
			}

			// Use plugin template
			$plugin_template = AIDDATA_LMS_PATH . 'templates/archive-aiddata_tutorial.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}

		return $template;
	}

	/**
	 * Register shortcodes
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_shortcodes(): void {
		add_shortcode( 'aiddata_tutorial_filters', array( $this, 'render_tutorial_filters' ) );
	}

	/**
	 * Render tutorial filters shortcode
	 *
	 * Displays search and filter controls for the tutorial archive.
	 *
	 * @since 2.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_tutorial_filters( $atts ): string {
		$atts = shortcode_atts(
			array(
				'show_search'     => true,
				'show_category'   => true,
				'show_difficulty' => true,
				'show_sort'       => true,
			),
			$atts,
			'aiddata_tutorial_filters'
		);

		// Convert string booleans
		$atts['show_search']     = filter_var( $atts['show_search'], FILTER_VALIDATE_BOOLEAN );
		$atts['show_category']   = filter_var( $atts['show_category'], FILTER_VALIDATE_BOOLEAN );
		$atts['show_difficulty'] = filter_var( $atts['show_difficulty'], FILTER_VALIDATE_BOOLEAN );
		$atts['show_sort']       = filter_var( $atts['show_sort'], FILTER_VALIDATE_BOOLEAN );

		ob_start();
		?>
		<form class="tutorial-filters" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'aiddata_tutorial' ) ); ?>">
			<?php if ( $atts['show_search'] ) : ?>
				<div class="filter-item search-filter">
					<input 
						type="search" 
						name="s" 
						placeholder="<?php esc_attr_e( 'Search tutorials...', 'aiddata-lms' ); ?>"
						value="<?php echo esc_attr( get_search_query() ); ?>"
						aria-label="<?php esc_attr_e( 'Search tutorials', 'aiddata-lms' ); ?>"
					>
				</div>
			<?php endif; ?>
			
			<?php if ( $atts['show_category'] ) : ?>
				<div class="filter-item category-filter">
					<?php
					wp_dropdown_categories(
						array(
							'taxonomy'        => 'aiddata_tutorial_cat',
							'show_option_all' => __( 'All Categories', 'aiddata-lms' ),
							'name'            => 'tutorial_category',
							'selected'        => get_query_var( 'tutorial_category' ),
							'hierarchical'    => true,
							'hide_empty'      => true,
							'value_field'     => 'slug',
							'orderby'         => 'name',
							'class'           => 'tutorial-category-dropdown',
						)
					);
					?>
				</div>
			<?php endif; ?>
			
			<?php if ( $atts['show_difficulty'] ) : ?>
				<div class="filter-item difficulty-filter">
					<?php
					wp_dropdown_categories(
						array(
							'taxonomy'        => 'aiddata_tutorial_difficulty',
							'show_option_all' => __( 'All Levels', 'aiddata-lms' ),
							'name'            => 'tutorial_difficulty',
							'selected'        => get_query_var( 'tutorial_difficulty' ),
							'hide_empty'      => true,
							'value_field'     => 'slug',
							'orderby'         => 'name',
							'class'           => 'tutorial-difficulty-dropdown',
						)
					);
					?>
				</div>
			<?php endif; ?>
			
			<?php if ( $atts['show_sort'] ) : ?>
				<div class="filter-item sort-filter">
					<select name="orderby" aria-label="<?php esc_attr_e( 'Sort by', 'aiddata-lms' ); ?>">
						<option value=""><?php esc_html_e( 'Sort by...', 'aiddata-lms' ); ?></option>
						<option value="date" <?php selected( get_query_var( 'orderby' ), 'date' ); ?>><?php esc_html_e( 'Newest First', 'aiddata-lms' ); ?></option>
						<option value="title" <?php selected( get_query_var( 'orderby' ), 'title' ); ?>><?php esc_html_e( 'Title', 'aiddata-lms' ); ?></option>
						<option value="popular" <?php selected( get_query_var( 'orderby' ), 'popular' ); ?>><?php esc_html_e( 'Most Popular', 'aiddata-lms' ); ?></option>
					</select>
				</div>
			<?php endif; ?>
			
			<button type="submit" class="filter-submit">
				<?php esc_html_e( 'Filter', 'aiddata-lms' ); ?>
			</button>
		</form>
		<?php
		return ob_get_clean();
	}
}

