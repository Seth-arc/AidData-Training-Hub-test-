<?php
/**
 * Trace WordPress initialization step-by-step
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>WordPress Initialization Trace</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .step { padding: 10px; margin: 5px 0; background: white; border-left: 4px solid green; }
        .error { border-left-color: red; background: #ffe0e0; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>WordPress Initialization Trace</h1>

<?php

function trace($msg) {
    echo "<div class='step'>$msg</div>";
    flush();
    ob_flush();
}

function trace_error($msg, $error = '') {
    echo "<div class='step error'>$msg</div>";
    if ($error) {
        echo "<pre>" . htmlspecialchars($error) . "</pre>";
    }
    flush();
    ob_flush();
}

// Step 1: Check file exists
trace("1. Checking if wp-config.php exists...");
if (!file_exists(__DIR__ . '/wp-config.php')) {
    trace_error("ERROR: wp-config.php not found!");
    exit;
}
trace("✓ wp-config.php found");

// Step 2: Read wp-config.php and prevent wp-settings.php from loading
trace("2. Reading wp-config.php (preventing wp-settings.php)...");

// Define ABSPATH early to prevent wp-settings.php from loading
define('ABSPATH', __DIR__ . '/');

// Read wp-config.php content
$wp_config_content = file_get_contents(__DIR__ . '/wp-config.php');

// Check if wp-settings.php is loaded
if (strpos($wp_config_content, 'wp-settings.php') !== false) {
    trace("⚠ wp-config.php loads wp-settings.php at the end");

    // We need to prevent this, so let's manually parse and execute wp-config
    trace("3. Manually parsing wp-config.php constants...");

    // Create a modified version that doesn't load wp-settings
    $modified_config = preg_replace(
        '/require_once.*wp-settings\.php.*;.*$/m',
        '// wp-settings.php loading prevented for testing',
        $wp_config_content
    );

    // Save to temporary file
    $temp_config = __DIR__ . '/wp-config-temp.php';
    file_put_contents($temp_config, $modified_config);

    try {
        require_once($temp_config);
        trace("✓ wp-config.php loaded without wp-settings.php");
        unlink($temp_config); // Clean up
    } catch (Throwable $e) {
        unlink($temp_config); // Clean up
        trace_error("ERROR loading wp-config.php: " . $e->getMessage(), $e->getTraceAsString());
        exit;
    }
}

// Step 3: Verify constants
trace("4. Verifying WordPress constants...");
$required_constants = ['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST', 'WP_DEBUG'];
$missing = [];
foreach ($required_constants as $const) {
    if (!defined($const)) {
        $missing[] = $const;
    }
}

if (!empty($missing)) {
    trace_error("ERROR: Missing constants: " . implode(', ', $missing));
} else {
    trace("✓ All required constants defined");
    echo "<div class='step'>";
    echo "DB_HOST: " . DB_HOST . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
    echo "WP_DEBUG: " . (WP_DEBUG ? 'true' : 'false') . "<br>";
    echo "</div>";
}

// Step 4: Test database connection
trace("5. Testing database connection...");
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        trace_error("ERROR: Database connection failed: " . $mysqli->connect_error);
        exit;
    }
    trace("✓ Database connection successful");
    $mysqli->close();
} catch (Throwable $e) {
    trace_error("ERROR: " . $e->getMessage(), $e->getTraceAsString());
    exit;
}

// Step 5: Try to load wp-includes files needed for wpdb
trace("6. Loading WordPress database class...");

if (!defined('WPINC')) {
    define('WPINC', 'wp-includes');
}

try {
    // Load wpdb class
    require_once(ABSPATH . WPINC . '/class-wpdb.php');
    trace("✓ wpdb class loaded");

    // Try to instantiate wpdb
    trace("7. Creating wpdb instance...");
    $wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

    if (!empty($wpdb->error)) {
        trace_error("ERROR: wpdb error: " . $wpdb->error);
    } else {
        trace("✓ wpdb instance created successfully");

        // Test a query
        trace("8. Testing database query...");
        $result = $wpdb->get_var("SELECT 1");
        if ($result === '1') {
            trace("✓ Database query successful");
        } else {
            trace_error("ERROR: Query failed or returned unexpected result");
        }
    }

} catch (Throwable $e) {
    trace_error("ERROR: " . $e->getMessage(), $e->getTraceAsString());
    exit;
}

// Step 6: Now try to load wp-settings.php
trace("9. Attempting to load wp-settings.php...");
try {
    // Check if file exists
    if (!file_exists(ABSPATH . 'wp-settings.php')) {
        trace_error("ERROR: wp-settings.php not found!");
        exit;
    }

    trace("⚠ About to load wp-settings.php - this is where things might fail...");

    ob_start();
    require_once(ABSPATH . 'wp-settings.php');
    $output = ob_get_clean();

    if (!empty($output)) {
        trace_error("⚠ wp-settings.php generated output:", htmlspecialchars($output));
    } else {
        trace("✓ wp-settings.php loaded successfully!");
    }

} catch (Throwable $e) {
    ob_get_clean();
    trace_error("ERROR loading wp-settings.php: " . $e->getMessage(), $e->getTraceAsString());
    exit;
}

// Step 7: Verify WordPress is loaded
trace("10. Verifying WordPress is fully loaded...");
if (function_exists('wp_get_current_user')) {
    trace("✓ WordPress functions available!");
    echo "<div class='step'>";
    echo "WordPress Version: " . (defined('WP_VERSION') ? WP_VERSION : 'unknown') . "<br>";
    echo "Site URL: " . (function_exists('site_url') ? site_url() : 'function not available') . "<br>";
    echo "</div>";
} else {
    trace_error("ERROR: WordPress functions not available");
}

?>

<hr>
<h2>Summary</h2>
<div class="step">
    <p><strong>If you see this message, WordPress initialization was successful!</strong></p>
    <p>The 500 error on the homepage must be caused by something else (theme, plugins, etc.)</p>
</div>

</body>
</html>
