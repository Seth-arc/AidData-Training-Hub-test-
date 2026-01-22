<?php
/**
 * Register Interview Custom Post Type
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Four
 * @since 1.0.0
 */

// Register Interview Custom Post Type
function register_interview_post_type() {
    $labels = array(
        'name'                  => _x('Interviews', 'Post Type General Name', 'twentytwentyfour'),
        'singular_name'         => _x('Interview', 'Post Type Singular Name', 'twentytwentyfour'),
        'menu_name'             => __('Interviews', 'twentytwentyfour'),
        'name_admin_bar'        => __('Interview', 'twentytwentyfour'),
        'archives'              => __('Interview Archives', 'twentytwentyfour'),
        'attributes'            => __('Interview Attributes', 'twentytwentyfour'),
        'parent_item_colon'     => __('Parent Interview:', 'twentytwentyfour'),
        'all_items'             => __('All Interviews', 'twentytwentyfour'),
        'add_new_item'          => __('Add New Interview', 'twentytwentyfour'),
        'add_new'               => __('Add New', 'twentytwentyfour'),
        'new_item'              => __('New Interview', 'twentytwentyfour'),
        'edit_item'             => __('Edit Interview', 'twentytwentyfour'),
        'update_item'           => __('Update Interview', 'twentytwentyfour'),
        'view_item'             => __('View Interview', 'twentytwentyfour'),
        'view_items'            => __('View Interviews', 'twentytwentyfour'),
        'search_items'          => __('Search Interview', 'twentytwentyfour'),
        'not_found'             => __('Not found', 'twentytwentyfour'),
        'not_found_in_trash'    => __('Not found in Trash', 'twentytwentyfour'),
        'featured_image'        => __('Featured Image', 'twentytwentyfour'),
        'set_featured_image'    => __('Set featured image', 'twentytwentyfour'),
        'remove_featured_image' => __('Remove featured image', 'twentytwentyfour'),
        'use_featured_image'    => __('Use as featured image', 'twentytwentyfour'),
        'insert_into_item'      => __('Insert into interview', 'twentytwentyfour'),
        'uploaded_to_this_item' => __('Uploaded to this interview', 'twentytwentyfour'),
        'items_list'            => __('Interviews list', 'twentytwentyfour'),
        'items_list_navigation' => __('Interviews list navigation', 'twentytwentyfour'),
        'filter_items_list'     => __('Filter interviews list', 'twentytwentyfour'),
    );
    
    $args = array(
        'label'                 => __('Interview', 'twentytwentyfour'),
        'description'           => __('Expert interviews and video content', 'twentytwentyfour'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions', 'page-attributes'),
        'taxonomies'            => array('interview_category', 'interview_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-video-alt3',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'interviews'),
    );
    
    register_post_type('aiddata_interview', $args);
}
add_action('init', 'register_interview_post_type', 0);

// Register Interview Categories Taxonomy
function register_interview_category_taxonomy() {
    $labels = array(
        'name'                       => _x('Interview Categories', 'Taxonomy General Name', 'twentytwentyfour'),
        'singular_name'              => _x('Interview Category', 'Taxonomy Singular Name', 'twentytwentyfour'),
        'menu_name'                  => __('Categories', 'twentytwentyfour'),
        'all_items'                  => __('All Categories', 'twentytwentyfour'),
        'parent_item'                => __('Parent Category', 'twentytwentyfour'),
        'parent_item_colon'          => __('Parent Category:', 'twentytwentyfour'),
        'new_item_name'              => __('New Category Name', 'twentytwentyfour'),
        'add_new_item'               => __('Add New Category', 'twentytwentyfour'),
        'edit_item'                  => __('Edit Category', 'twentytwentyfour'),
        'update_item'                => __('Update Category', 'twentytwentyfour'),
        'view_item'                  => __('View Category', 'twentytwentyfour'),
        'separate_items_with_commas' => __('Separate categories with commas', 'twentytwentyfour'),
        'add_or_remove_items'        => __('Add or remove categories', 'twentytwentyfour'),
        'choose_from_most_used'      => __('Choose from the most used', 'twentytwentyfour'),
        'popular_items'              => __('Popular Categories', 'twentytwentyfour'),
        'search_items'               => __('Search Categories', 'twentytwentyfour'),
        'not_found'                  => __('Not Found', 'twentytwentyfour'),
        'no_terms'                   => __('No categories', 'twentytwentyfour'),
        'items_list'                 => __('Categories list', 'twentytwentyfour'),
        'items_list_navigation'      => __('Categories list navigation', 'twentytwentyfour'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );
    
    register_taxonomy('interview_category', array('aiddata_interview'), $args);
}
add_action('init', 'register_interview_category_taxonomy', 0);

// Register Interview Tags Taxonomy
function register_interview_tag_taxonomy() {
    $labels = array(
        'name'                       => _x('Interview Tags', 'Taxonomy General Name', 'twentytwentyfour'),
        'singular_name'              => _x('Interview Tag', 'Taxonomy Singular Name', 'twentytwentyfour'),
        'menu_name'                  => __('Tags', 'twentytwentyfour'),
        'all_items'                  => __('All Tags', 'twentytwentyfour'),
        'parent_item'                => __('Parent Tag', 'twentytwentyfour'),
        'parent_item_colon'          => __('Parent Tag:', 'twentytwentyfour'),
        'new_item_name'              => __('New Tag Name', 'twentytwentyfour'),
        'add_new_item'               => __('Add New Tag', 'twentytwentyfour'),
        'edit_item'                  => __('Edit Tag', 'twentytwentyfour'),
        'update_item'                => __('Update Tag', 'twentytwentyfour'),
        'view_item'                  => __('View Tag', 'twentytwentyfour'),
        'separate_items_with_commas' => __('Separate tags with commas', 'twentytwentyfour'),
        'add_or_remove_items'        => __('Add or remove tags', 'twentytwentyfour'),
        'choose_from_most_used'      => __('Choose from the most used', 'twentytwentyfour'),
        'popular_items'              => __('Popular Tags', 'twentytwentyfour'),
        'search_items'               => __('Search Tags', 'twentytwentyfour'),
        'not_found'                  => __('Not Found', 'twentytwentyfour'),
        'no_terms'                   => __('No tags', 'twentytwentyfour'),
        'items_list'                 => __('Tags list', 'twentytwentyfour'),
        'items_list_navigation'      => __('Tags list navigation', 'twentytwentyfour'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );
    
    register_taxonomy('interview_tag', array('aiddata_interview'), $args);
}
add_action('init', 'register_interview_tag_taxonomy', 0);

// Add Custom Meta Boxes for Interview Data
function add_interview_meta_boxes() {
    add_meta_box(
        'interview_details',
        __('Interview Details', 'twentytwentyfour'),
        'render_interview_details_meta_box',
        'aiddata_interview',
        'normal',
        'high'
    );
    
    add_meta_box(
        'interview_experts',
        __('Interview Experts', 'twentytwentyfour'),
        'render_interview_experts_meta_box',
        'aiddata_interview',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_interview_meta_boxes');

// Render Interview Details Meta Box
function render_interview_details_meta_box($post) {
    wp_nonce_field('interview_details_nonce', 'interview_details_nonce');
    
    $video_url = get_post_meta($post->ID, '_interview_video_url', true);
    $duration = get_post_meta($post->ID, '_interview_duration', true);
    $recorded_date = get_post_meta($post->ID, '_interview_recorded_date', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="interview_video_url"><?php _e('Video URL', 'twentytwentyfour'); ?></label></th>
            <td>
                <input type="url" id="interview_video_url" name="interview_video_url" value="<?php echo esc_attr($video_url); ?>" class="large-text" />
                <p class="description"><?php _e('Enter the video URL (YouTube, Vimeo, or direct video file)', 'twentytwentyfour'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="interview_duration"><?php _e('Duration', 'twentytwentyfour'); ?></label></th>
            <td>
                <input type="text" id="interview_duration" name="interview_duration" value="<?php echo esc_attr($duration); ?>" class="regular-text" placeholder="28:15" />
                <p class="description"><?php _e('Enter the video duration (e.g., 28:15)', 'twentytwentyfour'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="interview_recorded_date"><?php _e('Recorded Date', 'twentytwentyfour'); ?></label></th>
            <td>
                <input type="text" id="interview_recorded_date" name="interview_recorded_date" value="<?php echo esc_attr($recorded_date); ?>" class="regular-text" placeholder="Oct 2025" />
                <p class="description"><?php _e('Enter the recording date (e.g., Oct 2025)', 'twentytwentyfour'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

// Render Interview Experts Meta Box
function render_interview_experts_meta_box($post) {
    wp_nonce_field('interview_experts_nonce', 'interview_experts_nonce');
    
    $experts = get_post_meta($post->ID, '_interview_experts', true);
    if (empty($experts)) {
        $experts = array(array('name' => '', 'title' => '', 'bio' => '', 'photo' => ''));
    }
    ?>
    <div id="interview-experts-container">
        <?php foreach ($experts as $index => $expert): ?>
            <div class="interview-expert-item" data-index="<?php echo $index; ?>" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                <h4><?php _e('Expert', 'twentytwentyfour'); ?> #<?php echo $index + 1; ?></h4>
                <p>
                    <label><?php _e('Name', 'twentytwentyfour'); ?>:</label><br>
                    <input type="text" name="interview_experts[<?php echo $index; ?>][name]" value="<?php echo esc_attr($expert['name']); ?>" class="large-text" />
                </p>
                <p>
                    <label><?php _e('Title/Position', 'twentytwentyfour'); ?>:</label><br>
                    <input type="text" name="interview_experts[<?php echo $index; ?>][title]" value="<?php echo esc_attr($expert['title']); ?>" class="large-text" />
                </p>
                <p>
                    <label><?php _e('Bio', 'twentytwentyfour'); ?>:</label><br>
                    <textarea name="interview_experts[<?php echo $index; ?>][bio]" rows="3" class="large-text"><?php echo esc_textarea($expert['bio']); ?></textarea>
                </p>
                <p>
                    <label><?php _e('Photo URL', 'twentytwentyfour'); ?>:</label><br>
                    <input type="url" name="interview_experts[<?php echo $index; ?>][photo]" value="<?php echo esc_attr($expert['photo']); ?>" class="large-text" />
                </p>
                <button type="button" class="button remove-expert" data-index="<?php echo $index; ?>"><?php _e('Remove Expert', 'twentytwentyfour'); ?></button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button button-primary" id="add-expert"><?php _e('Add Expert', 'twentytwentyfour'); ?></button>
    
    <script>
    jQuery(document).ready(function($) {
        var expertIndex = <?php echo count($experts); ?>;
        
        $('#add-expert').on('click', function() {
            var html = '<div class="interview-expert-item" data-index="' + expertIndex + '" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">' +
                '<h4><?php _e('Expert', 'twentytwentyfour'); ?> #' + (expertIndex + 1) + '</h4>' +
                '<p><label><?php _e('Name', 'twentytwentyfour'); ?>:</label><br>' +
                '<input type="text" name="interview_experts[' + expertIndex + '][name]" value="" class="large-text" /></p>' +
                '<p><label><?php _e('Title/Position', 'twentytwentyfour'); ?>:</label><br>' +
                '<input type="text" name="interview_experts[' + expertIndex + '][title]" value="" class="large-text" /></p>' +
                '<p><label><?php _e('Bio', 'twentytwentyfour'); ?>:</label><br>' +
                '<textarea name="interview_experts[' + expertIndex + '][bio]" rows="3" class="large-text"></textarea></p>' +
                '<p><label><?php _e('Photo URL', 'twentytwentyfour'); ?>:</label><br>' +
                '<input type="url" name="interview_experts[' + expertIndex + '][photo]" value="" class="large-text" /></p>' +
                '<button type="button" class="button remove-expert" data-index="' + expertIndex + '"><?php _e('Remove Expert', 'twentytwentyfour'); ?></button>' +
                '</div>';
            
            $('#interview-experts-container').append(html);
            expertIndex++;
        });
        
        $(document).on('click', '.remove-expert', function() {
            $(this).closest('.interview-expert-item').remove();
        });
    });
    </script>
    <?php
}

// Save Interview Meta Data
function save_interview_meta_data($post_id) {
    // Check nonces
    if (!isset($_POST['interview_details_nonce']) || !wp_verify_nonce($_POST['interview_details_nonce'], 'interview_details_nonce')) {
        return;
    }
    
    if (!isset($_POST['interview_experts_nonce']) || !wp_verify_nonce($_POST['interview_experts_nonce'], 'interview_experts_nonce')) {
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
    
    // Save video URL
    if (isset($_POST['interview_video_url'])) {
        update_post_meta($post_id, '_interview_video_url', sanitize_text_field($_POST['interview_video_url']));
    }
    
    // Save duration
    if (isset($_POST['interview_duration'])) {
        update_post_meta($post_id, '_interview_duration', sanitize_text_field($_POST['interview_duration']));
    }
    
    // Save recorded date
    if (isset($_POST['interview_recorded_date'])) {
        update_post_meta($post_id, '_interview_recorded_date', sanitize_text_field($_POST['interview_recorded_date']));
    }
    
    // Save experts
    if (isset($_POST['interview_experts']) && is_array($_POST['interview_experts'])) {
        $experts = array();
        foreach ($_POST['interview_experts'] as $expert) {
            if (!empty($expert['name'])) {
                $experts[] = array(
                    'name' => sanitize_text_field($expert['name']),
                    'title' => sanitize_text_field($expert['title']),
                    'bio' => sanitize_textarea_field($expert['bio']),
                    'photo' => esc_url_raw($expert['photo'])
                );
            }
        }
        update_post_meta($post_id, '_interview_experts', $experts);
    }
}
add_action('save_post_aiddata_interview', 'save_interview_meta_data');

// Flush rewrite rules on theme activation
function interview_rewrite_flush() {
    register_interview_post_type();
    register_interview_category_taxonomy();
    register_interview_tag_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'interview_rewrite_flush');

