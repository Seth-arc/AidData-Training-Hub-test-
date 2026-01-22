<?php
define('WP_USE_THEMES', false);
require('./wp-load.php');

$tutorial_id = 227;

echo "<!DOCTYPE html><html><head><title>Tutorial Meta Check</title>";
echo "<style>body{font-family:monospace;padding:20px;} .meta{background:#f0f0f0;padding:10px;margin:10px 0;}</style>";
echo "</head><body>";

echo "<h1>Tutorial Meta Check</h1>";
echo "<p>Tutorial ID: $tutorial_id</p>";

$post = get_post($tutorial_id);
if (!$post) {
    echo "<p style='color:red;'>Tutorial not found!</p>";
    exit;
}

echo "<h2>Post Data</h2>";
echo "<div class='meta'>";
echo "Title: " . $post->post_title . "<br>";
echo "Status: " . $post->post_status . "<br>";
echo "Type: " . $post->post_type . "<br>";
echo "URL: " . get_permalink($tutorial_id) . "<br>";
echo "</div>";

echo "<h2>All Post Meta</h2>";
$all_meta = get_post_meta($tutorial_id);
echo "<div class='meta'>";
echo "<pre>";
print_r($all_meta);
echo "</pre>";
echo "</div>";

echo "<h2>Template Meta</h2>";
$template_meta = get_post_meta($tutorial_id, '_use_page_builder_template', true);
echo "<div class='meta'>";
echo "_use_page_builder_template: ";
if (empty($template_meta)) {
    echo "<strong style='color:orange;'>NOT SET (will use classic template)</strong>";
} else {
    echo "<strong style='color:green;'>$template_meta (will use page builder)</strong>";
}
echo "</div>";

echo "<h2>Steps</h2>";
$steps = get_post_meta($tutorial_id, '_tutorial_steps', true);
echo "<div class='meta'>";
if (empty($steps)) {
    echo "<strong style='color:red;'>NO STEPS FOUND!</strong>";
} else {
    echo "Steps count: " . count($steps) . "<br>";
    echo "<pre>";
    print_r($steps);
    echo "</pre>";
}
echo "</div>";

echo "</body></html>";

