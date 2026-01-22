<?php
// Simplest possible test
define('WP_USE_THEMES', false);
require('./wp-load.php');

$tutorial_id = 227;
$post = get_post($tutorial_id);

echo "Tutorial ID: " . $tutorial_id . "<br>";
echo "Post exists: " . ($post ? "YES" : "NO") . "<br>";
echo "Post title: " . ($post ? $post->post_title : "N/A") . "<br>";
echo "Post status: " . ($post ? $post->post_status : "N/A") . "<br>";
echo "Template meta: " . get_post_meta($tutorial_id, '_use_page_builder_template', true) . "<br>";
echo "Permalink: " . get_permalink($tutorial_id) . "<br>";
echo "<br>";
echo "Tutorial class exists: " . (class_exists('AidData_LMS_Tutorial') ? "YES" : "NO") . "<br>";

if (class_exists('AidData_LMS_Tutorial')) {
    $tutorial = new AidData_LMS_Tutorial($tutorial_id);
    echo "Tutorial loaded: " . ($tutorial->get_id() ? "YES" : "NO") . "<br>";
    echo "Tutorial name: " . $tutorial->get_name() . "<br>";
    $steps = $tutorial->get_steps();
    echo "Steps count: " . count($steps) . "<br>";
}
?>

