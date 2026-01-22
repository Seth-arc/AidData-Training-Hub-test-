<?php
/**
 * Register all taxonomies for the plugin
 *
 * @link       https://aiddata.org
 * @since      2.0.0
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all taxonomies for the plugin.
 *
 * Maintains all taxonomies:
 * - Tutorial Categories (hierarchical)
 * - Tutorial Tags (flat)
 * - Tutorial Difficulty (hierarchical)
 *
 * @since      2.0.0
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 * @author     AidData <info@aiddata.org>
 */
class AidData_LMS_Taxonomies {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 0 );
	}

	/**
	 * Register all taxonomies.
	 *
	 * @since    2.0.0
	 */
	public function register_taxonomies(): void {
		$this->register_tutorial_category();
		$this->register_tutorial_tag();
		$this->register_tutorial_difficulty();
	}

	/**
	 * Register Tutorial Category taxonomy.
	 *
	 * Hierarchical taxonomy for organizing tutorials into categories.
	 * Supports parent/child relationships like WordPress categories.
	 *
	 * @since    2.0.0
	 */
	private function register_tutorial_category(): void {
		$labels = array(
			'name'              => _x( 'Tutorial Categories', 'taxonomy general name', 'aiddata-lms' ),
			'singular_name'     => _x( 'Tutorial Category', 'taxonomy singular name', 'aiddata-lms' ),
			'search_items'      => __( 'Search Categories', 'aiddata-lms' ),
			'all_items'         => __( 'All Categories', 'aiddata-lms' ),
			'parent_item'       => __( 'Parent Category', 'aiddata-lms' ),
			'parent_item_colon' => __( 'Parent Category:', 'aiddata-lms' ),
			'edit_item'         => __( 'Edit Category', 'aiddata-lms' ),
			'update_item'       => __( 'Update Category', 'aiddata-lms' ),
			'add_new_item'      => __( 'Add New Category', 'aiddata-lms' ),
			'new_item_name'     => __( 'New Category Name', 'aiddata-lms' ),
			'menu_name'         => __( 'Categories', 'aiddata-lms' ),
		);

		$args = array(
			'labels'            => $labels,
			'description'       => __( 'Tutorial categories for organizing courses', 'aiddata-lms' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
			'rest_base'         => 'tutorial-categories',
			'rewrite'           => array( 'slug' => 'tutorial-category' ),
		);

		register_taxonomy( 'aiddata_tutorial_cat', array( 'aiddata_tutorial' ), $args );
	}

	/**
	 * Register Tutorial Tag taxonomy.
	 *
	 * Flat (non-hierarchical) taxonomy for tagging tutorials with keywords.
	 * Works like WordPress post tags.
	 *
	 * @since    2.0.0
	 */
	private function register_tutorial_tag(): void {
		$labels = array(
			'name'                       => _x( 'Tutorial Tags', 'taxonomy general name', 'aiddata-lms' ),
			'singular_name'              => _x( 'Tutorial Tag', 'taxonomy singular name', 'aiddata-lms' ),
			'search_items'               => __( 'Search Tags', 'aiddata-lms' ),
			'popular_items'              => __( 'Popular Tags', 'aiddata-lms' ),
			'all_items'                  => __( 'All Tags', 'aiddata-lms' ),
			'edit_item'                  => __( 'Edit Tag', 'aiddata-lms' ),
			'update_item'                => __( 'Update Tag', 'aiddata-lms' ),
			'add_new_item'               => __( 'Add New Tag', 'aiddata-lms' ),
			'new_item_name'              => __( 'New Tag Name', 'aiddata-lms' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'aiddata-lms' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'aiddata-lms' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'aiddata-lms' ),
			'not_found'                  => __( 'No tags found.', 'aiddata-lms' ),
			'menu_name'                  => __( 'Tags', 'aiddata-lms' ),
		);

		$args = array(
			'labels'            => $labels,
			'description'       => __( 'Tutorial tags for keyword organization', 'aiddata-lms' ),
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_in_rest'      => true,
			'rest_base'         => 'tutorial-tags',
			'rewrite'           => array( 'slug' => 'tutorial-tag' ),
		);

		register_taxonomy( 'aiddata_tutorial_tag', array( 'aiddata_tutorial' ), $args );
	}

	/**
	 * Register Tutorial Difficulty taxonomy.
	 *
	 * Hierarchical taxonomy for setting tutorial difficulty levels.
	 * Default terms: Beginner, Intermediate, Advanced.
	 *
	 * @since    2.0.0
	 */
	private function register_tutorial_difficulty(): void {
		$labels = array(
			'name'              => _x( 'Difficulty Levels', 'taxonomy general name', 'aiddata-lms' ),
			'singular_name'     => _x( 'Difficulty Level', 'taxonomy singular name', 'aiddata-lms' ),
			'search_items'      => __( 'Search Levels', 'aiddata-lms' ),
			'all_items'         => __( 'All Levels', 'aiddata-lms' ),
			'edit_item'         => __( 'Edit Level', 'aiddata-lms' ),
			'update_item'       => __( 'Update Level', 'aiddata-lms' ),
			'add_new_item'      => __( 'Add New Level', 'aiddata-lms' ),
			'new_item_name'     => __( 'New Level Name', 'aiddata-lms' ),
			'menu_name'         => __( 'Difficulty', 'aiddata-lms' ),
		);

		$args = array(
			'labels'            => $labels,
			'description'       => __( 'Tutorial difficulty levels', 'aiddata-lms' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rest_base'         => 'tutorial-difficulty',
			'rewrite'           => array( 'slug' => 'difficulty' ),
			'meta_box_cb'       => 'post_categories_meta_box',
		);

		register_taxonomy( 'aiddata_tutorial_difficulty', array( 'aiddata_tutorial' ), $args );
	}

	/**
	 * Get all terms from a taxonomy.
	 *
	 * @since    2.0.0
	 * @param    string $taxonomy    The taxonomy name.
	 * @param    array  $args        Optional. Query arguments.
	 * @return   array               Array of WP_Term objects.
	 */
	public static function get_terms( string $taxonomy, array $args = array() ): array {
		$defaults = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		$terms = get_terms( $args );

		return is_array( $terms ) ? $terms : array();
	}

	/**
	 * Get tutorials by taxonomy term.
	 *
	 * @since    2.0.0
	 * @param    string $taxonomy    The taxonomy name.
	 * @param    string $term_slug   The term slug.
	 * @param    array  $args        Optional. Query arguments.
	 * @return   array               Array of WP_Post objects.
	 */
	public static function get_tutorials_by_term( string $taxonomy, string $term_slug, array $args = array() ): array {
		$defaults = array(
			'post_type'      => 'aiddata_tutorial',
			'posts_per_page' => -1,
			'tax_query'      => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $term_slug,
				),
			),
		);

		$args = wp_parse_args( $args, $defaults );

		$query = new WP_Query( $args );

		return $query->posts;
	}

	/**
	 * Check if a tutorial has a specific term.
	 *
	 * @since    2.0.0
	 * @param    int    $post_id     The post ID.
	 * @param    string $term_slug   The term slug.
	 * @param    string $taxonomy    The taxonomy name.
	 * @return   bool                True if post has the term.
	 */
	public static function has_term( int $post_id, string $term_slug, string $taxonomy ): bool {
		return has_term( $term_slug, $taxonomy, $post_id );
	}
}

