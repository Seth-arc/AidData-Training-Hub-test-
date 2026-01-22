<?php
/**
 * Register Custom Post Types
 *
 * Defines and registers the custom post types used by the plugin.
 * Handles Tutorial and Quiz post types with Gutenberg support,
 * REST API integration, and custom capabilities.
 *
 * @package    AidData_LMS
 * @subpackage AidData_LMS/includes
 * @since      2.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AidData_LMS_Post_Types
 *
 * Registers custom post types for the LMS plugin.
 *
 * @since 2.0.0
 */
class AidData_LMS_Post_Types {

	/**
	 * Constructor.
	 *
	 * Hooks into WordPress to register post types and configure columns.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ) );
		
		// Admin columns.
		add_filter( 'manage_aiddata_tutorial_posts_columns', array( $this, 'add_tutorial_columns' ) );
		add_action( 'manage_aiddata_tutorial_posts_custom_column', array( $this, 'render_tutorial_column' ), 10, 2 );
		add_filter( 'manage_edit-aiddata_tutorial_sortable_columns', array( $this, 'sortable_tutorial_columns' ) );
		
		// Bulk actions.
		add_filter( 'bulk_actions-edit-aiddata_tutorial', array( $this, 'add_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-aiddata_tutorial', array( $this, 'handle_bulk_actions' ), 10, 3 );
		
		// Quick edit.
		add_action( 'quick_edit_custom_box', array( $this, 'add_quick_edit_fields' ), 10, 2 );
		add_action( 'save_post_aiddata_tutorial', array( $this, 'save_quick_edit_data' ) );
		
		// Filters.
		add_action( 'restrict_manage_posts', array( $this, 'add_admin_filters' ) );
		add_filter( 'parse_query', array( $this, 'filter_tutorials_query' ) );
		
		// Admin notices.
		add_action( 'admin_notices', array( $this, 'bulk_action_notices' ) );
		
		// Enqueue admin CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register all custom post types.
	 *
	 * Calls individual registration methods for each post type.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_post_types(): void {
		$this->register_tutorial_post_type();
		$this->register_quiz_post_type();
	}

	/**
	 * Register the Tutorial custom post type.
	 *
	 * Registers the aiddata_tutorial post type with Gutenberg support,
	 * REST API integration, and custom capabilities.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_tutorial_post_type(): void {
		$labels = array(
			'name'                  => _x( 'Tutorials', 'Post type general name', 'aiddata-lms' ),
			'singular_name'         => _x( 'Tutorial', 'Post type singular name', 'aiddata-lms' ),
			'menu_name'             => _x( 'Tutorials', 'Admin Menu text', 'aiddata-lms' ),
			'name_admin_bar'        => _x( 'Tutorial', 'Add New on Toolbar', 'aiddata-lms' ),
			'add_new'               => __( 'Add New', 'aiddata-lms' ),
			'add_new_item'          => __( 'Add New Tutorial', 'aiddata-lms' ),
			'new_item'              => __( 'New Tutorial', 'aiddata-lms' ),
			'edit_item'             => __( 'Edit Tutorial', 'aiddata-lms' ),
			'view_item'             => __( 'View Tutorial', 'aiddata-lms' ),
			'all_items'             => __( 'All Tutorials', 'aiddata-lms' ),
			'search_items'          => __( 'Search Tutorials', 'aiddata-lms' ),
			'parent_item_colon'     => __( 'Parent Tutorials:', 'aiddata-lms' ),
			'not_found'             => __( 'No tutorials found.', 'aiddata-lms' ),
			'not_found_in_trash'    => __( 'No tutorials found in Trash.', 'aiddata-lms' ),
			'featured_image'        => _x( 'Tutorial Cover Image', 'Overrides the "Featured Image" phrase', 'aiddata-lms' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase', 'aiddata-lms' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase', 'aiddata-lms' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase', 'aiddata-lms' ),
			'archives'              => _x( 'Tutorial archives', 'The post type archive label used in nav menus', 'aiddata-lms' ),
			'insert_into_item'      => _x( 'Insert into tutorial', 'Overrides the "Insert into post" phrase', 'aiddata-lms' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this tutorial', 'Overrides the "Uploaded to this post" phrase', 'aiddata-lms' ),
			'filter_items_list'     => _x( 'Filter tutorials list', 'Screen reader text', 'aiddata-lms' ),
			'items_list_navigation' => _x( 'Tutorials list navigation', 'Screen reader text', 'aiddata-lms' ),
			'items_list'            => _x( 'Tutorials list', 'Screen reader text', 'aiddata-lms' ),
		);

		$args = array(
			'labels'                => $labels,
			'description'           => __( 'Tutorial courses and lessons', 'aiddata-lms' ),
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'tutorial' ),
			'capability_type'       => array( 'aiddata_tutorial', 'aiddata_tutorials' ),
			'map_meta_cap'          => true,
			'has_archive'           => true,
			'hierarchical'          => false,
			'menu_position'         => 20,
			'menu_icon'             => 'dashicons-welcome-learn-more',
			'show_in_rest'          => true,
			'rest_base'             => 'tutorials',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
		);

		register_post_type( 'aiddata_tutorial', $args );
	}

	/**
	 * Register the Quiz custom post type.
	 *
	 * Registers the aiddata_quiz post type with REST API support.
	 * Quizzes are shown as a submenu under Tutorials.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_quiz_post_type(): void {
		$labels = array(
			'name'               => _x( 'Quizzes', 'Post type general name', 'aiddata-lms' ),
			'singular_name'      => _x( 'Quiz', 'Post type singular name', 'aiddata-lms' ),
			'menu_name'          => _x( 'Quizzes', 'Admin Menu text', 'aiddata-lms' ),
			'add_new'            => __( 'Add New', 'aiddata-lms' ),
			'add_new_item'       => __( 'Add New Quiz', 'aiddata-lms' ),
			'new_item'           => __( 'New Quiz', 'aiddata-lms' ),
			'edit_item'          => __( 'Edit Quiz', 'aiddata-lms' ),
			'view_item'          => __( 'View Quiz', 'aiddata-lms' ),
			'all_items'          => __( 'All Quizzes', 'aiddata-lms' ),
			'search_items'       => __( 'Search Quizzes', 'aiddata-lms' ),
			'not_found'          => __( 'No quizzes found.', 'aiddata-lms' ),
			'not_found_in_trash' => __( 'No quizzes found in Trash.', 'aiddata-lms' ),
		);

		$args = array(
			'labels'                => $labels,
			'description'           => __( 'Tutorial quizzes and assessments', 'aiddata-lms' ),
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=aiddata_tutorial',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'quiz' ),
			'capability_type'       => array( 'aiddata_quiz', 'aiddata_quizzes' ),
			'map_meta_cap'          => true,
			'has_archive'           => false,
			'hierarchical'          => false,
			'menu_position'         => null,
			'show_in_rest'          => true,
			'rest_base'             => 'quizzes',
			'supports'              => array( 'title', 'custom-fields', 'revisions' ),
		);

		register_post_type( 'aiddata_quiz', $args );
	}

	/**
	 * Add custom admin columns for tutorials.
	 *
	 * Adds enhanced columns including thumbnail, steps, enrollments, and completion rate.
	 *
	 * @since 2.0.0
	 * @param array $columns Existing columns.
	 * @return array Modified columns.
	 */
	public function add_tutorial_columns( array $columns ): array {
		// Remove default date column temporarily.
		$date = $columns['date'];
		unset( $columns['date'] );
		
		// Add custom columns.
		$columns['thumbnail']        = __( 'Thumbnail', 'aiddata-lms' );
		$columns['steps']            = __( 'Steps', 'aiddata-lms' );
		$columns['enrollments']      = __( 'Enrollments', 'aiddata-lms' );
		$columns['active']           = __( 'Active', 'aiddata-lms' );
		$columns['completion_rate']  = __( 'Completion', 'aiddata-lms' );
		$columns['difficulty']       = __( 'Difficulty', 'aiddata-lms' );
		$columns['date']             = $date; // Re-add date at end.
		
		return $columns;
	}

