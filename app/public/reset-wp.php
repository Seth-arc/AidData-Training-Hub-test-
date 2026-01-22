<?php
/**
 * Direct database reset - bypasses WordPress
 */

// Database credentials (hardcoded to avoid WordPress loading)
$db_host = 'mysql.railway.internal';
$db_name = 'railway';
$db_user = 'root';
$db_pass = 'mopzmAdFBAdfFWjwhNcznxdyZzNuoFNx';
$correct_url = 'https://aiddata-training-hub-test-production.up.railway.app';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>WordPress Database Reset</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #333; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        button { background: #0073aa; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 16px; }
        button:hover { background: #005177; }
    </style>
</head>
<body>
    <h1>WordPress Database Reset Tool</h1>

<?php
$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<p class='error'>❌ Cannot connect to database: " . htmlspecialchars($mysqli->connect_error) . "</p>";
    echo "<p>Database connection details:</p>";
    echo "<ul>";
    echo "<li>Host: $db_host</li>";
    echo "<li>Database: $db_name</li>";
    echo "<li>User: $db_user</li>";
    echo "</ul>";
    exit;
}

echo "<p class='success'>✓ Connected to database successfully</p>";

// Check if wp_options exists
$result = $mysqli->query("SHOW TABLES LIKE 'wp_options'");

if ($result->num_rows > 0) {
    echo "<div class='info'>";
    echo "<p><strong>WordPress tables found in database.</strong></p>";

    // Check current URLs
    $url_result = $mysqli->query("SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home')");
    if ($url_result) {
        echo "<p>Current URLs in database:</p><ul>";
        while ($row = $url_result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['option_name']) . ": " . htmlspecialchars($row['option_value']) . "</li>";
        }
        echo "</ul>";
    }

    echo "<p>Correct URL should be: <strong>$correct_url</strong></p>";
    echo "</div>";

    if (isset($_POST['fix_urls'])) {
        echo "<h2>Fixing URLs...</h2>";
        $mysqli->query("UPDATE wp_options SET option_value = '$correct_url' WHERE option_name = 'siteurl'");
        $mysqli->query("UPDATE wp_options SET option_value = '$correct_url' WHERE option_name = 'home'");
        echo "<p class='success'>✓ URLs updated in database!</p>";
        echo "<p><a href='$correct_url'>Visit your site</a> | <a href='$correct_url/wp-admin/'>Admin login</a></p>";
    } elseif (isset($_POST['drop_tables'])) {
        echo "<h2>Dropping all WordPress tables...</h2>";
        $tables = $mysqli->query("SHOW TABLES LIKE 'wp_%'");
        $dropped = 0;
        while ($row = $tables->fetch_array()) {
            $table = $row[0];
            $mysqli->query("DROP TABLE IF EXISTS `$table`");
            echo "<p>Dropped table: $table</p>";
            $dropped++;
        }
        echo "<p class='success'>✓ Dropped $dropped WordPress tables</p>";
        echo "<p><a href='$correct_url/wp-admin/install.php'>Run WordPress installer</a></p>";
    } else {
        echo "<form method='post' onsubmit='return confirm(\"Are you sure?\");'>";
        echo "<button type='submit' name='fix_urls'>Fix URLs Only</button>";
        echo "</form>";
        echo "<br>";
        echo "<form method='post' onsubmit='return confirm(\"This will DELETE ALL WordPress data! Are you sure?\");'>";
        echo "<button type='submit' name='drop_tables' style='background: #dc3232;'>Drop All Tables & Start Fresh</button>";
        echo "</form>";
    }
} else {
    echo "<div class='info'>";
    echo "<p>No WordPress tables found. Database is clean.</p>";
    echo "<p><a href='$correct_url/wp-admin/install.php'>Run WordPress installer</a></p>";
    echo "</div>";
}

$mysqli->close();
?>

</body>
</html>
