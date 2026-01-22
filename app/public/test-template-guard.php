<?php
/**
 * Test Template Guard
 * 
 * This script tests if the template guard is working properly
 */

// Load WordPress
require_once('wp-load.php');

echo "<h1>Template Guard Test</h1>";

// Get the tutorial post
$tutorial_post = get_posts(array(
    'post_type' => 'aiddata_tutorial',
    'name' => 'global-development-finance',
    'posts_per_page' => 1
));

if (empty($tutorial_post)) {
    echo "<p style='color: red;'>Tutorial not found!</p>";
    exit;
}

$post = $tutorial_post[0];
setup_postdata($post);

echo "<p><strong>Tutorial Found:</strong> " . esc_html($post->post_title) . " (ID: " . $post->ID . ")</p>";

// Check template meta
$use_page_builder = get_post_meta($post->ID, '_use_page_builder_template', true);
echo "<p><strong>Use Page Builder Template:</strong> " . ($use_page_builder ? 'Yes (' . esc_html($use_page_builder) . ')' : 'No (using classic template)') . "</p>";

// Check if template guard constant is defined
echo "<p><strong>Template Guard Status:</strong> ";
if (defined('AIDDATA_SINGLE_TUTORIAL_LOADED')) {
    echo "<span style='color: orange;'>Already loaded (guard is working!)</span>";
} else {
    echo "<span style='color: green;'>Not loaded yet (ready to load)</span>";
}
echo "</p>";

echo "<hr>";
echo "<p><strong>Next Step:</strong> Try accessing the tutorial at: <a href='" . get_permalink($post->ID) . "' target='_blank'>" . get_permalink($post->ID) . "</a></p>";

wp_reset_postdata();

