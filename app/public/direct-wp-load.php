<?php
/**
 * Load WordPress with error suppression disabled
 * This will show the REAL error, not WordPress's custom error page
 */

// Maximum error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

// Don't let WordPress handle errors
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', false);  // Disable log to see errors on screen

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct WordPress Load</title>
    <style>
        body { font-family: monospace; background: #f5f5f5; padding: 20px; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Direct WordPress Load Test</h1>
    <div class="info">Loading WordPress with all error suppression disabled...</div>

<?php

echo "<div class='info'>Step 1: Loading wp-load.php...</div>\n";
flush();

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

echo "<div class='info'>✓ WordPress loaded successfully!</div>\n";
echo "<div class='info'>WordPress Version: " . $wp_version . "</div>\n";
echo "<div class='info'>Site URL: " . get_option('siteurl') . "</div>\n";
echo "<div class='info'>Home URL: " . get_option('home') . "</div>\n";

// Test database query
echo "<div class='info'>Testing database query...</div>\n";
$test = $wpdb->get_var("SELECT 1");
echo "<div class='info'>✓ Database query result: $test</div>\n";

// Check if WordPress is fully functional
echo "<div class='info'>Checking WordPress functions...</div>\n";
echo "<div class='info'>is_admin(): " . (is_admin() ? 'true' : 'false') . "</div>\n";
echo "<div class='info'>Current user ID: " . get_current_user_id() . "</div>\n";

?>

    <hr>
    <div class="info">
        <strong>Success!</strong> WordPress loaded without errors.
        <br>If you see this, the database connection is working.
        <br>The issue with the homepage must be something else (theme, template, etc.)
    </div>

</body>
</html>
