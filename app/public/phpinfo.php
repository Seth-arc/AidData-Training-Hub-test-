<?php
/**
 * PHP Information and Extension Check
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Configuration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #333; }
        .success { color: green; }
        .error { color: red; }
        table { border-collapse: collapse; margin: 10px 0; }
        td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>PHP Configuration Check</h1>

    <h2>PHP Version</h2>
    <p><strong><?php echo phpversion(); ?></strong></p>

    <h2>Required WordPress Extensions</h2>
    <table>
        <tr><th>Extension</th><th>Status</th></tr>
        <?php
        $required = ['mysqli', 'curl', 'gd', 'mbstring', 'xml', 'zip', 'json'];
        foreach ($required as $ext) {
            $loaded = extension_loaded($ext);
            echo "<tr>";
            echo "<td>$ext</td>";
            echo "<td class='" . ($loaded ? 'success' : 'error') . "'>" . ($loaded ? '✓ Loaded' : '✗ Missing') . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <h2>Memory Settings</h2>
    <table>
        <tr><th>Setting</th><th>Value</th></tr>
        <tr><td>memory_limit</td><td><?php echo ini_get('memory_limit'); ?></td></tr>
        <tr><td>max_execution_time</td><td><?php echo ini_get('max_execution_time'); ?></td></tr>
        <tr><td>upload_max_filesize</td><td><?php echo ini_get('upload_max_filesize'); ?></td></tr>
        <tr><td>post_max_size</td><td><?php echo ini_get('post_max_size'); ?></td></tr>
    </table>

    <h2>Error Reporting</h2>
    <table>
        <tr><th>Setting</th><th>Value</th></tr>
        <tr><td>display_errors</td><td><?php echo ini_get('display_errors') ? 'On' : 'Off'; ?></td></tr>
        <tr><td>log_errors</td><td><?php echo ini_get('log_errors') ? 'On' : 'Off'; ?></td></tr>
        <tr><td>error_log</td><td><?php echo ini_get('error_log') ?: 'Not set'; ?></td></tr>
        <tr><td>error_reporting</td><td><?php echo error_reporting(); ?></td></tr>
    </table>

    <h2>Database Connection Test</h2>
    <?php
    $db_host = 'mysql.railway.internal';
    $db_name = 'railway';
    $db_user = 'root';
    $db_pass = 'mopzmAdFBAdfFWjwhNcznxdyZzNuoFNx';

    try {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($mysqli->connect_error) {
            echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($mysqli->connect_error) . "</p>";
        } else {
            echo "<p class='success'>✓ Database connection successful</p>";
            $mysqli->close();
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>

    <h2>WordPress Files</h2>
    <?php
    $wp_files = ['index.php', 'wp-config.php', 'wp-login.php', 'wp-load.php'];
    echo "<table><tr><th>File</th><th>Status</th></tr>";
    foreach ($wp_files as $file) {
        $exists = file_exists(__DIR__ . '/' . $file);
        echo "<tr>";
        echo "<td>$file</td>";
        echo "<td class='" . ($exists ? 'success' : 'error') . "'>" . ($exists ? '✓ Exists' : '✗ Missing') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>

    <hr>
    <p><a href="/">Back to Homepage</a></p>
</body>
</html>
