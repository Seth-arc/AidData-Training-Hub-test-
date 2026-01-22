<?php
/**
 * Deactivate WP-CASsify plugin
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
    <title>Deactivate WP-CASsify</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        button { padding: 10px 20px; background: #dc3545; color: white; border: none; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <h1>Deactivate WP-CASsify Plugin</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='warning'>Connection failed</div>";
    exit;
}

echo "<div class='warning'>";
echo "<h3>⚠️ WP-CASsify is causing login issues</h3>";
echo "<p>WP-CASsify is a CAS (Central Authentication Service) plugin for single sign-on.</p>";
echo "<p>It's trying to redirect logins to a CAS server that doesn't exist in this environment.</p>";
echo "<p><strong>This is causing your \"Redirect URL is not valid!\" error.</strong></p>";

if (!isset($_POST['deactivate'])) {
    echo "<form method='post'>";
    echo "<button type='submit' name='deactivate'>Deactivate WP-CASsify Plugin</button>";
    echo "</form>";
    echo "</div>";
} else {
    echo "</div>";

    // Get current active plugins
    $result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");
    $row = $result->fetch_assoc();
    $active_plugins = unserialize($row['option_value']);

    echo "<h2>Current Active Plugins:</h2>";
    echo "<div class='info'><ul>";
    foreach ($active_plugins as $plugin) {
        echo "<li>" . htmlspecialchars($plugin) . "</li>";
    }
    echo "</ul></div>";

    // Remove wp-cassify
    $plugin_to_remove = 'wp-cassify/wp-cassify.php';
    $key = array_search($plugin_to_remove, $active_plugins);

    if ($key !== false) {
        unset($active_plugins[$key]);
        $active_plugins = array_values($active_plugins); // Re-index array

        $serialized = serialize($active_plugins);
        $stmt = $mysqli->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
        $stmt->bind_param('s', $serialized);

        if ($stmt->execute()) {
            echo "<div class='success'>";
            echo "<h2>✓ WP-CASsify Deactivated!</h2>";
            echo "<p><strong>The plugin has been removed from active plugins.</strong></p>";
            echo "</div>";

            echo "<h2>Updated Active Plugins:</h2>";
            echo "<div class='info'>";
            if (empty($active_plugins)) {
                echo "<p>No plugins active</p>";
            } else {
                echo "<ul>";
                foreach ($active_plugins as $plugin) {
                    echo "<li>" . htmlspecialchars($plugin) . "</li>";
                }
                echo "</ul>";
            }
            echo "</div>";

            echo "<div class='success'>";
            echo "<h3>Now try logging in:</h3>";
            echo "<p><a href='/wp-login.php' style='font-size: 18px; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; display: inline-block;'>Go to Login Page</a></p>";
            echo "<p>The \"Redirect URL is not valid!\" error should be gone now.</p>";
            echo "</div>";
        } else {
            echo "<div class='warning'>Failed to update: " . htmlspecialchars($mysqli->error) . "</div>";
        }
    } else {
        echo "<div class='info'>WP-CASsify was not found in active plugins (maybe already deactivated?)</div>";
    }
}

$mysqli->close();

?>

</body>
</html>
