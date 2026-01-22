<?php
/**
 * Check which plugins are active
 */

header('Content-Type: text/html; charset=utf-8');

$db_host = 'mysql.railway.internal';
$db_name = 'railway';
$db_user = 'root';
$db_pass = 'mopzmAdFBAdfFWjwhNcznxdyZzNuoFNx';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Active Plugins Check</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Active Plugins Check</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='error'>Connection failed: " . htmlspecialchars($mysqli->connect_error) . "</div>";
    exit;
}

echo "<div class='success'>✓ Connected to database</div>";

// Get active plugins
echo "<h2>Active Plugins</h2>";
$result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");

if ($result && $row = $result->fetch_assoc()) {
    $active_plugins = unserialize($row['option_value']);

    if (empty($active_plugins)) {
        echo "<div class='info'>No plugins are active.</div>";
    } else {
        echo "<div class='info'>";
        echo "<strong>Active plugins:</strong><br>";
        echo "<ul>";
        foreach ($active_plugins as $plugin) {
            echo "<li>" . htmlspecialchars($plugin) . "</li>";
        }
        echo "</ul>";
        echo "</div>";

        echo "<div class='warning'>";
        echo "<p><strong>Recommendation:</strong> Try deactivating all plugins to see if one is causing the issue.</p>";
        echo "<p>Click the button below to deactivate all plugins:</p>";
        echo "<form method='post'>";
        echo "<button type='submit' name='deactivate_all' style='padding: 10px 20px; background: #dc3545; color: white; border: none; cursor: pointer;'>Deactivate All Plugins</button>";
        echo "</form>";
        echo "</div>";
    }
} else {
    echo "<div class='info'>Could not find active_plugins option</div>";
}

// Handle deactivation
if (isset($_POST['deactivate_all'])) {
    echo "<h2>Deactivating All Plugins...</h2>";
    $result = $mysqli->query("UPDATE wp_options SET option_value = 'a:0:{}' WHERE option_name = 'active_plugins'");

    if ($result) {
        echo "<div class='success'>";
        echo "✓ All plugins have been deactivated!<br>";
        echo "<strong>Now try accessing the homepage:</strong><br>";
        echo "<a href='/'>https://aiddata-training-hub-test-production.up.railway.app/</a>";
        echo "</div>";
    } else {
        echo "<div class='error'>Failed to deactivate plugins: " . htmlspecialchars($mysqli->error) . "</div>";
    }
}

// Check theme
echo "<h2>Active Theme</h2>";
$result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name = 'template'");
if ($result && $row = $result->fetch_assoc()) {
    $theme = $row['option_value'];
    echo "<div class='info'>Active theme: <strong>" . htmlspecialchars($theme) . "</strong></div>";

    // Check if theme directory exists
    $theme_path = __DIR__ . "/wp-content/themes/$theme";
    if (file_exists($theme_path)) {
        echo "<div class='success'>✓ Theme directory exists at: $theme</div>";
    } else {
        echo "<div class='warning'>⚠ Theme directory not found! This could be the issue.</div>";
    }
}

// Check for errors in wp_options that might block loading
echo "<h2>WordPress Configuration</h2>";
$options_to_check = ['siteurl', 'home', 'blog_public'];
foreach ($options_to_check as $option_name) {
    $result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name = '$option_name'");
    if ($result && $row = $result->fetch_assoc()) {
        echo "<div class='info'>$option_name: " . htmlspecialchars($row['option_value']) . "</div>";
    }
}

$mysqli->close();

?>

</body>
</html>
