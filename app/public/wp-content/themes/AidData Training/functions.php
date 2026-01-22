<?php
/**
 * Twenty Twenty-Four functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Twenty Twenty-Four
 * @since Twenty Twenty-Four 1.0
 */

/**
 * Enable LearnPress template overrides in theme.
 * This allows the theme to use custom templates in /learnpress/ folder.
 * Required since LearnPress 4.0.0 which disabled theme overrides by default.
 */
add_filter( 'learn-press/override-templates', '__return_true' );

/**
 * Force custom single-course template for block themes.
 * LearnPress skips template_loader for block themes, so we need to use template_include directly.
 */
add_filter( 'template_include', 'aiddata_force_learnpress_template', 99 );
function aiddata_force_learnpress_template( $template ) {
	if ( is_singular( 'lp_course' ) ) {
		global $wp;

		// Check if we're viewing a course item (lesson/quiz) via query vars
		$course_item = isset( $wp->query_vars['course-item'] ) ? $wp->query_vars['course-item'] : '';

		if ( ! empty( $course_item ) ) {
			// This is a lesson or quiz page - use content-single-item.php
			$custom_template = get_template_directory() . '/learnpress/content-single-item.php';
			if ( file_exists( $custom_template ) ) {
				return $custom_template;
			}
		} else {
			// This is the main course page - use single-course.php
			$custom_template = get_template_directory() . '/learnpress/single-course.php';
			if ( file_exists( $custom_template ) ) {
				return $custom_template;
			}
		}
	}
	return $template;
}

/**
 * Force custom LearnPress templates from theme.
 */
add_filter( 'learn_press_get_template', 'aiddata_force_learnpress_templates', 10, 2 );
function aiddata_force_learnpress_templates( $located, $template_name ) {
	// List of templates to force from theme
	$templates_to_override = array(
		'content-single-course.php',
		'single-course/tabs/curriculum.php',
		'single-course/tabs/overview.php',
		'profile/tabs.php',
	);

	if ( in_array( $template_name, $templates_to_override, true ) ) {
		$custom_template = get_template_directory() . '/learnpress/' . $template_name;
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}
	}
	return $located;
}

/**
 * Disable lazy loading only on LearnPress course item views.
 */
function aiddata_disable_lazy_loading_learnpress_items( $default, $tag_name, $context ) {
	if ( 'img' !== $tag_name ) {
		return $default;
	}

	if ( ! is_singular( 'lp_course' ) ) {
		return $default;
	}

	$course_item = get_query_var( 'course-item' );
	if ( ! $course_item && isset( $_GET['course-item'] ) ) {
		$course_item = sanitize_title( wp_unslash( $_GET['course-item'] ) );
	}

	if ( ! $course_item ) {
		return $default;
	}

	return false;
}
add_filter( 'wp_lazy_loading_enabled', 'aiddata_disable_lazy_loading_learnpress_items', 10, 3 );

add_filter( 'manage_lp_course_posts_columns', function ( $cols ) {
    $cols['lp_course_id'] = 'ID';
    return $cols;
} );

add_action( 'manage_lp_course_posts_custom_column', function ( $column, $post_id ) {
    if ( 'lp_course_id' === $column ) {
        echo (int) $post_id;
    }
}, 10, 2 );

// Display the assigned page template in the Pages list table.
add_filter( 'manage_edit-page_columns', function ( $columns ) {
    $columns['page_template'] = __( 'Template', 'twentytwentyfour' );
    return $columns;
} );

add_action( 'manage_page_posts_custom_column', function ( $column, $post_id ) {
    if ( 'page_template' !== $column ) {
        return;
    }

    $template = get_page_template_slug( $post_id );

    if ( ! $template ) {
        echo esc_html__( 'Default', 'twentytwentyfour' );
        return;
    }

    $templates = wp_get_theme()->get_page_templates( null, 'page' );

    if ( isset( $templates[ $template ] ) ) {
        echo esc_html( $templates[ $template ] );
        return;
    }

    echo esc_html( $template );
}, 10, 2 );


/**
 * Register block styles.
 */

