<?php
/**
 * Step-by-step WordPress loading test
 * This will help identify where WordPress is failing
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>WordPress Load Test</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .step { padding: 10px; margin: 5px 0; background: white; border-left: 4px solid #ccc; }
        .success { border-left-color: green; }
        .error { border-left-color: red; background: #ffe0e0; }
        .warning { border-left-color: orange; background: #fff3cd; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>WordPress Loading Test</h1>

<?php

function step($message, $status = 'success') {
    $class = $status === 'error' ? 'step error' : ($status === 'warning' ? 'step warning' : 'step success');
    echo "<div class='$class'>$message</div>";
    flush();
}

// Step 1: Check wp-config.php
step("Step 1: Checking wp-config.php...");
if (!file_exists(__DIR__ . '/wp-config.php')) {
    step("❌ wp-config.php not found!", 'error');
    exit;
}
step("✓ wp-config.php exists");

// Step 2: Load wp-config.php
step("Step 2: Loading wp-config.php...");
try {
    define('WP_USE_THEMES', false);
    require_once(__DIR__ . '/wp-config.php');
    step("✓ wp-config.php loaded successfully");
} catch (Throwable $e) {
    step("❌ Error loading wp-config.php: " . $e->getMessage(), 'error');
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

// Step 3: Check database constants
step("Step 3: Checking database configuration...");
echo "<div class='step success'>";
echo "Database Name: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "<br>";
echo "Database User: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "<br>";
echo "Database Host: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "<br>";
echo "WordPress URL: " . (defined('WP_SITEURL') ? WP_SITEURL : 'NOT DEFINED') . "<br>";
echo "Home URL: " . (defined('WP_HOME') ? WP_HOME : 'NOT DEFINED') . "<br>";
echo "</div>";

// Step 4: Test database connection
step("Step 4: Testing database connection...");
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        step("❌ Database connection failed: " . $mysqli->connect_error, 'error');
        exit;
    }
    step("✓ Database connection successful");

    // Check if tables exist
    $result = $mysqli->query("SHOW TABLES LIKE 'wp_%'");
    $table_count = $result->num_rows;
    step("✓ Found $table_count WordPress tables in database");

    if ($table_count === 0) {
        step("⚠ WordPress is not installed yet!", 'warning');
        echo "<div class='step warning'>";
        echo "<p><a href='https://aiddata-training-hub-test-production.up.railway.app/wp-admin/install.php'>Click here to install WordPress</a></p>";
        echo "</div>";
        exit;
    }

    // Check URLs in database
    step("Step 5: Checking URLs in wp_options...");
    $url_result = $mysqli->query("SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home')");
    if ($url_result && $url_result->num_rows > 0) {
        echo "<div class='step success'>";
        echo "<p>URLs in database:</p>";
        while ($row = $url_result->fetch_assoc()) {
            echo htmlspecialchars($row['option_name']) . ": <strong>" . htmlspecialchars($row['option_value']) . "</strong><br>";
        }
        echo "</div>";
    } else {
        step("⚠ No URLs found in wp_options table", 'warning');
    }

    $mysqli->close();
} catch (Throwable $e) {
    step("❌ Database error: " . $e->getMessage(), 'error');
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

// Step 6: Try to load wp-load.php
step("Step 6: Attempting to load WordPress (wp-load.php)...");
try {
    ob_start();
    require_once(__DIR__ . '/wp-load.php');
    $output = ob_get_clean();

    if (!empty($output)) {
        step("⚠ WordPress generated output during load (may indicate an issue):", 'warning');
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    } else {
        step("✓ wp-load.php loaded without output");
    }

    step("✓ WordPress loaded successfully!");

} catch (Throwable $e) {
    $output = ob_get_clean();
    step("❌ Fatal error loading WordPress: " . $e->getMessage(), 'error');
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    if (!empty($output)) {
        echo "<div class='step error'><p>Output before error:</p><pre>" . htmlspecialchars($output) . "</pre></div>";
    }
    exit;
}

// Step 7: Check WordPress functions
step("Step 7: Checking WordPress is fully loaded...");
if (function_exists('wp_get_current_user')) {
    step("✓ WordPress functions available");

    echo "<div class='step success'>";
    echo "<p><strong>WordPress Info:</strong></p>";
    echo "WordPress Version: " . (defined('WP_VERSION') ? WP_VERSION : 'unknown') . "<br>";
    echo "Site URL: " . site_url() . "<br>";
    echo "Home URL: " . home_url() . "<br>";
    echo "Admin URL: " . admin_url() . "<br>";
    echo "</div>";

    // Test that we can query posts
    step("Step 8: Testing WordPress database query...");
    try {
        $posts = get_posts(['numberposts' => 1]);
        step("✓ WordPress database queries working (found " . count($posts) . " post)");
    } catch (Throwable $e) {
        step("❌ Error querying posts: " . $e->getMessage(), 'error');
    }

} else {
    step("❌ WordPress functions not available!", 'error');
}

?>

<hr>
<h2>Conclusion</h2>
<div class="step success">
    <p>✓ WordPress appears to be loading correctly in this test script!</p>
    <p>If WordPress loads here but not on the homepage, the issue might be:</p>
    <ul>
        <li>A theme issue (theme files missing or corrupt)</li>
        <li>A plugin causing problems</li>
        <li>URL/redirect issues</li>
    </ul>
    <p><strong>Next steps:</strong></p>
    <ul>
        <li><a href="/">Try the homepage</a></li>
        <li><a href="/wp-admin/">Try the admin area</a></li>
    </ul>
</div>

</body>
</html>