	/**
	 * Render content for custom admin columns.
	 *
	 * Outputs the content for enhanced columns.
	 *
	 * @since 2.0.0
	 * @param string $column  Column identifier.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function render_tutorial_column( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'thumbnail':
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
				} else {
					echo '<span class="dashicons dashicons-format-image"></span>';
				}
				break;
			
			case 'steps':
				$step_count = get_post_meta( $post_id, '_tutorial_step_count', true );
				if ( $step_count ) {
					printf(
						'<span class="step-count">%d</span>',
						absint( $step_count )
					);
				} else {
					echo '<span class="dashicons dashicons-minus"></span>';
				}
				break;
			
			case 'enrollments':
				$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
				$total = $enrollment_manager->get_enrollment_count( $post_id );
				printf(
					'<a href="%s">%d</a>',
					esc_url( admin_url( 'admin.php?page=aiddata-lms-enrollments&tutorial_id=' . $post_id ) ),
					$total
				);
				break;
			
			case 'active':
				$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
				$active = $enrollment_manager->get_enrollment_count( $post_id, 'active' );
				printf( '<span class="active-count">%d</span>', $active );
				break;
			
			case 'completion_rate':
				$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
				$total = $enrollment_manager->get_enrollment_count( $post_id );
				$completed = $enrollment_manager->get_enrollment_count( $post_id, 'completed' );
				
				if ( $total > 0 ) {
					$rate = round( ( $completed / $total ) * 100 );
					$color = $this->get_completion_color( $rate );
					printf(
						'<span class="completion-rate" style="color: %s;">%d%%</span>',
						esc_attr( $color ),
						$rate
					);
				} else {
					echo '<span class="dashicons dashicons-minus"></span>';
				}
				break;
			
			case 'difficulty':
				$terms = get_the_terms( $post_id, 'aiddata_tutorial_difficulty' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$term = array_shift( $terms );
					printf(
						'<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'aiddata_tutorial_difficulty' => $term->slug ), 'edit.php?post_type=aiddata_tutorial' ) ),
						esc_html( $term->name )
					);
				} else {
					echo '—';
				}
				break;
		}
	}
	
	/**
	 * Get color for completion rate display.
	 *
	 * @since 2.0.0
	 * @param float $rate Completion rate (0-100).
	 * @return string Hex color code.
	 */
	private function get_completion_color( float $rate ): string {
		if ( $rate >= 75 ) {
			return '#46b450'; // Green.
		} elseif ( $rate >= 50 ) {
			return '#ffb900'; // Yellow.
		} else {
			return '#dc3232'; // Red.
		}
	}

