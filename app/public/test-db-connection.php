<?php
/**
 * Test WordPress database connection
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .test { padding: 10px; margin: 10px 0; background: white; border-left: 4px solid #ccc; }
        .success { border-left-color: green; }
        .error { border-left-color: red; background: #ffe0e0; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; margin: 10px 0; }
        h2 { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>WordPress Database Connection Test</h1>

<?php

// Extract database credentials from wp-config.php without loading WordPress
echo "<h2>Test 1: Reading Database Credentials</h2>";

$db_name = 'railway';
$db_user = 'root';
$db_password = 'mopzmAdFBAdfFWjwhNcznxdyZzNuoFNx';
$db_host = 'mysql.railway.internal';
$db_charset = 'utf8';

echo "<div class='test success'>✓ Using hardcoded credentials to avoid wp-config loading</div>";
echo "<div class='test'>";
echo "Database Configuration:<br>";
echo "DB_NAME: " . $db_name . "<br>";
echo "DB_USER: " . $db_user . "<br>";
echo "DB_HOST: " . $db_host . "<br>";
echo "DB_CHARSET: " . $db_charset . "<br>";
echo "</div>";

// Now define them as constants for later tests
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);
define('DB_HOST', $db_host);
define('DB_CHARSET', $db_charset);

// Test 2: Direct mysqli connection (like phpinfo.php does)
echo "<h2>Test 2: Direct mysqli Connection</h2>";
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($mysqli->connect_error) {
        echo "<div class='test error'>✗ Connection failed: " . $mysqli->connect_error . "</div>";
        echo "<div class='test error'>Error number: " . $mysqli->connect_errno . "</div>";
    } else {
        echo "<div class='test success'>✓ Direct mysqli connection successful</div>";
        echo "<div class='test'>Server info: " . $mysqli->server_info . "</div>";

        // Test a simple query
        $result = $mysqli->query("SELECT 1");
        if ($result) {
            echo "<div class='test success'>✓ Can execute queries</div>";
        } else {
            echo "<div class='test error'>✗ Cannot execute queries: " . $mysqli->error . "</div>";
        }

        $mysqli->close();
    }
} catch (Throwable $e) {
    echo "<div class='test error'>✗ Exception: " . $e->getMessage() . "</div>";
}

// Test 3: Check if we can create a wpdb instance
echo "<h2>Test 3: WordPress wpdb Class</h2>";

// Check if wpdb class file exists
if (!file_exists(__DIR__ . '/wp-includes/class-wpdb.php')) {
    echo "<div class='test error'>✗ class-wpdb.php not found!</div>";
} else {
    echo "<div class='test success'>✓ class-wpdb.php exists</div>";

    // Try to load WordPress database class
    echo "<h3>Loading WordPress database class...</h3>";

    try {
        // Define required constants if not already defined
        if (!defined('ABSPATH')) {
            define('ABSPATH', __DIR__ . '/');
        }
        if (!defined('WPINC')) {
            define('WPINC', 'wp-includes');
        }

        // Load required files
        require_once(ABSPATH . WPINC . '/class-wpdb.php');
        echo "<div class='test success'>✓ wpdb class loaded</div>";

        // Try to create a wpdb instance
        echo "<h3>Creating wpdb instance...</h3>";

        $wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

        if (!empty($wpdb->error)) {
            echo "<div class='test error'>✗ wpdb connection error: " . htmlspecialchars($wpdb->error) . "</div>";
        } else {
            echo "<div class='test success'>✓ wpdb instance created</div>";

            // Check if connection is ready
            if ($wpdb->check_connection(false)) {
                echo "<div class='test success'>✓ wpdb connection verified</div>";

                // Try a test query
                $result = $wpdb->get_var("SELECT 1");
                if ($result === '1') {
                    echo "<div class='test success'>✓ wpdb queries working</div>";
                } else {
                    echo "<div class='test error'>✗ wpdb query failed</div>";
                }
            } else {
                echo "<div class='test error'>✗ wpdb connection check failed</div>";
                if (isset($wpdb->last_error) && !empty($wpdb->last_error)) {
                    echo "<div class='test error'>Last error: " . htmlspecialchars($wpdb->last_error) . "</div>";
                }
            }
        }

    } catch (Throwable $e) {
        echo "<div class='test error'>✗ Exception creating wpdb: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}

// Test 4: Check PHP mysqli extension
echo "<h2>Test 4: PHP mysqli Extension</h2>";
if (extension_loaded('mysqli')) {
    echo "<div class='test success'>✓ mysqli extension loaded</div>";

    $mysqli_info = [
        'Client version' => mysqli_get_client_info(),
        'Client library version' => mysqli_get_client_version(),
    ];

    echo "<div class='test'>";
    foreach ($mysqli_info as $key => $value) {
        echo "$key: $value<br>";
    }
    echo "</div>";
} else {
    echo "<div class='test error'>✗ mysqli extension NOT loaded!</div>";
}

// Test 5: Check if database exists and has tables
echo "<h2>Test 5: Database Tables</h2>";
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$mysqli->connect_error) {
        $result = $mysqli->query("SHOW TABLES");
        $tables = [];
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }

        echo "<div class='test success'>✓ Found " . count($tables) . " tables in database</div>";

        if (count($tables) > 0) {
            echo "<div class='test'>";
            echo "Tables (first 10):<br>";
            foreach (array_slice($tables, 0, 10) as $table) {
                echo "- $table<br>";
            }
            echo "</div>";
        }

        $mysqli->close();
    }
} catch (Throwable $e) {
    echo "<div class='test error'>✗ Error: " . $e->getMessage() . "</div>";
}

?>

<hr>
<h2>Summary</h2>
<p>This diagnostic will help identify why WordPress cannot connect to the database even though direct mysqli connections work.</p>

</body>
</html>
