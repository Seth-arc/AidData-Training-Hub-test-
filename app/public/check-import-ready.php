<?php
/**
 * Check if All-in-One WP Migration is ready for import
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
    <title>Import Readiness Check</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>All-in-One WP Migration Import Readiness</h1>

<?php

// Check if plugin is active
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='error'>Database connection failed</div>";
    exit;
}

echo "<h2>Plugin Status</h2>";
$result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");
$active_plugins = [];
if ($result && $row = $result->fetch_assoc()) {
    $active_plugins = unserialize($row['option_value']);
}

$aio_active = false;
foreach ($active_plugins as $plugin) {
    if (strpos($plugin, 'all-in-one-wp-migration') !== false) {
        $aio_active = true;
        echo "<div class='success'>✓ All-in-One WP Migration is ACTIVE: $plugin</div>";
    }
}

if (!$aio_active) {
    echo "<div class='warning'>";
    echo "⚠ All-in-One WP Migration is NOT active<br>";
    echo "<form method='post'>";
    echo "<button type='submit' name='activate_plugin' style='padding: 10px; background: #007bff; color: white; border: none; cursor: pointer;'>Activate Plugin Now</button>";
    echo "</form>";
    echo "</div>";
}

// Handle activation
if (isset($_POST['activate_plugin'])) {
    $plugin_path = 'all-in-one-wp-migration/all-in-one-wp-migration.php';
    if (!in_array($plugin_path, $active_plugins)) {
        $active_plugins[] = $plugin_path;
        $serialized = serialize($active_plugins);
        $stmt = $mysqli->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
        $stmt->bind_param('s', $serialized);
        if ($stmt->execute()) {
            echo "<div class='success'>✓ Plugin activated! Please refresh this page.</div>";
        }
    }
}

// Check upload limits
echo "<h2>PHP Upload Limits</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Current Value</th><th>Status</th></tr>";

$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$memory_limit = ini_get('memory_limit');
$max_execution = ini_get('max_execution_time');

echo "<tr>";
echo "<td>upload_max_filesize</td>";
echo "<td>$upload_max</td>";
echo "<td>" . (intval($upload_max) >= 64 ? "✓ Good" : "⚠ May need increase") . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td>post_max_size</td>";
echo "<td>$post_max</td>";
echo "<td>" . (intval($post_max) >= 64 ? "✓ Good" : "⚠ May need increase") . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td>memory_limit</td>";
echo "<td>$memory_limit</td>";
echo "<td>" . (intval($memory_limit) >= 256 ? "✓ Good" : "⚠ May need increase") . "</td>";
echo "</tr>";

echo "<tr>";
echo "<td>max_execution_time</td>";
echo "<td>$max_execution seconds</td>";
echo "<td>" . ($max_execution >= 300 ? "✓ Good" : "⚠ May need increase") . "</td>";
echo "</tr>";

echo "</table>";

// Check nginx client_max_body_size
echo "<h2>Nginx Upload Limit</h2>";
$nginx_limit = "100M"; // From default.conf
echo "<div class='success'>✓ Nginx client_max_body_size: $nginx_limit</div>";

// Check if storage directory exists
echo "<h2>Storage Directory</h2>";
$storage_path = '/var/www/html/wp-content/ai1wm-backups';
if (file_exists($storage_path)) {
    echo "<div class='success'>✓ Storage directory exists: $storage_path</div>";
    if (is_writable($storage_path)) {
        echo "<div class='success'>✓ Storage directory is writable</div>";
    } else {
        echo "<div class='error'>✗ Storage directory is NOT writable</div>";
    }
} else {
    echo "<div class='warning'>⚠ Storage directory does not exist yet (will be created on first use)</div>";
}

$mysqli->close();

?>

<hr>
<h2>Next Steps to Import Your Backup</h2>
<div class="info">
    <ol>
        <li>Make sure the plugin is activated (see above)</li>
        <li>Go to <a href="/wp-admin/admin.php?page=ai1wm_import" target="_blank">All-in-One WP Migration → Import</a></li>
        <li>Click "Import From" → "File"</li>
        <li>Select your .wpress backup file</li>
        <li>Wait for the import to complete (may take several minutes for large files)</li>
    </ol>

    <p><strong>Important:</strong> After importing, you may need to:</p>
    <ul>
        <li>Log back in (your current session will end)</li>
        <li>Run the fix-site-urls.php script again if URLs get changed</li>
    </ul>

    <p><strong>Quick Link:</strong> <a href="/wp-admin/admin.php?page=ai1wm_import" target="_blank">Go to Import Page</a></p>
</div>

</body>
</html>
