<?php
/**
 * Check if wp-admin files exist
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check WP-Admin Files</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>WP-Admin Files Check</h1>

<?php

echo "<h2>Checking wp-admin directory...</h2>";

$wp_admin_path = __DIR__ . '/wp-admin';

if (file_exists($wp_admin_path)) {
    echo "<div class='success'>✓ wp-admin directory exists</div>";

    // Check key files
    $key_files = [
        'index.php',
        'admin.php',
        'menu.php',
        'includes/admin.php'
    ];

    echo "<h3>Key Admin Files:</h3>";
    foreach ($key_files as $file) {
        $full_path = $wp_admin_path . '/' . $file;
        if (file_exists($full_path)) {
            echo "<div class='success'>✓ $file exists</div>";
        } else {
            echo "<div class='error'>✗ $file MISSING!</div>";
        }
    }

    // List contents
    echo "<h3>wp-admin Directory Contents:</h3>";
    echo "<pre>";
    $contents = scandir($wp_admin_path);
    foreach ($contents as $item) {
        if ($item !== '.' && $item !== '..') {
            $full_path = $wp_admin_path . '/' . $item;
            $type = is_dir($full_path) ? '[DIR]' : '[FILE]';
            echo "$type $item\n";
        }
    }
    echo "</pre>";

} else {
    echo "<div class='error'>✗ wp-admin directory NOT FOUND!</div>";
}

// Try to load WordPress and see what happens
echo "<h2>Testing WordPress Load with wp-admin Access</h2>";

try {
    define('WP_USE_THEMES', false);
    require_once(__DIR__ . '/wp-load.php');

    echo "<div class='success'>✓ WordPress loaded</div>";

    // Check if user is logged in
    if (is_user_logged_in()) {
        echo "<div class='info'>User is logged in as: " . wp_get_current_user()->user_login . "</div>";
    } else {
        echo "<div class='info'>User is NOT logged in</div>";
    }

    // Test admin_url()
    echo "<div class='info'>admin_url() returns: " . admin_url() . "</div>";
    echo "<div class='info'>admin_url('index.php') returns: " . admin_url('index.php') . "</div>";

    // Check if we can access admin constants
    if (defined('WP_ADMIN')) {
        echo "<div class='info'>WP_ADMIN is defined as: " . (WP_ADMIN ? 'true' : 'false') . "</div>";
    } else {
        echo "<div class='info'>WP_ADMIN is NOT defined</div>";
    }

} catch (Throwable $e) {
    echo "<div class='error'>Error loading WordPress: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Check nginx rewrite rules
echo "<h2>Nginx Configuration</h2>";
echo "<div class='info'>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "<br>";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'not set') . "<br>";
echo "</div>";

?>

<hr>
<h2>Quick Links</h2>
<div class="info">
    <p><a href="/wp-admin/index.php" target="_blank">Try: /wp-admin/index.php</a></p>
    <p><a href="/wp-login.php" target="_blank">Try: /wp-login.php</a></p>
    <p><a href="/" target="_blank">Homepage</a></p>
</div>

</body>
</html>