	/**
	 * Get all registered tutorial posts.
	 *
	 * Retrieves a list of all tutorials. Useful for admin pages
	 * and reporting.
	 *
	 * @since 2.0.0
	 * @param array $args Optional. Query arguments.
	 * @return WP_Post[] Array of post objects.
	 */
	public function get_tutorials( array $args = array() ): array {
		$defaults = array(
			'post_type'      => 'aiddata_tutorial',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$query_args = wp_parse_args( $args, $defaults );
		$query      = new WP_Query( $query_args );

		return $query->posts;
	}

	/**
	 * Get all registered quiz posts.
	 *
	 * Retrieves a list of all quizzes.
	 *
	 * @since 2.0.0
	 * @param array $args Optional. Query arguments.
	 * @return WP_Post[] Array of post objects.
	 */
	public function get_quizzes( array $args = array() ): array {
		$defaults = array(
			'post_type'      => 'aiddata_quiz',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$query_args = wp_parse_args( $args, $defaults );
		$query      = new WP_Query( $query_args );

		return $query->posts;
	}

	/**
	 * Check if a post is a tutorial.
	 *
	 * @since 2.0.0
	 * @param int|WP_Post $post Post ID or object.
	 * @return bool True if post is a tutorial, false otherwise.
	 */
	public function is_tutorial( $post ): bool {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		return $post instanceof WP_Post && 'aiddata_tutorial' === $post->post_type;
	}

	/**
	 * Check if a post is a quiz.
	 *
	 * @since 2.0.0
	 * @param int|WP_Post $post Post ID or object.
	 * @return bool True if post is a quiz, false otherwise.
	 */
	public function is_quiz( $post ): bool {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		return $post instanceof WP_Post && 'aiddata_quiz' === $post->post_type;
	}
	
	/**
	 * Make tutorial columns sortable.
	 *
	 * @since 2.0.0
	 * @param array $columns Existing sortable columns.
	 * @return array Modified sortable columns.
	 */
	public function sortable_tutorial_columns( array $columns ): array {
		$columns['enrollments']     = 'enrollments';
		$columns['completion_rate'] = 'completion_rate';
		$columns['steps']           = 'steps';
		
		return $columns;
	}
	
	/**
	 * Add bulk actions for tutorials.
	 *
	 * @since 2.0.0
	 * @param array $actions Existing bulk actions.
	 * @return array Modified bulk actions.
	 */
	public function add_bulk_actions( array $actions ): array {
		$actions['duplicate']         = __( 'Duplicate', 'aiddata-lms' );
		$actions['export_data']       = __( 'Export Data', 'aiddata-lms' );
		$actions['toggle_enrollment'] = __( 'Toggle Enrollment', 'aiddata-lms' );
		
		return $actions;
	}
	
	/**
	 * Handle bulk actions for tutorials.
	 *
	 * @since 2.0.0
	 * @param string $redirect_to Redirect URL.
	 * @param string $action      Bulk action being performed.
	 * @param array  $post_ids    Array of post IDs.
	 * @return string Modified redirect URL with query args.
	 */
	public function handle_bulk_actions( string $redirect_to, string $action, array $post_ids ): string {
		if ( empty( $post_ids ) ) {
			return $redirect_to;
		}
		
		switch ( $action ) {
			case 'duplicate':
				$count = $this->duplicate_tutorials( $post_ids );
				$redirect_to = add_query_arg( 'duplicated', $count, $redirect_to );
				break;
			
			case 'export_data':
				$this->export_tutorials_data( $post_ids );
				// Don't redirect for download.
				return $redirect_to;
			
			case 'toggle_enrollment':
				$count = $this->toggle_enrollment_status( $post_ids );
				$redirect_to = add_query_arg( 'enrollment_toggled', $count, $redirect_to );
				break;
		}
		
		return $redirect_to;
	}
	
	/**
	 * Duplicate selected tutorials.
	 *
	 * @since 2.0.0
	 * @param array $post_ids Array of post IDs to duplicate.
	 * @return int Number of tutorials duplicated.
	 */
	private function duplicate_tutorials( array $post_ids ): int {
		$count = 0;
		
		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			if ( ! $post || 'aiddata_tutorial' !== $post->post_type ) {
				continue;
			}
			
			// Create duplicate.
			$new_post = array(
				'post_title'   => $post->post_title . ' (Copy)',
				'post_content' => $post->post_content,
				'post_status'  => 'draft',
				'post_type'    => 'aiddata_tutorial',
				'post_author'  => get_current_user_id(),
			);
			
			$new_id = wp_insert_post( $new_post );
			
			if ( ! is_wp_error( $new_id ) ) {
				// Duplicate meta data.
				$this->duplicate_post_meta( $post_id, $new_id );
				
				// Duplicate taxonomies.
				$this->duplicate_post_taxonomies( $post_id, $new_id );
				
				$count++;
			}
		}
		
		return $count;
	}
	
	/**
	 * Duplicate post meta from source to target.
	 *
	 * @since 2.0.0
	 * @param int $source_id Source post ID.
	 * @param int $target_id Target post ID.
	 * @return void
	 */
	private function duplicate_post_meta( int $source_id, int $target_id ): void {
		$meta_keys = array(
			'_tutorial_short_description',
			'_tutorial_full_description',
			'_tutorial_duration',
			'_tutorial_steps',
			'_tutorial_step_count',
			'_tutorial_type',
			'_tutorial_access',
			'_tutorial_allow_enrollment',
			'_tutorial_require_approval',
			'_tutorial_enrollment_limit',
			'_tutorial_enrollment_deadline',
			'_tutorial_completion_requires_all_steps',
			'_tutorial_completion_requires_quiz',
			'_tutorial_generate_certificate',
			'_tutorial_show_in_catalog',
			'_tutorial_outcomes',
			'_tutorial_prerequisites',
		);
		
		foreach ( $meta_keys as $key ) {
			$value = get_post_meta( $source_id, $key, true );
			if ( $value ) {
				update_post_meta( $target_id, $key, $value );
			}
		}
	}
	
	/**
	 * Duplicate taxonomies from source to target.
	 *
	 * @since 2.0.0
	 * @param int $source_id Source post ID.
	 * @param int $target_id Target post ID.
	 * @return void
	 */
	private function duplicate_post_taxonomies( int $source_id, int $target_id ): void {
		$taxonomies = array( 'aiddata_tutorial_cat', 'aiddata_tutorial_tag', 'aiddata_tutorial_difficulty' );
		
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_post_terms( $source_id, $taxonomy, array( 'fields' => 'ids' ) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				wp_set_post_terms( $target_id, $terms, $taxonomy );
			}
		}
	}
	
