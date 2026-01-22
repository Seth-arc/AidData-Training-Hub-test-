<?php
/**
 * Diagnostic script to test database connectivity
 */
header('Content-Type: text/plain');

echo "=== Railway WordPress Diagnostics ===\n\n";

// Test 1: PHP is working
echo "1. PHP Version: " . phpversion() . " ✓\n\n";

// Test 2: Check if wp-config.php exists and can be loaded
if (file_exists(__DIR__ . '/wp-config.php')) {
    echo "2. wp-config.php exists ✓\n";

    // Load WordPress configuration
    require_once(__DIR__ . '/wp-config.php');

    echo "3. Database Configuration:\n";
    echo "   - Host: " . DB_HOST . "\n";
    echo "   - Database: " . DB_NAME . "\n";
    echo "   - User: " . DB_USER . "\n\n";

    // Test 3: Database connection
    echo "4. Testing database connection...\n";
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($mysqli->connect_error) {
        echo "   ✗ Connection failed: " . $mysqli->connect_error . "\n";
    } else {
        echo "   ✓ Database connected successfully\n";
        echo "   - MySQL version: " . $mysqli->server_info . "\n";

        // Test if tables exist
        $result = $mysqli->query("SHOW TABLES");
        if ($result) {
            $table_count = $result->num_rows;
            echo "   - Tables found: " . $table_count . "\n";

            if ($table_count > 0) {
                echo "   - Sample tables:\n";
                $i = 0;
                while ($row = $result->fetch_array() and $i < 5) {
                    echo "     * " . $row[0] . "\n";
                    $i++;
                }
            }
        }
        $mysqli->close();
    }
} else {
    echo "2. wp-config.php NOT FOUND ✗\n";
}

echo "\n5. WordPress Core Files:\n";
echo "   - index.php: " . (file_exists(__DIR__ . '/index.php') ? "✓" : "✗") . "\n";
echo "   - wp-load.php: " . (file_exists(__DIR__ . '/wp-load.php') ? "✓" : "✗") . "\n";
echo "   - wp-settings.php: " . (file_exists(__DIR__ . '/wp-settings.php') ? "✓" : "✗") . "\n";

echo "\n6. PHP Extensions:\n";
echo "   - mysqli: " . (extension_loaded('mysqli') ? "✓" : "✗") . "\n";
echo "   - pdo_mysql: " . (extension_loaded('pdo_mysql') ? "✓" : "✗") . "\n";
echo "   - gd: " . (extension_loaded('gd') ? "✓" : "✗") . "\n";

echo "\n=== End of Diagnostics ===\n";
