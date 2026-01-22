<?php
/**
 * Fix WordPress URLs in database
 */

// Load WordPress config
require_once(__DIR__ . '/wp-config.php');

// Connect to database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$correct_url = 'https://aiddata-training-hub-test-production.up.railway.app';

echo "<h2>WordPress URL Fixer</h2>";
echo "<p>Correct URL should be: <strong>$correct_url</strong></p>";

// Check if wp_options table exists
$table_check = $mysqli->query("SHOW TABLES LIKE 'wp_options'");
if ($table_check->num_rows == 0) {
    echo "<p style='color: red;'>❌ WordPress is not installed yet. No wp_options table found.</p>";
    echo "<p>You need to run the WordPress installer first.</p>";
    echo "<p>But there seems to be a redirect issue preventing that...</p>";

    // Let's try to create a minimal installation
    echo "<h3>Attempting to create minimal WordPress installation...</h3>";

    // Create tables using WordPress installer
    define('WP_INSTALLING', true);
    require_once(__DIR__ . '/wp-admin/includes/upgrade.php');

    wp_install("AidData Training Hub", "admin", "admin@example.com", true, '', wp_slash(wp_generate_password()));

    echo "<p style='color: green;'>✓ WordPress tables created!</p>";

    // Now set the URLs
    $mysqli->query("UPDATE wp_options SET option_value = '$correct_url' WHERE option_name = 'siteurl'");
    $mysqli->query("UPDATE wp_options SET option_value = '$correct_url' WHERE option_name = 'home'");

    echo "<p style='color: green;'>✓ URLs set correctly!</p>";
    echo "<p><strong>WordPress is now installed with default credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Username: admin</li>";
    echo "<li>Password: (check the output above or reset via database)</li>";
    echo "</ul>";
    echo "<p><a href='/wp-admin/'>Go to WordPress Admin</a></p>";
} else {
    // Table exists, check current values
    $result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name IN ('siteurl', 'home')");

    echo "<h3>Current URLs in database:</h3><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['option_value']) . "</li>";
    }
    echo "</ul>";

    // Fix the URLs
    echo "<h3>Fixing URLs...</h3>";
    $mysqli->query("UPDATE wp_options SET option_value = '$correct_url' WHERE option_name = 'siteurl'");
    $mysqli->query("UPDATE wp_options SET option_value = '$correct_url' WHERE option_name = 'home'");

    echo "<p style='color: green;'>✓ URLs updated!</p>";
    echo "<p><a href='/'>Go to Homepage</a> | <a href='/wp-admin/'>Go to Admin</a></p>";
}

$mysqli->close();