	/**
	 * Export tutorial data to CSV.
	 *
	 * @since 2.0.0
	 * @param array $post_ids Array of post IDs to export.
	 * @return void
	 */
	private function export_tutorials_data( array $post_ids ): void {
		// Generate CSV export.
		$filename = 'tutorials-export-' . gmdate( 'Y-m-d-His' ) . '.csv';
		
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		
		$output = fopen( 'php://output', 'w' );
		
		// CSV header.
		fputcsv( $output, array(
			'ID',
			'Title',
			'Status',
			'Steps',
			'Duration',
			'Enrollments',
			'Active',
			'Completed',
			'Completion Rate',
			'Created',
		) );
		
		$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
		
		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			if ( ! $post ) {
				continue;
			}
			
			$total = $enrollment_manager->get_enrollment_count( $post_id );
			$active = $enrollment_manager->get_enrollment_count( $post_id, 'active' );
			$completed = $enrollment_manager->get_enrollment_count( $post_id, 'completed' );
			$rate = $total > 0 ? round( ( $completed / $total ) * 100, 2 ) : 0;
			
			fputcsv( $output, array(
				$post->ID,
				$post->post_title,
				$post->post_status,
				get_post_meta( $post->ID, '_tutorial_step_count', true ),
				get_post_meta( $post->ID, '_tutorial_duration', true ),
				$total,
				$active,
				$completed,
				$rate . '%',
				$post->post_date,
			) );
		}
		
