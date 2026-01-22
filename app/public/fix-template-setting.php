<?php
/**
 * Emergency Fix - Reset Template to Classic
 */

define('WP_USE_THEMES', false);
require('./wp-load.php');

$tutorial_id = 227;

echo "<!DOCTYPE html><html><head><title>Fix Template Setting</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".error{background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".info{background:#d1ecf1;color:#0c5460;padding:15px;border-radius:5px;margin:10px 0;}";
echo "code{background:#f4f4f4;padding:2px 6px;border-radius:3px;}";
echo "</style></head><body>";

echo "<h1>🔧 Fix Template Setting</h1>";

// Check current setting
$current_template = get_post_meta($tutorial_id, '_use_page_builder_template', true);

echo "<div class='info'>";
echo "<h2>Current Setting</h2>";
echo "<strong>Tutorial ID:</strong> $tutorial_id<br>";
echo "<strong>Current Template Meta:</strong> ";
if (empty($current_template)) {
    echo "<span style='color:green;'>NOT SET (using classic template)</span>";
} else {
    echo "<span style='color:orange;'>$current_template (using page builder - CAUSING ISSUES)</span>";
}
echo "</div>";

// Fix it
if (!empty($current_template)) {
    echo "<div class='info'>";
    echo "<h2>Applying Fix...</h2>";
    
    $result = delete_post_meta($tutorial_id, '_use_page_builder_template');
    
    if ($result) {
        echo "<div class='success'>";
        echo "<h3>✓ Fixed!</h3>";
        echo "<p>The template setting has been removed. The tutorial will now use the classic template.</p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>✗ Fix Failed</h3>";
        echo "<p>Could not remove the template setting. Try manually in the database.</p>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<h3>✓ Already Fixed</h3>";
    echo "<p>The tutorial is already set to use the classic template.</p>";
    echo "</div>";
}

// Verify
$new_template = get_post_meta($tutorial_id, '_use_page_builder_template', true);
echo "<div class='info'>";
echo "<h2>After Fix</h2>";
echo "<strong>Template Meta:</strong> ";
if (empty($new_template)) {
    echo "<span style='color:green;'>NOT SET (classic template will be used)</span>";
} else {
    echo "<span style='color:red;'>$new_template (still set - something went wrong)</span>";
}
echo "</div>";

echo "<div class='success'>";
echo "<h2>✓ Next Steps</h2>";
echo "<p><strong>Try accessing the tutorial now:</strong></p>";
echo "<p><a href='" . get_permalink($tutorial_id) . "' target='_blank' style='display:inline-block;background:#004E38;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;font-size:16px;'>View Tutorial</a></p>";
echo "<p style='margin-top:20px;'><small>URL: " . get_permalink($tutorial_id) . "</small></p>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>📝 Note</h2>";
echo "<p>The classic template works perfectly fine. The page builder template has a bug that needs more investigation.</p>";
echo "<p>You can continue using the classic template for now, and we can fix the page builder template later.</p>";
echo "</div>";

echo "</body></html>";
?>

