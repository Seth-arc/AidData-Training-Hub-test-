<?php
/**
 * Helper script to set up a WordPress page for a tutorial
 * This creates a page that displays the tutorial using the Page Builder template
 */

define('WP_USE_THEMES', false);
require('./wp-load.php');

echo "<!DOCTYPE html><html><head><title>Tutorial Page Setup</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".error{background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".info{background:#d1ecf1;color:#0c5460;padding:15px;border-radius:5px;margin:10px 0;}";
echo "code{background:#f4f4f4;padding:2px 6px;border-radius:3px;font-family:monospace;}";
echo "</style></head><body>";

echo "<h1>Tutorial Page Setup Helper</h1>";

// Get the tutorial
$tutorial_slug = 'global-development-finance';
$tutorial_post = get_page_by_path($tutorial_slug, OBJECT, 'aiddata_tutorial');

if (!$tutorial_post) {
    echo "<div class='error'>Tutorial not found with slug: $tutorial_slug</div>";
    echo "</body></html>";
    exit;
}

$tutorial_id = $tutorial_post->ID;

echo "<div class='success'>";
echo "<h2>✓ Tutorial Found</h2>";
echo "<strong>Tutorial ID:</strong> $tutorial_id<br>";
echo "<strong>Title:</strong> " . esc_html($tutorial_post->post_title) . "<br>";
echo "<strong>Status:</strong> " . esc_html($tutorial_post->post_status) . "<br>";
echo "<strong>Direct URL:</strong> <a href='" . get_permalink($tutorial_id) . "' target='_blank'>" . get_permalink($tutorial_id) . "</a>";
echo "</div>";

// Check if page already exists
$page_slug = 'tutorial-page-' . $tutorial_slug;
$existing_page = get_page_by_path($page_slug, OBJECT, 'page');

if ($existing_page) {
    echo "<div class='info'>";
    echo "<h2>Page Already Exists</h2>";
    echo "<strong>Page ID:</strong> " . $existing_page->ID . "<br>";
    echo "<strong>Page URL:</strong> <a href='" . get_permalink($existing_page->ID) . "' target='_blank'>" . get_permalink($existing_page->ID) . "</a><br>";
    echo "<strong>Tutorial ID in custom field:</strong> " . get_post_meta($existing_page->ID, '_tutorial_page_id', true);
    echo "</div>";
} else {
    // Create a new page
    $page_data = array(
        'post_title' => $tutorial_post->post_title . ' (Page)',
        'post_content' => '',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_name' => $page_slug
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id && !is_wp_error($page_id)) {
        // Add the tutorial ID as custom field
        update_post_meta($page_id, '_tutorial_page_id', $tutorial_id);
        
        // Set the page template
        update_post_meta($page_id, '_wp_page_template', 'template-tutorial-page-builder.php');
        
        echo "<div class='success'>";
        echo "<h2>✓ Page Created Successfully!</h2>";
        echo "<strong>Page ID:</strong> $page_id<br>";
        echo "<strong>Page URL:</strong> <a href='" . get_permalink($page_id) . "' target='_blank'>" . get_permalink($page_id) . "</a><br>";
        echo "<strong>Tutorial ID stored:</strong> $tutorial_id";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<h3>Next Steps:</h3>";
        echo "<ol>";
        echo "<li>Visit the <a href='" . get_permalink($page_id) . "' target='_blank'>new page</a> to see the tutorial</li>";
        echo "<li>Or edit the page in WordPress admin to customize it</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div class='error'>Failed to create page</div>";
    }
}

echo "<hr>";
echo "<h2>Manual Setup Instructions</h2>";
echo "<div class='info'>";
echo "<p>To manually set up a page for this tutorial:</p>";
echo "<ol>";
echo "<li>Go to <strong>Pages > Add New</strong> in WordPress admin</li>";
echo "<li>Give it any title you want</li>";
echo "<li>In the <strong>Page Attributes</strong> box, select template: <code>Tutorial Page Builder</code></li>";
echo "<li>Add a <strong>Custom Field</strong>:";
echo "<ul style='margin-top:10px;'>";
echo "<li><strong>Name:</strong> <code>_tutorial_page_id</code></li>";
echo "<li><strong>Value:</strong> <code>$tutorial_id</code></li>";
echo "</ul>";
echo "</li>";
echo "<li>Publish the page</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";