if ( ! function_exists( 'twentytwentyfour_block_styles' ) ) :
	/**
	 * Register custom block styles
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_styles() {

		register_block_style(
			'core/details',
			array(
				'name'         => 'arrow-icon-details',
				'label'        => __( 'Arrow icon', 'twentytwentyfour' ),
				/*
				 * Styles for the custom Arrow icon style of the Details block
				 */
				'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}

				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}

				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
			)
		);
		register_block_style(
			'core/post-terms',
			array(
				'name'         => 'pill',
				'label'        => __( 'Pill', 'twentytwentyfour' ),
				/*
				 * Styles variation for post terms
				 * https://github.com/WordPress/gutenberg/issues/24956
				 */
				'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}

				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfour' ),
				/*
				 * Styles for the custom checkmark list block style
				 * https://github.com/WordPress/gutenberg/issues/51480
				 */
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
		register_block_style(
			'core/navigation-link',
			array(
				'name'         => 'arrow-link',
				'label'        => __( 'With arrow', 'twentytwentyfour' ),
				/*
				 * Styles for the custom arrow nav link block style
				 */
				'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
			)
		);
		register_block_style(
			'core/heading',
			array(
				'name'         => 'asterisk',
				'label'        => __( 'With asterisk', 'twentytwentyfour' ),
				'inline_style' => "
				.is-style-asterisk:before {
					content: '';
					width: 1.5rem;
					height: 3rem;
					background: var(--wp--preset--color--contrast-2, currentColor);
					clip-path: polygon(10% 40%, 40% 40%, 40% 10%, 60% 10%, 60% 40%, 90% 40%, 90% 60%, 60% 60%, 60% 90%, 40% 90%, 40% 60%, 10% 60%);
					display: inline-block;
					vertical-align: middle;
					margin-inline-end: 0.5rem;
				}

				/* RTL support */
				.rtl .is-style-asterisk:before {
					margin-inline-start: 0.5rem;
					margin-inline-end: 0;
				}",
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_styles' );

/**
 * Enqueue block stylesheets.
 */

if ( ! function_exists( 'twentytwentyfour_block_stylesheets' ) ) :
	/**
	 * Enqueue custom block stylesheets
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_stylesheets() {
		/**
		 * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
		 * for a specific block. These will only get loaded when the block is rendered
		 * (both in the editor and on the front end), improving performance
		 * and reducing the amount of data requested by visitors.
		 *
		 * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
		 */
		wp_enqueue_block_style(
			'core/button',
			array(
				'handle' => 'twentytwentyfour-button-style-outline',
				'src'    => get_parent_theme_file_uri( 'assets/css/button-outline.css' ),
				'ver'    => wp_get_theme( get_template() )->get( 'Version' ),
				'path'   => get_parent_theme_file_path( 'assets/css/button-outline.css' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_stylesheets' );

/**
 * Register pattern categories.
 */

if ( ! function_exists( 'twentytwentyfour_pattern_categories' ) ) :
	/**
	 * Register pattern categories
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfour_page',
			array(
				'label'       => _x( 'Pages', 'Block pattern category', 'twentytwentyfour' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfour' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_pattern_categories' );

// Course post type registration moved to AidData LMS plugin
// The plugin handles course management with 'aiddata_course' post type

/**
 * Enqueue Course Template assets
 */
function aiddata_enqueue_course_template_assets() {
    // Only load these assets on pages using the Course Template
    if (is_page_template('page-templates/course-template.php')) {
        // Enqueue the course template CSS
        wp_enqueue_style(
            'course-template-styles',
            get_template_directory_uri() . '/assets/css/course-template.css',
            array(),
            wp_get_theme()->get('Version')
        );
        
        // Enqueue the course template JavaScript
        wp_enqueue_script(
            'course-template-script',
            get_template_directory_uri() . '/assets/js/course-template.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'aiddata_enqueue_course_template_assets');

/**
 * Enqueue quiz assets
 */
function aiddata_enqueue_quiz_assets() {
    // Only load on single quiz pages
    if (is_singular('aiddata_quiz')) {
        // Enqueue quiz CSS
        wp_enqueue_style(
            'aiddata-quiz-styles',
            get_template_directory_uri() . '/assets/css/quiz.css',
            array(),
            wp_get_theme()->get('Version')
        );
        
        // Enqueue quiz JavaScript
        wp_enqueue_script(
            'aiddata-quiz-script',
            get_template_directory_uri() . '/assets/js/quiz.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
    }
    
    // Enqueue tutorial assets
    if (is_singular('aiddata_tutorial')) {
        // Enqueue tutorial JavaScript
        wp_enqueue_script(
            'aiddata-tutorial-script',
            get_template_directory_uri() . '/assets/js/tutorial.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'aiddata_enqueue_quiz_assets');

/**
 * Add custom meta fields for courses
 */
function aiddata_add_course_meta_boxes() {
    add_meta_box(
        'course_details',
        'Course Details',
        'aiddata_course_details_callback',
        'course',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'aiddata_add_course_meta_boxes');

/**
 * Callback function for meta box
 */
function aiddata_course_details_callback($post) {
    wp_nonce_field('aiddata_save_course_details', 'aiddata_course_details_nonce');
    
    $course_type = get_post_meta($post->ID, 'course_type', true);
    $course_duration = get_post_meta($post->ID, 'course_duration', true);
    $course_level = get_post_meta($post->ID, 'course_level', true);
    $course_link = get_post_meta($post->ID, 'course_link', true);
    $trailer_video = get_post_meta($post->ID, 'trailer_video', true);
    $is_coming_soon = get_post_meta($post->ID, 'is_coming_soon', true);
    $has_badge = get_post_meta($post->ID, 'has_badge', true);
    $has_certificate = get_post_meta($post->ID, 'has_certificate', true);
    
    // Output form fields
    ?>
    <p>
        <label for="course_type">Course Type:</label>
        <select name="course_type" id="course_type">
            <option value="course" <?php selected($course_type, 'course'); ?>>Course</option>
            <option value="simulation" <?php selected($course_type, 'simulation'); ?>>Simulation</option>
            <option value="tutorial" <?php selected($course_type, 'tutorial'); ?>>Tutorial</option>
            <option value="interview" <?php selected($course_type, 'interview'); ?>>Interview</option>
            <option value="game" <?php selected($course_type, 'game'); ?>>Game</option>
            <option value="tools" <?php selected($course_type, 'tools'); ?>>Tool</option>
        </select>
    </p>
    <p>
        <label for="course_duration">Duration:</label>
        <input type="text" name="course_duration" id="course_duration" value="<?php echo esc_attr($course_duration); ?>" placeholder="e.g. 12-16 hours">
    </p>
    <p>
        <label for="course_level">Level:</label>
        <select name="course_level" id="course_level">
            <option value="Introductory" <?php selected($course_level, 'Introductory'); ?>>Introductory</option>
            <option value="Intermediate" <?php selected($course_level, 'Intermediate'); ?>>Intermediate</option>
            <option value="Advanced" <?php selected($course_level, 'Advanced'); ?>>Advanced</option>
            <option value="All Levels" <?php selected($course_level, 'All Levels'); ?>>All Levels</option>
        </select>
    </p>
    <p>
        <label for="course_link">Course Link:</label>
        <input type="text" name="course_link" id="course_link" value="<?php echo esc_attr($course_link); ?>" class="widefat">
    </p>
    <p>
        <label for="trailer_video">Trailer Video URL:</label>
        <input type="text" name="trailer_video" id="trailer_video" value="<?php echo esc_attr($trailer_video); ?>" class="widefat">
    </p>
    <p>
        <input type="checkbox" name="is_coming_soon" id="is_coming_soon" <?php checked($is_coming_soon, 'on'); ?>>
        <label for="is_coming_soon">Mark as Coming Soon</label>
    </p>
    <p>
        <input type="checkbox" name="has_badge" id="has_badge" <?php checked($has_badge, 'on'); ?>>
        <label for="has_badge">Offers Digital Badge</label>
    </p>
    <p>
        <input type="checkbox" name="has_certificate" id="has_certificate" <?php checked($has_certificate, 'on'); ?>>
        <label for="has_certificate">Offers Certificate</label>
    </p>
    <?php
}

/**
 * Save meta box data
 */
function aiddata_save_course_meta($post_id) {
    if (!isset($_POST['aiddata_course_details_nonce']) || !wp_verify_nonce($_POST['aiddata_course_details_nonce'], 'aiddata_save_course_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = [
        'course_type',
        'course_duration',
        'course_level',
        'course_link',
        'trailer_video'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    $checkboxes = [
        'is_coming_soon',
        'has_badge',
        'has_certificate'
    ];

    foreach ($checkboxes as $checkbox) {
        if (isset($_POST[$checkbox])) {
            update_post_meta($post_id, $checkbox, 'on');
        } else {
            delete_post_meta($post_id, $checkbox);
        }
    }
}
add_action('save_post', 'aiddata_save_course_meta');

/**
 * Enqueue AidData Training Hub styles and scripts
 */
function aiddata_enqueue_assets() {
    // Only enqueue on front page or when using the AidData template
    if (is_front_page() || is_page_template('front-page.php')) {
        // Styles
        wp_enqueue_style('aiddata-lms-style', get_template_directory_uri() . '/assets/css/lms.css', array(), '1.0.0');
        wp_enqueue_style('aiddata-auth-style', get_template_directory_uri() . '/assets/css/auth.css', array(), '1.0.0');
        wp_enqueue_style('aiddata-modals-style', get_template_directory_uri() . '/assets/css/modals.css', array(), '1.0.0');
        
        // Scripts
        wp_enqueue_script('aiddata-lms-script', get_template_directory_uri() . '/assets/js/lms.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('aiddata-modals-script', get_template_directory_uri() . '/assets/js/modals.js', array('jquery'), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'aiddata_enqueue_assets');

/**
 * Enqueue shared loading and transition assets.
 */
function aiddata_enqueue_page_transitions() {
    if (is_admin()) {
        return;
    }

    wp_enqueue_style(
        'loading-screen',
        get_template_directory_uri() . '/assets/css/loading-screen.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'page-transitions',
        get_template_directory_uri() . '/assets/js/page-transitions.js',
        array(),
        '1.0.0',
        true
    );

    wp_add_inline_script(
        'page-transitions',
        'window.aiddataPageTransitions = { logoUrl: "' . esc_url( get_template_directory_uri() . '/assets/images/logodark.png' ) . '" };',
        'before'
    );
}
add_action('wp_enqueue_scripts', 'aiddata_enqueue_page_transitions');

/**
 * Skip page-level loading screens when a transition just ran.
 */
function aiddata_skip_loading_screen_on_transition() {
    ?>
    <script>
    (function() {
        try {
            if (sessionStorage.getItem('aiddataSkipLoadingScreen')) {
                document.documentElement.classList.add('skip-loading-screen');
                sessionStorage.removeItem('aiddataSkipLoadingScreen');
            }
        } catch (error) {
            // Ignore storage errors in restricted environments.
        }
    })();
    </script>
    <?php
}
add_action('wp_head', 'aiddata_skip_loading_screen_on_transition', 1);

/**
 * Enqueue enrollment protection script globally
 */
function aiddata_enqueue_enrollment_protection() {
    // Enqueue on all pages (tutorials, courses, etc.)
    wp_enqueue_script(
        'aiddata-enrollment-protection',
        get_template_directory_uri() . '/assets/js/enrollment-protection.js',
        array(),
        '1.0.0',
        true
    );
    
    // Pass user login status to JavaScript
    wp_localize_script('aiddata-enrollment-protection', 'aidataEnrollment', array(
        'isLoggedIn' => is_user_logged_in(),
        'loginUrl' => wp_login_url(get_permalink()),
        'signupUrl' => site_url('/signup'),
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('aiddata_enrollment')
    ));
    
    // Make login status available globally
    wp_add_inline_script('aiddata-enrollment-protection', 
        'window.aidataUserLoggedIn = ' . (is_user_logged_in() ? 'true' : 'false') . ';',
        'before'
    );
}
add_action('wp_enqueue_scripts', 'aiddata_enqueue_enrollment_protection');

/**
 * Handle direct enrollment requests via ?enroll-course=ID and redirect users to the target page.
 */
function aiddata_handle_direct_enrollment_redirect() {
    if ( empty( $_GET['enroll-course'] ) ) {
        return;
    }

    $course_id = absint( $_GET['enroll-course'] );

    if ( ! $course_id ) {
        return;
    }

    $raw_redirect     = isset( $_GET['redirect_to'] ) ? wp_unslash( $_GET['redirect_to'] ) : '';
    $default_redirect = aiddata_get_course_start_url( $course_id );

    if ( ! $default_redirect ) {
        $default_redirect = home_url( '/' );
    }

    if ( ! $raw_redirect ) {
        $redirect_to = $default_redirect;
    } else {
        $redirect_to = wp_validate_redirect( esc_url_raw( $raw_redirect ), $default_redirect );
    }

    if ( ! is_user_logged_in() ) {
        $login_target = add_query_arg(
            array(
                'enroll-course' => $course_id,
            ),
            home_url( '/' )
        );

        if ( $redirect_to ) {
            $login_target = add_query_arg( 'redirect_to', $redirect_to, $login_target );
        }

        wp_safe_redirect( wp_login_url( $login_target ) );
        exit;
    }

    aiddata_enroll_current_user_in_course( $course_id );

    if ( ! $redirect_to ) {
        $redirect_to = $default_redirect;
    }

    wp_safe_redirect( $redirect_to );
    exit;
}
add_action( 'template_redirect', 'aiddata_handle_direct_enrollment_redirect', 1 );

/**
 * Enroll the current user in LearnPress course when possible.
 */
function aiddata_enroll_current_user_in_course( $course_id ) {
    if ( ! $course_id || ! is_user_logged_in() ) {
        return;
    }

    $user_id = get_current_user_id();

    if ( function_exists( 'learn_press_enroll_course' ) ) {
        learn_press_enroll_course( $course_id, $user_id );
        return;
    }

    if ( function_exists( 'learn_press_get_current_user' ) ) {
        $lp_user = learn_press_get_current_user();

        if ( $lp_user && method_exists( $lp_user, 'enroll' ) ) {
            $lp_user->enroll( $course_id );
        }
    }
}

/**
 * Resolve the "first lesson" (or first available item) permalink for a LearnPress course.
 */
function aiddata_get_course_start_url( $course_id ) {
    $course_id = absint( $course_id );

    if ( ! $course_id ) {
        return home_url( '/' );
    }

    $fallback = get_permalink( $course_id );
    if ( ! $fallback ) {
        $fallback = home_url( '/' );
    }

    if ( ! function_exists( 'learn_press_get_course' ) ) {
        return $fallback;
    }

    $course = learn_press_get_course( $course_id );

    if ( ! $course ) {
        return $fallback;
    }

    $first_item_id = aiddata_locate_first_course_item_id( $course );

    if ( ! $first_item_id ) {
        return $fallback;
    }

    $item_link = '';

    if ( method_exists( $course, 'get_item_link' ) ) {
        $item_link = $course->get_item_link( $first_item_id );
    }

    if ( ! $item_link ) {
        $item_link = get_permalink( $first_item_id );

        if ( $item_link ) {
            $item_link = add_query_arg( 'course', $course_id, $item_link );
        }
    }

    return $item_link ? $item_link : $fallback;
}

/**
 * Attempt to find the first lesson/item ID from an LP_Course instance.
 */
function aiddata_locate_first_course_item_id( $course ) {
    if ( ! $course ) {
        return 0;
    }

    $first_from_api = 0;

    if ( method_exists( $course, 'get_first_item' ) ) {
        $first_from_api = aiddata_normalize_course_item_id( $course->get_first_item() );

        if ( $first_from_api ) {
            return $first_from_api;
        }
    }

    if ( method_exists( $course, 'get_first_item_id' ) ) {
        $first_from_api = absint( $course->get_first_item_id() );

        if ( $first_from_api ) {
            return $first_from_api;
        }
    }

    $fallback_item_id = 0;

    if ( method_exists( $course, 'get_items' ) ) {
        $items = $course->get_items();

        if ( $items instanceof Traversable ) {
            $items = iterator_to_array( $items );
        }

        if ( is_array( $items ) ) {
            foreach ( $items as $item ) {
                // Sections may wrap nested items under an `items` key.
                if ( is_array( $item ) && isset( $item['items'] ) ) {
                    foreach ( $item['items'] as $section_item ) {
                        $item_id = aiddata_normalize_course_item_id( $section_item );

                        if ( ! $item_id ) {
                            continue;
                        }

                        if ( 'lp_lesson' === get_post_type( $item_id ) ) {
                            return $item_id;
                        }

                        if ( ! $fallback_item_id ) {
                            $fallback_item_id = $item_id;
                        }
                    }

                    continue;
                }

                $item_id = aiddata_normalize_course_item_id( $item );

                if ( ! $item_id ) {
                    continue;
                }

                if ( 'lp_lesson' === get_post_type( $item_id ) ) {
                    return $item_id;
                }

                if ( ! $fallback_item_id ) {
                    $fallback_item_id = $item_id;
                }
            }
        }
    }

    return $fallback_item_id;
}

/**
 * Normalize a curriculum item into a plain post ID.
 */
function aiddata_normalize_course_item_id( $item ) {
    if ( is_numeric( $item ) ) {
        return absint( $item );
    }

    if ( is_object( $item ) ) {
        if ( method_exists( $item, 'get_id' ) ) {
            return absint( $item->get_id() );
        }

        if ( isset( $item->ID ) ) {
            return absint( $item->ID );
        }

        if ( isset( $item->item_id ) ) {
            return absint( $item->item_id );
        }
    }

    if ( is_array( $item ) ) {
        if ( isset( $item['id'] ) ) {
            return absint( $item['id'] );
        }

        if ( isset( $item['item_id'] ) ) {
            return absint( $item['item_id'] );
        }
    }

    return 0;
}

/**
 * Register custom widget areas for AidData
 */
function aiddata_register_widget_areas() {
    register_sidebar(array(
        'name'          => 'Training Hub Sidebar',
        'id'            => 'training-hub-sidebar',
        'description'   => 'Widget area for Training Hub pages',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'aiddata_register_widget_areas');

/**
 * Add custom image sizes for course thumbnails
 */
function aiddata_add_image_sizes() {
    add_image_size('course-thumbnail', 600, 340, true);
    add_image_size('course-large', 1200, 675, true);
}
add_action('after_setup_theme', 'aiddata_add_image_sizes');

/**
 * Add custom course types filter to admin
 */
function aiddata_add_course_type_filter() {
    global $typenow;
    
    if ($typenow == 'course') {
        $course_types = array(
            'course' => 'Course',
            'simulation' => 'Simulation',
            'tutorial' => 'Tutorial',
            'interview' => 'Interview',
            'game' => 'Game',
            'tools' => 'Tool'
        );
        
        $current_course_type = isset($_GET['course_type']) ? $_GET['course_type'] : '';
        
        echo '<select name="course_type">';
        echo '<option value="">All Course Types</option>';
        
        foreach ($course_types as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($current_course_type, $value, false) . '>' . esc_html($label) . '</option>';
        }
        
        echo '</select>';
    }
}
add_action('restrict_manage_posts', 'aiddata_add_course_type_filter');

/**
 * Filter courses by course type in admin
 */
function aiddata_filter_courses_by_type($query) {
    global $pagenow, $typenow;
    
    if (is_admin() && $pagenow == 'edit.php' && $typenow == 'course' && isset($_GET['course_type']) && $_GET['course_type'] != '') {
        $query->query_vars['meta_key'] = 'course_type';
        $query->query_vars['meta_value'] = $_GET['course_type'];
    }
}
add_action('pre_get_posts', 'aiddata_filter_courses_by_type');

/**
 * Add custom course columns to admin
 */
function aiddata_add_course_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        if ($key == 'title') {
            $new_columns[$key] = $value;
            $new_columns['course_type'] = 'Type';
            $new_columns['course_level'] = 'Level';
            $new_columns['course_duration'] = 'Duration';
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter('manage_course_posts_columns', 'aiddata_add_course_columns');

/**
 * Populate custom course columns
 */
function aiddata_populate_course_columns($column, $post_id) {
    switch ($column) {
        case 'course_type':
            $course_type = get_post_meta($post_id, 'course_type', true);
            echo ucfirst($course_type ?: 'Course');
            break;
        case 'course_level':
            $course_level = get_post_meta($post_id, 'course_level', true);
            echo $course_level ?: 'Not set';
            break;
        case 'course_duration':
            $course_duration = get_post_meta($post_id, 'course_duration', true);
            echo $course_duration ?: 'Not set';
            break;
    }
}
add_action('manage_course_posts_custom_column', 'aiddata_populate_course_columns', 10, 2);

/**
 * Make custom columns sortable
 */
function aiddata_sortable_course_columns($columns) {
    $columns['course_type'] = 'course_type';
    $columns['course_level'] = 'course_level';
    return $columns;
}
add_filter('manage_edit-course_sortable_columns', 'aiddata_sortable_course_columns');

/**
 * Handle sorting custom columns
 */
function aiddata_sort_course_columns($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ($query->get('orderby') == 'course_type') {
        $query->set('meta_key', 'course_type');
        $query->set('orderby', 'meta_value');
    }
    
    if ($query->get('orderby') == 'course_level') {
        $query->set('meta_key', 'course_level');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'aiddata_sort_course_columns');

/**
 * Add course categories taxonomy
 */
function aiddata_register_course_taxonomies() {
    register_taxonomy(
        'course_category',
        'lp_course', // LearnPress uses 'lp_course' not 'course'
        array(
            'labels' => array(
                'name' => 'Course Categories',
                'singular_name' => 'Course Category',
                'search_items' => 'Search Course Categories',
                'all_items' => 'All Course Categories',
                'parent_item' => 'Parent Course Category',
                'parent_item_colon' => 'Parent Course Category:',
                'edit_item' => 'Edit Course Category',
                'update_item' => 'Update Course Category',
                'add_new_item' => 'Add New Course Category',
                'new_item_name' => 'New Course Category Name',
                'menu_name' => 'Categories',
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'course-category'),
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'aiddata_register_course_taxonomies');

/**
 * Register REST API fields for courses
 */
function aiddata_register_course_rest_fields() {
    register_rest_field(
        'course',
        'course_meta',
        array(
            'get_callback' => 'aiddata_get_course_meta',
            'update_callback' => null,
            'schema' => null,
        )
    );
}
add_action('rest_api_init', 'aiddata_register_course_rest_fields');

/**
 * Callback to get course meta for REST API
 */
function aiddata_get_course_meta($object) {
    $post_id = $object['id'];
    
    return array(
        'course_type' => get_post_meta($post_id, 'course_type', true),
        'course_duration' => get_post_meta($post_id, 'course_duration', true),
        'course_level' => get_post_meta($post_id, 'course_level', true),
        'course_link' => get_post_meta($post_id, 'course_link', true),
        'trailer_video' => get_post_meta($post_id, 'trailer_video', true),
        'is_coming_soon' => get_post_meta($post_id, 'is_coming_soon', true) === 'on',
        'has_badge' => get_post_meta($post_id, 'has_badge', true) === 'on',
        'has_certificate' => get_post_meta($post_id, 'has_certificate', true) === 'on',
        'thumbnail_url' => get_the_post_thumbnail_url($post_id, 'course-thumbnail')
    );
}

// Remove WordPress credit from footer
function remove_footer_credit() {
    ?>
    <style type="text/css">
        #footer p {
            display: none !important;
        }
    </style>
    <?php
}
add_action('wp_footer', 'remove_footer_credit', 100);

/**
 * Authentication Integration Functions
 * Integrates custom frontend with WordPress authentication
 */

/**
 * Enqueue authentication scripts
 */
function aiddata_enqueue_auth_scripts() {
    wp_enqueue_script('aiddata-auth', get_template_directory_uri() . '/assets/js/auth-integration.js', array('jquery'), '1.0.0', true);
    
    // Pass Ajax URL and security nonce to script
    wp_localize_script('aiddata-auth', 'auth_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('custom-auth-nonce'),
        'home_url' => home_url()
    ));
}
add_action('wp_enqueue_scripts', 'aiddata_enqueue_auth_scripts');

/**
 * AJAX login handler
 */
function aiddata_ajax_login() {
    check_ajax_referer('custom-auth-nonce', 'security');
    
    $user_data = array(
        'user_login' => sanitize_email($_POST['username']),
        'user_password' => $_POST['password'],
        'remember' => true
    );
    
    $user = wp_signon($user_data, false);
    
    if(is_wp_error($user)) {
        wp_send_json_error(array('message' => $user->get_error_message()));
    } else {
        wp_send_json_success(array('message' => 'Login successful!'));
    }
    
    wp_die();
}
add_action('wp_ajax_nopriv_custom_ajax_login', 'aiddata_ajax_login');

/**
 * AJAX registration handler
 */
function aiddata_ajax_register() {
    check_ajax_referer('custom-auth-nonce', 'security');
    
    $user_email = sanitize_email($_POST['email']);
    $user_name = sanitize_text_field($_POST['fullName']);
    $password = $_POST['password'];
    
    if (email_exists($user_email)) {
        wp_send_json_error(array('message' => 'This email address is already registered. Please log in instead.'));
        wp_die();
    }
    
    // Create username from email
    $username = explode('@', $user_email)[0] . '_' . wp_rand(100, 999);
    
    $user_id = wp_create_user($username, $password, $user_email);
    
    if(is_wp_error($user_id)) {
        wp_send_json_error(array('message' => $user_id->get_error_message()));
    } else {
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $user_name,
            'first_name' => $user_name
        ));
        
        // Add organization as user meta if provided
        if(!empty($_POST['organization'])) {
            update_user_meta($user_id, 'organization', sanitize_text_field($_POST['organization']));
        }
        
        // Newsletter subscription logic
        if(isset($_POST['newsletter']) && $_POST['newsletter'] === 'on') {
            update_user_meta($user_id, 'newsletter_subscription', 'yes');
        }
        
        // Automatically log the user in
        $user = wp_signon(array(
            'user_login' => $username,
            'user_password' => $password,
            'remember' => true
        ), false);
        
        if(is_wp_error($user)) {
            wp_send_json_error(array('message' => 'Registration successful but there was an error logging you in. Please log in manually.'));
        } else {
            wp_send_json_success(array('message' => 'Registration successful!'));
        }
    }
    
    wp_die();
}
add_action('wp_ajax_nopriv_custom_ajax_register', 'aiddata_ajax_register');

/**
 * AJAX password reset handler
 */
function aiddata_ajax_reset_password() {
    check_ajax_referer('custom-auth-nonce', 'security');
    
    $email = sanitize_email($_POST['email']);
    $user = get_user_by('email', $email);
    
    if(!$user) {
        wp_send_json_error(array('message' => 'No user found with that email address.'));
    } else {
        $key = get_password_reset_key($user);
        if(is_wp_error($key)) {
            wp_send_json_error(array('message' => 'Error generating password reset link.'));
        }
        
        $reset_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');
        
        // Send email with password reset link
        $subject = 'Password Reset Request for AidData Training Hub';
        $message = "Hello " . $user->display_name . ",\n\n";
        $message .= "You requested a password reset for your AidData Training Hub account. Click the link below to set a new password:\n\n";
        $message .= $reset_link . "\n\n";
        $message .= "If you didn't request this, please ignore this email.\n\n";
        $message .= "Thanks,\nAidData Training Hub Team";
        
        $sent = wp_mail($email, $subject, $message);
        
        if($sent) {
            wp_send_json_success(array('message' => 'Password reset link has been sent to your email address.'));
        } else {
            wp_send_json_error(array('message' => 'There was an error sending the email. Please try again later.'));
        }
    }
    
    wp_die();
}
add_action('wp_ajax_nopriv_custom_ajax_reset_password', 'aiddata_ajax_reset_password');

/**
 * AJAX logout handler
 */
function aiddata_ajax_logout() {
    check_ajax_referer('custom-auth-nonce', 'security');
    wp_logout();
    wp_send_json_success(array('message' => 'Logged out successfully'));
    wp_die();
}
add_action('wp_ajax_custom_ajax_logout', 'aiddata_ajax_logout');

/**
 * AJAX authentication status handler
 */
function aiddata_get_auth_status() {
    $response = array(
        'loggedIn' => is_user_logged_in(),
        'userName' => '',
        'userEmail' => '',
        'isAdmin' => false
    );
    
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $response['userName'] = $current_user->display_name;
        $response['userEmail'] = $current_user->user_email;
        $response['isAdmin'] = current_user_can('manage_options');
    }
    
    wp_send_json($response);
    wp_die();
}
add_action('wp_ajax_get_auth_status', 'aiddata_get_auth_status');
add_action('wp_ajax_nopriv_get_auth_status', 'aiddata_get_auth_status');

/**
 * Secure course pages for logged-in users only
 */
function aiddata_secure_course_pages() {
    // If not logged in and trying to access a course page
    if (!is_user_logged_in() && is_singular('course')) {
        // Redirect to homepage
        wp_redirect(home_url('/?login=required'));
        exit;
    }
    
    // Check if accessing course directory or specific course content pages
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $course_paths = array(
        '/course_pages/',
        '/tutorials_pages/',
        '/simulation_pages/',
        '/game_pages/'
    );
    
    foreach ($course_paths as $path) {
        if (strpos($request_uri, $path) !== false && !is_user_logged_in()) {
            wp_redirect(home_url('/?login=required'));
            exit;
        }
    }
}
add_action('template_redirect', 'aiddata_secure_course_pages');

/**
 * Add login required notification
 */
function aiddata_login_required_notice() {
    if (isset($_GET['login']) && $_GET['login'] === 'required') {
        wp_enqueue_script('aiddata-auth-notice', '', array(), '', true);
        wp_add_inline_script('aiddata-auth-notice', '
            document.addEventListener("DOMContentLoaded", function() {
                const authModal = document.getElementById("authModal");
                if (authModal) {
                    authModal.style.display = "flex";
                    const modalTitle = authModal.querySelector("#authModalTitle");
                    if (modalTitle) {
                        modalTitle.textContent = "Login Required";
                    }
                    const modalText = authModal.querySelector("p");
                    if (modalText) {
                        modalText.textContent = "Please log in or create an account to access this content";
                    }
                }
            });
        ');
    }
}
add_action('wp_enqueue_scripts', 'aiddata_login_required_notice');

/**
 * Temporary function to delete the "Navigating Global Development Finance" course
 * REMOVE THIS FUNCTION AFTER USE
 */
function delete_navigating_finance_course() {
    // Only run once
    if (get_option('deleted_finance_course')) {
        return;
    }
    
    // Only run on admin pages
    if (!is_admin()) {
        return;
    }
    
    // Find posts with the title containing "Navigating Global Development Finance"
    $args = array(
        'post_type' => 'course',
        'post_status' => 'any',
        'posts_per_page' => -1,
        's' => 'Navigating Global Development Finance' // Using search instead of exact title
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            // Delete the post
            wp_delete_post($post_id, true);
        }
        wp_reset_postdata();
        
        // Add admin notice
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Successfully deleted "Navigating Global Development Finance" course.</p></div>';
        });
    }
    
    // Mark as completed so we don't run this again
    update_option('deleted_finance_course', true);
}
add_action('admin_init', 'delete_navigating_finance_course');

/**
 * Custom LifterLMS Dashboard Functions
 * 
 * Override default LifterLMS dashboard wrapper functions to align with our theme design
 */

// Remove default LifterLMS dashboard wrapper actions
remove_action( 'lifterlms_before_student_dashboard', 'lifterlms_template_student_dashboard_wrapper_open', 10 );
remove_action( 'lifterlms_after_student_dashboard', 'lifterlms_template_student_dashboard_wrapper_close', 10 );

/**
 * Custom wrapper opening for LifterLMS dashboard
 * Simplified to work with our page template
 */
function twentytwentyfour_llms_dashboard_wrapper_open( $layout ) {
    $current = LLMS_Student_Dashboard::get_current_tab( 'slug' );
    echo '<div class="llms-student-dashboard ' . esc_attr( $current ) . ' llms-sd-layout-' . esc_attr( $layout ) . '" data-current="' . esc_attr( $current ) . '">';
}
add_action( 'lifterlms_before_student_dashboard', 'twentytwentyfour_llms_dashboard_wrapper_open', 10 );

/**
 * Custom wrapper closing for LifterLMS dashboard
 */
function twentytwentyfour_llms_dashboard_wrapper_close() {
    echo '</div><!-- .llms-student-dashboard -->';
}
add_action( 'lifterlms_after_student_dashboard', 'twentytwentyfour_llms_dashboard_wrapper_close', 10 );

/**
 * Enqueue custom LifterLMS dashboard styles
 */
function twentytwentyfour_llms_dashboard_styles() {
    if ( is_page() && has_shortcode( get_post()->post_content, 'lifterlms_my_account' ) ) {
        wp_enqueue_style( 'twentytwentyfour-llms-dashboard', get_template_directory_uri() . '/assets/css/llms-dashboard.css', array(), wp_get_theme()->get( 'Version' ) );
    }
}
add_action( 'wp_enqueue_scripts', 'twentytwentyfour_llms_dashboard_styles' );

/**
 * Add custom body class for LifterLMS dashboard pages
 */
function twentytwentyfour_llms_dashboard_body_class( $classes ) {
    if ( is_page() && has_shortcode( get_post()->post_content, 'lifterlms_my_account' ) ) {
        $classes[] = 'llms-dashboard-page';
        $classes[] = 'has-auth-styles';
    }
    return $classes;
}
add_filter( 'body_class', 'twentytwentyfour_llms_dashboard_body_class' );

/**
 * Customize LifterLMS dashboard title for our theme
 */
function twentytwentyfour_llms_dashboard_title_filter( $title, $data ) {
    // Only modify on our custom page template
    if ( is_page_template( 'page-myaccount.php' ) ) {
        // Remove the default title wrapper since we have our own
        return '';
    }
    return $title;
}
add_filter( 'lifterlms_student_dashboard_title', 'twentytwentyfour_llms_dashboard_title_filter', 15, 2 );

/**
 * Ensure proper authentication redirect for LifterLMS
 */
function twentytwentyfour_llms_auth_redirect() {
    if ( is_page_template( 'page-myaccount.php' ) && ! is_user_logged_in() ) {
        // Additional authentication handling can be added here if needed
        // For now, LifterLMS handles this through the shortcode
    }
}
add_action( 'template_redirect', 'twentytwentyfour_llms_auth_redirect' );

/**
 * Add theme support for LifterLMS
 */
add_theme_support( 'lifterlms-sidebars' );
add_theme_support( 'lifterlms' );

/**
 * Customize LifterLMS templates path for our theme
 */
function twentytwentyfour_llms_template_path() {
    return 'lifterlms/';
}
add_filter( 'lifterlms_template_path', 'twentytwentyfour_llms_template_path' );

/**
 * Modify LifterLMS dashboard tabs to show only specific components
 * Keep only: Dashboard (required), My Courses, My Certificates, Edit Account, Notifications, Sign Out
 * But hide Dashboard from navigation
 */
function twentytwentyfour_filter_dashboard_tabs($tabs) {
    // Keep these tabs (including dashboard which is required by LifterLMS)
    $allowed_tabs = array(
        'dashboard',        // Required by LifterLMS system (but will hide from nav)
        'view-courses',     // My Courses
        'view-certificates', // My Certificates
        'edit-account',     // Edit Account
        'notifications',    // Notifications
        'signout'          // Sign Out
    );
    
    $filtered_tabs = array();
    foreach ($allowed_tabs as $tab_key) {
        if (isset($tabs[$tab_key])) {
            // Hide dashboard from navigation but keep the tab
            if ($tab_key === 'dashboard') {
                $tabs[$tab_key]['nav_item'] = false;
            }
            $filtered_tabs[$tab_key] = $tabs[$tab_key];
        }
    }
    
    // Debug: Log what tabs we're returning (remove after testing)
    if (is_page_template('page-myaccount.php')) {
        error_log('Filtered dashboard tabs: ' . print_r(array_keys($filtered_tabs), true));
    }
    
    return $filtered_tabs;
}
add_filter('llms_get_student_dashboard_tabs', 'twentytwentyfour_filter_dashboard_tabs');

/**
 * Filter navigation tabs to exclude dashboard and show only desired items
 */
function twentytwentyfour_filter_dashboard_nav_tabs($tabs) {
    // Only show these tabs in navigation (exclude dashboard)
    $allowed_nav_tabs = array(
        'view-courses',     // My Courses
        'view-certificates', // My Certificates
        'edit-account',     // Edit Account
        'notifications',    // Notifications
        'signout'          // Sign Out
    );
    
    $filtered_tabs = array();
    foreach ($allowed_nav_tabs as $tab_key) {
        if (isset($tabs[$tab_key])) {
            $filtered_tabs[$tab_key] = $tabs[$tab_key];
        }
    }
    
    return $filtered_tabs;
}
add_filter('llms_get_student_dashboard_tabs_for_nav', 'twentytwentyfour_filter_dashboard_nav_tabs');

/**
 * Customize LifterLMS dashboard navigation display
 */
function twentytwentyfour_customize_llms_nav() {
    if (is_page_template('page-myaccount.php')) {
        echo '<style>
        /* Show and style the navigation */
        .llms-sd-nav { 
            display: block !important; 
            margin-bottom: 30px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }
        
        /* Hide the separator spans */
        .llms-sd-nav .llms-sep {
            display: none !important;
        }
        
        /* Style the navigation we want to keep */
        .llms-sd-nav .llms-sd-items {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin: 0 auto;
            max-width: 800px;
            list-style: none;
            padding: 0;
        }
        
        .llms-sd-nav .llms-sd-item {
            flex: 0 0 auto;
        }
        
        .llms-sd-nav .llms-sd-link {
            background: #026447;
            color: white !important;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            min-width: 140px;
            text-align: center;
        }
        
        .llms-sd-nav .llms-sd-link:hover,
        .llms-sd-nav .llms-sd-item.current .llms-sd-link {
            background: #015336 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            text-decoration: none;
            color: white !important;
        }
        
        /* Responsive navigation */
        @media (max-width: 768px) {
            .llms-sd-nav .llms-sd-items {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }
            
            .llms-sd-nav .llms-sd-link {
                min-width: 200px;
            }
        }
        </style>';
    }
}
add_action('wp_head', 'twentytwentyfour_customize_llms_nav');

/**
 * Ensure proper viewport meta tag for mobile responsiveness
 */
function twentytwentyfour_viewport_meta() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">';
}
add_action('wp_head', 'twentytwentyfour_viewport_meta', 1);

/**
 * Include custom post types
 */
// Include Interview Post Type
if (file_exists(get_template_directory() . '/includes/post-type-interview.php')) {
    require_once get_template_directory() . '/includes/post-type-interview.php';
}

/**
 * Force the LearnPress "Profile" tab label to read "My Profile".
 *
 * @param string      $title        Original tab title.
 * @param string      $tab_key      Current tab key.
 * @param string|null $section_key  Section key when filtering sub-items.
 * @param array|null  $section_data Section data when filtering sub-items.
 *
 * @return string
 */
function aiddata_learnpress_profile_tab_title( $title, $tab_key, $section_key = null, $section_data = null ) {
    if ( 'profile' !== $tab_key ) {
        return $title;
    }

    if ( null !== $section_key ) {
        return $title;
    }

    return __( 'My Profile', 'twentytwentyfour' );
}
add_filter( 'learn_press_profile_profile_tab_title', 'aiddata_learnpress_profile_tab_title', 10, 4 );

/**
 * Remove comments from LearnPress single course pages.
 *
 * This removes the default comment template that LearnPress adds to course pages.
 * Comments are not needed for our training hub implementation.
 */
function aiddata_remove_learnpress_comments() {
	// Remove the course comment template action
	remove_action( 
		'learn-press/course-content-summary', 
		LearnPress::instance()->template( 'course' )->func( 'course_comment_template' ), 
		75 
	);
}
add_action( 'init', 'aiddata_remove_learnpress_comments', 20 );

/**
 * Remove course meta-primary section from LearnPress single course pages.
 *
 * This removes the default course metadata (instructor, category) that appears
 * at the top of course pages. We use custom metadata display in our theme.
 */
function aiddata_remove_learnpress_meta_primary() {
	// Remove the course meta-primary template action
	remove_action( 
		'learn-press/course-content-summary', 
		LearnPress::instance()->template( 'course' )->callback( 'single-course/meta-primary' ), 
		10 
	);
}
add_action( 'init', 'aiddata_remove_learnpress_meta_primary', 20 );

/**
 * Remove course title from LearnPress content area.
 *
 * The course title is already displayed in the hero section of our custom theme,
 * so we don't need it to appear again in the content area.
 */
function aiddata_remove_learnpress_title() {
	// Remove the course title template action
	remove_action(
		'learn-press/course-content-summary',
		LearnPress::instance()->template( 'course' )->callback( 'single-course/title' ),
		10
	);
}
add_action( 'init', 'aiddata_remove_learnpress_title', 20 );

/**
 * Dequeue LearnPress curriculum scripts on single course pages.
 *
 * Our custom template renders curriculum server-side, so we don't need
 * the LearnPress AJAX lazy-loading scripts which cause duplicate content.
 */
function aiddata_dequeue_learnpress_curriculum_scripts() {
	if ( is_singular( 'lp_course' ) ) {
		wp_dequeue_script( 'lp-single-curriculum' );
		wp_deregister_script( 'lp-single-curriculum' );
	}
}
add_action( 'wp_enqueue_scripts', 'aiddata_dequeue_learnpress_curriculum_scripts', 100 );

/**
 * Enqueue custom LearnPress styles.
 *
 * Loads with priority 999 to ensure it loads AFTER LearnPress core CSS.
 */
function aiddata_enqueue_learnpress_custom_styles()
{
    wp_enqueue_style(
        'learn-press-custom',
        get_template_directory_uri() . '/css/learn-press-custom.css',
        array('learnpress'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'aiddata_enqueue_learnpress_custom_styles', 999);

/**
 * Replace "Failed" with "Try again" in LearnPress messages.
 */
function aiddata_replace_failed_text($translated_text, $text, $domain)
{
    if ($domain === 'learnpress' || $domain === 'learnpress-v4') {
        if (stripos($translated_text, 'Failed') !== false) {
            $translated_text = str_ireplace('Failed', 'Try again', $translated_text);
        }
    }
    return $translated_text;
}
add_filter('gettext', 'aiddata_replace_failed_text', 20, 3);
add_filter('ngettext', 'aiddata_replace_failed_text', 20, 3);

// Add custom meta box to course post type
add_action('add_meta_boxes', 'aiddata_learning_objectives_meta_box');

function aiddata_learning_objectives_meta_box() {
    add_meta_box(
        'aiddata_learning_objectives',
        'Learning Objectives (What You\'ll Learn)',
        'aiddata_learning_objectives_callback',
        'lp_course',
        'normal',
        'high'
    );
}

// Meta box HTML
function aiddata_learning_objectives_callback($post) {
    wp_nonce_field('aiddata_learning_objectives_nonce', 'aiddata_learning_objectives_nonce');
    
    // Get saved objectives
    $objectives = get_post_meta($post->ID, '_aiddata_learning_objectives', true);
    
    if (!is_array($objectives)) {
        $objectives = array();
    }
    
    // Ensure we have at least 4 empty slots
    while (count($objectives) < 4) {
        $objectives[] = array('title' => '', 'description' => '');
    }
    ?>
    
    <div id="learning-objectives-container" style="margin: 20px 0;">
        <p style="margin-bottom: 15px; color: #666;">
            <strong>Instructions:</strong> Enter up to 4 learning objectives. Each will display as a card with an icon in the "What You'll Learn" section.
        </p>
        
        <div id="objectives-list">
            <?php foreach ($objectives as $index => $objective) : ?>
                <div class="objective-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #004E38;">Objective <?php echo ($index + 1); ?></h4>
                    
                    <p style="margin: 0 0 5px 0;">
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Title:</label>
                        <input 
                            type="text" 
                            name="aiddata_objectives[<?php echo $index; ?>][title]" 
                            value="<?php echo esc_attr($objective['title']); ?>"
                            placeholder="e.g., Core Concepts"
                            style="width: 100%; padding: 8px;"
                        />
                    </p>
                    
                    <p style="margin: 10px 0 0 0;">
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Description:</label>
                        <textarea 
                            name="aiddata_objectives[<?php echo $index; ?>][description]" 
                            rows="3"
                            placeholder="e.g., Master the fundamental concepts and principles..."
                            style="width: 100%; padding: 8px;"
                        ><?php echo esc_textarea($objective['description']); ?></textarea>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <p style="margin-top: 15px; padding: 10px; background: #e7f5ff; border-left: 4px solid #004E38;">
            <strong>Note:</strong> Leave fields empty to use default placeholder objectives. Only filled objectives will be displayed.
        </p>
    </div>
    
    <?php
}

// Save meta box data
add_action('save_post', 'aiddata_save_learning_objectives');

function aiddata_save_learning_objectives($post_id) {
    // Check nonce
    if (!isset($_POST['aiddata_learning_objectives_nonce']) || 
        !wp_verify_nonce($_POST['aiddata_learning_objectives_nonce'], 'aiddata_learning_objectives_nonce')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save objectives
    if (isset($_POST['aiddata_objectives'])) {
        $objectives = array();
        
        foreach ($_POST['aiddata_objectives'] as $objective) {
            $title = sanitize_text_field($objective['title']);
            $description = sanitize_textarea_field($objective['description']);
            
            // Only save if at least title or description is filled
            if (!empty($title) || !empty($description)) {
                $objectives[] = array(
                    'title' => $title,
                    'description' => $description
                );
            }
        }
        
        update_post_meta($post_id, '_aiddata_learning_objectives', $objectives);
    }
}

/**
 * Add custom favicon to all pages
 */
function aiddata_add_favicon() {
    $favicon_url = get_template_directory_uri() . '/assets/images/aiddata_icon.ico';
    echo '<link rel="icon" type="image/png" href="' . esc_url($favicon_url) . '">' . "\n";
    echo '<link rel="shortcut icon" type="image/png" href="' . esc_url($favicon_url) . '">' . "\n";
}
add_action('wp_head', 'aiddata_add_favicon');
add_action('admin_head', 'aiddata_add_favicon');
add_action('login_head', 'aiddata_add_favicon');


