<?php
/**
 * WordPress Admin Script to Create Sample Content
 * Place this in wp-admin folder and access via browser
 */

// Only allow access from admin
if (!is_admin() && !current_user_can('manage_options')) {
    wp_die('Access denied');
}

// Check if we should create sample content
if (isset($_GET['create_sample_content']) && $_GET['create_sample_content'] === '1') {
    
    // Verify nonce for security
    if (!wp_verify_nonce($_GET['_wpnonce'], 'create_sample_content')) {
        wp_die('Security check failed');
    }
    
    echo '<div class="wrap">';
    echo '<h1>Creating AidData LMS Sample Content</h1>';
    
    try {
        // Check if plugin is active
        if (!class_exists('AidData_LMS_Sample_Content')) {
            throw new Exception('AidData LMS plugin is not active or sample content class not found');
        }
        
        echo '<p>Creating categories...</p>';
        AidData_LMS_Sample_Content::create_categories();
        
        echo '<p>Creating courses and simulations...</p>';
        AidData_LMS_Sample_Content::create_all_content();
        
        echo '<p>Creating sample users...</p>';
        AidData_LMS_Sample_Content::create_sample_users();
        
        echo '<div class="notice notice-success"><p><strong>Success!</strong> Sample content has been created.</p></div>';
        
        // List created content
        $courses = get_posts(array(
            'post_type' => 'aiddata_course',
            'post_status' => 'publish',
            'numberposts' => -1
        ));
        
        $simulations = get_posts(array(
            'post_type' => 'aiddata_simulation',
            'post_status' => 'publish',
            'numberposts' => -1
        ));
        
        echo '<h2>Created Content:</h2>';
        echo '<h3>Courses (' . count($courses) . '):</h3><ul>';
        foreach ($courses as $course) {
            echo '<li><a href="' . get_edit_post_link($course->ID) . '">' . $course->post_title . '</a> (ID: ' . $course->ID . ')</li>';
        }
        echo '</ul>';
        
        echo '<h3>Simulations (' . count($simulations) . '):</h3><ul>';
        foreach ($simulations as $simulation) {
            echo '<li><a href="' . get_edit_post_link($simulation->ID) . '">' . $simulation->post_title . '</a> (ID: ' . $simulation->ID . ')</li>';
        }
        echo '</ul>';
        
    } catch (Exception $e) {
        echo '<div class="notice notice-error"><p><strong>Error:</strong> ' . $e->getMessage() . '</p></div>';
    }
    
    echo '</div>';
    return;
}

// Show the form
?>
<div class="wrap">
    <h1>AidData LMS Sample Content Creator</h1>
    
    <?php
    // Check if content already exists
    $existing_courses = get_posts(array(
        'post_type' => 'aiddata_course',
        'post_status' => 'publish',
        'numberposts' => 1
    ));
    
    $existing_simulations = get_posts(array(
        'post_type' => 'aiddata_simulation',
        'post_status' => 'publish', 
        'numberposts' => 1
    ));
    
    if (!empty($existing_courses) || !empty($existing_simulations)) {
        echo '<div class="notice notice-info"><p>Sample content already exists!</p></div>';
        
        $all_content = get_posts(array(
            'post_type' => array('aiddata_course', 'aiddata_simulation'),
            'post_status' => 'publish',
            'numberposts' => -1
        ));
        
        echo '<h2>Existing Content:</h2><ul>';
        foreach ($all_content as $content) {
            echo '<li><a href="' . get_edit_post_link($content->ID) . '">' . $content->post_title . '</a> (' . $content->post_type . ')</li>';
        }
        echo '</ul>';
    }
    ?>
    
    <p>This will create the 8 sample courses and simulations for the AidData LMS.</p>
    
    <p>
        <a href="<?php echo wp_nonce_url(add_query_arg('create_sample_content', '1'), 'create_sample_content'); ?>" 
           class="button button-primary button-large"
           onclick="return confirm('This will create sample content. Continue?')">
            Create Sample Content
        </a>
    </p>
</div>