		fclose( $output );
		exit;
	}
	
	/**
	 * Toggle enrollment status for tutorials.
	 *
	 * @since 2.0.0
	 * @param array $post_ids Array of post IDs.
	 * @return int Number of tutorials updated.
	 */
	private function toggle_enrollment_status( array $post_ids ): int {
		$count = 0;
		
		foreach ( $post_ids as $post_id ) {
			$current = get_post_meta( $post_id, '_tutorial_allow_enrollment', true );
			$new_value = ! $current;
			update_post_meta( $post_id, '_tutorial_allow_enrollment', $new_value );
			$count++;
		}
		
		return $count;
	}
	
	/**
	 * Add admin filter dropdowns.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function add_admin_filters(): void {
		global $typenow;
		
		if ( 'aiddata_tutorial' !== $typenow ) {
			return;
		}
		
		// Difficulty filter.
		wp_dropdown_categories( array(
			'show_option_all' => __( 'All Difficulties', 'aiddata-lms' ),
			'taxonomy'        => 'aiddata_tutorial_difficulty',
			'name'            => 'aiddata_tutorial_difficulty',
			'selected'        => isset( $_GET['aiddata_tutorial_difficulty'] ) ? $_GET['aiddata_tutorial_difficulty'] : '',
			'hierarchical'    => true,
			'hide_empty'      => false,
			'value_field'     => 'slug',
		) );
		
		// Enrollment status filter.
		$enrollment_status = isset( $_GET['enrollment_status'] ) ? $_GET['enrollment_status'] : '';
		?>
		<select name="enrollment_status">
			<option value=""><?php esc_html_e( 'All Enrollment Status', 'aiddata-lms' ); ?></option>
			<option value="open" <?php selected( $enrollment_status, 'open' ); ?>><?php esc_html_e( 'Open', 'aiddata-lms' ); ?></option>
			<option value="closed" <?php selected( $enrollment_status, 'closed' ); ?>><?php esc_html_e( 'Closed', 'aiddata-lms' ); ?></option>
		</select>
		<?php
		
		// Steps count filter.
		$steps_filter = isset( $_GET['steps_filter'] ) ? $_GET['steps_filter'] : '';
		?>
		<select name="steps_filter">
			<option value=""><?php esc_html_e( 'All Step Counts', 'aiddata-lms' ); ?></option>
			<option value="empty" <?php selected( $steps_filter, 'empty' ); ?>><?php esc_html_e( 'No Steps', 'aiddata-lms' ); ?></option>
			<option value="1-5" <?php selected( $steps_filter, '1-5' ); ?>><?php esc_html_e( '1-5 Steps', 'aiddata-lms' ); ?></option>
			<option value="6-10" <?php selected( $steps_filter, '6-10' ); ?>><?php esc_html_e( '6-10 Steps', 'aiddata-lms' ); ?></option>
			<option value="11+" <?php selected( $steps_filter, '11+' ); ?>><?php esc_html_e( '11+ Steps', 'aiddata-lms' ); ?></option>
		</select>
		<?php
	}
	
	/**
	 * Filter tutorials query based on admin filters.
	 *
	 * @since 2.0.0
	 * @param WP_Query $query WordPress query object.
	 * @return void
	 */
	public function filter_tutorials_query( WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		
		if ( 'aiddata_tutorial' !== $query->get( 'post_type' ) ) {
			return;
		}
		
		$meta_query = array();
		
		// Enrollment status filter.
		if ( ! empty( $_GET['enrollment_status'] ) ) {
			$status = $_GET['enrollment_status'];
			$meta_query[] = array(
				'key'     => '_tutorial_allow_enrollment',
				'value'   => ( 'open' === $status ) ? '1' : '0',
				'compare' => '=',
			);
		}
		
		// Steps filter.
		if ( ! empty( $_GET['steps_filter'] ) ) {
			$steps = $_GET['steps_filter'];
			
			if ( 'empty' === $steps ) {
				$meta_query[] = array(
					'key'     => '_tutorial_step_count',
					'compare' => 'NOT EXISTS',
				);
			} elseif ( '1-5' === $steps ) {
				$meta_query[] = array(
					'key'     => '_tutorial_step_count',
					'value'   => array( 1, 5 ),
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC',
				);
			} elseif ( '6-10' === $steps ) {
				$meta_query[] = array(
					'key'     => '_tutorial_step_count',
					'value'   => array( 6, 10 ),
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC',
				);
			} elseif ( '11+' === $steps ) {
				$meta_query[] = array(
					'key'     => '_tutorial_step_count',
					'value'   => 10,
					'compare' => '>',
					'type'    => 'NUMERIC',
				);
			}
		}
		
		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', $meta_query );
		}
	}
	
	/**
	 * Add quick edit fields for tutorials.
	 *
	 * @since 2.0.0
	 * @param string $column_name Column identifier.
	 * @param string $post_type   Post type.
	 * @return void
	 */
	public function add_quick_edit_fields( string $column_name, string $post_type ): void {
		if ( 'aiddata_tutorial' !== $post_type ) {
			return;
		}
		
		if ( 'title' === $column_name ) {
			?>
			<fieldset class="inline-edit-col-right">
				<div class="inline-edit-col">
					<label>
						<span class="title"><?php esc_html_e( 'Duration (minutes)', 'aiddata-lms' ); ?></span>
						<input type="number" name="tutorial_duration" min="0">
					</label>
					
					<label>
						<span class="title"><?php esc_html_e( 'Enrollment Limit', 'aiddata-lms' ); ?></span>
						<input type="number" name="tutorial_enrollment_limit" min="0">
					</label>
					
					<label>
						<input type="checkbox" name="tutorial_allow_enrollment" value="1">
						<span class="checkbox-title"><?php esc_html_e( 'Allow Enrollment', 'aiddata-lms' ); ?></span>
					</label>
					
					<label>
						<input type="checkbox" name="tutorial_show_in_catalog" value="1">
						<span class="checkbox-title"><?php esc_html_e( 'Show in Catalog', 'aiddata-lms' ); ?></span>
					</label>
				</div>
			</fieldset>
			<?php
			wp_nonce_field( 'aiddata_quick_edit', 'aiddata_quick_edit_nonce' );
		}
	}
	
	/**
	 * Save quick edit data.
	 *
	 * @since 2.0.0
	 * @param int $post_id Post ID being saved.
	 * @return void
	 */
	public function save_quick_edit_data( int $post_id ): void {
		// Verify nonce.
		if ( ! isset( $_POST['aiddata_quick_edit_nonce'] ) ) {
			return;
		}
		
		if ( ! wp_verify_nonce( $_POST['aiddata_quick_edit_nonce'], 'aiddata_quick_edit' ) ) {
			return;
		}
		
		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		// Save duration.
		if ( isset( $_POST['tutorial_duration'] ) ) {
			update_post_meta( $post_id, '_tutorial_duration', absint( $_POST['tutorial_duration'] ) );
		}
		
		// Save enrollment limit.
		if ( isset( $_POST['tutorial_enrollment_limit'] ) ) {
			update_post_meta( $post_id, '_tutorial_enrollment_limit', absint( $_POST['tutorial_enrollment_limit'] ) );
		}
		
		// Save checkboxes.
		update_post_meta( $post_id, '_tutorial_allow_enrollment', isset( $_POST['tutorial_allow_enrollment'] ) ? 1 : 0 );
		update_post_meta( $post_id, '_tutorial_show_in_catalog', isset( $_POST['tutorial_show_in_catalog'] ) ? 1 : 0 );
	}
	
	/**
	 * Display admin notices for bulk actions.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function bulk_action_notices(): void {
		global $pagenow, $typenow;
		
		if ( 'edit.php' !== $pagenow || 'aiddata_tutorial' !== $typenow ) {
			return;
		}
		
		// Duplication notice.
		if ( ! empty( $_GET['duplicated'] ) ) {
			$count = absint( $_GET['duplicated'] );
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				sprintf(
					/* translators: %d: number of tutorials */
					esc_html( _n( '%d tutorial duplicated.', '%d tutorials duplicated.', $count, 'aiddata-lms' ) ),
					$count
				)
			);
		}
		
		// Enrollment toggle notice.
		if ( ! empty( $_GET['enrollment_toggled'] ) ) {
			$count = absint( $_GET['enrollment_toggled'] );
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				sprintf(
					/* translators: %d: number of tutorials */
					esc_html( _n( 'Enrollment status toggled for %d tutorial.', 'Enrollment status toggled for %d tutorials.', $count, 'aiddata-lms' ) ),
					$count
				)
			);
		}
	}
	
	/**
	 * Enqueue admin assets.
	 *
	 * @since 2.0.0
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook ): void {
		// Only load on tutorial list screen.
		if ( 'edit.php' !== $hook ) {
			return;
		}
		
		$screen = get_current_screen();
		if ( ! $screen || 'aiddata_tutorial' !== $screen->post_type ) {
			return;
		}
		
		// Enqueue CSS.
		wp_enqueue_style(
			'aiddata-lms-tutorial-list',
			AIDDATA_LMS_URL . 'assets/css/admin/tutorial-list.css',
			array(),
			AIDDATA_LMS_VERSION
		);
		
		// Enqueue JavaScript.
		wp_enqueue_script(
			'aiddata-lms-tutorial-list',
			AIDDATA_LMS_URL . 'assets/js/admin/tutorial-list.js',
			array( 'jquery', 'inline-edit-post' ),
			AIDDATA_LMS_VERSION,
			true
		);
	}
}

