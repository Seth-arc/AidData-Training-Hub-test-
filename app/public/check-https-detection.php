<?php
/**
 * Check HTTPS detection and server variables
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>HTTPS Detection Check</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>HTTPS Detection Check</h1>

<h2>Critical $_SERVER Variables</h2>
<div class="info">
<table>
    <tr>
        <th>Variable</th>
        <th>Value</th>
        <th>Status</th>
    </tr>
    <tr>
        <td>$_SERVER['HTTPS']</td>
        <td><?php echo isset($_SERVER['HTTPS']) ? htmlspecialchars($_SERVER['HTTPS']) : '<em>not set</em>'; ?></td>
        <td><?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? '✓ Correct' : '✗ Problem'; ?></td>
    </tr>
    <tr>
        <td>$_SERVER['HTTP_X_FORWARDED_PROTO']</td>
        <td><?php echo isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? htmlspecialchars($_SERVER['HTTP_X_FORWARDED_PROTO']) : '<em>not set</em>'; ?></td>
        <td><?php echo (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? '✓ Correct' : '✗ Problem'; ?></td>
    </tr>
    <tr>
        <td>$_SERVER['HTTP_X_FORWARDED_HOST']</td>
        <td><?php echo isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? htmlspecialchars($_SERVER['HTTP_X_FORWARDED_HOST']) : '<em>not set</em>'; ?></td>
        <td><?php echo isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? 'Set' : 'Not set'; ?></td>
    </tr>
    <tr>
        <td>$_SERVER['HTTP_HOST']</td>
        <td><?php echo isset($_SERVER['HTTP_HOST']) ? htmlspecialchars($_SERVER['HTTP_HOST']) : '<em>not set</em>'; ?></td>
        <td><?php echo isset($_SERVER['HTTP_HOST']) ? 'Set' : 'Not set'; ?></td>
    </tr>
    <tr>
        <td>$_SERVER['SERVER_PORT']</td>
        <td><?php echo isset($_SERVER['SERVER_PORT']) ? htmlspecialchars($_SERVER['SERVER_PORT']) : '<em>not set</em>'; ?></td>
        <td><?php echo isset($_SERVER['SERVER_PORT']) ? 'Set' : 'Not set'; ?></td>
    </tr>
    <tr>
        <td>$_SERVER['REQUEST_SCHEME']</td>
        <td><?php echo isset($_SERVER['REQUEST_SCHEME']) ? htmlspecialchars($_SERVER['REQUEST_SCHEME']) : '<em>not set</em>'; ?></td>
        <td><?php echo (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') ? '✓ Correct' : '✗ Problem'; ?></td>
    </tr>
</table>
</div>

<h2>WordPress HTTPS Detection</h2>
<?php
define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

echo "<div class='info'>";
echo "<table>";
echo "<tr><th>Check</th><th>Result</th></tr>";

// Check is_ssl()
$is_ssl = is_ssl();
echo "<tr>";
echo "<td>is_ssl()</td>";
echo "<td>" . ($is_ssl ? '<span style="color: green;">✓ TRUE - WordPress detects HTTPS</span>' : '<span style="color: red;">✗ FALSE - WordPress does NOT detect HTTPS</span>') . "</td>";
echo "</tr>";

// Check home_url()
$home_url = home_url();
echo "<tr>";
echo "<td>home_url()</td>";
echo "<td>" . htmlspecialchars($home_url) . "</td>";
echo "</tr>";

// Check admin_url()
$admin_url = admin_url();
echo "<tr>";
echo "<td>admin_url()</td>";
echo "<td>" . htmlspecialchars($admin_url) . "</td>";
echo "</tr>";

// Check site_url()
$site_url = site_url();
echo "<tr>";
echo "<td>site_url()</td>";
echo "<td>" . htmlspecialchars($site_url) . "</td>";
echo "</tr>";

echo "</table>";
echo "</div>";

// Diagnosis
echo "<h2>Diagnosis</h2>";

if (!$is_ssl) {
    echo "<div class='error'>";
    echo "<h3>❌ Problem Found: WordPress is not detecting HTTPS</h3>";
    echo "<p>Even though you're accessing via HTTPS, WordPress's <code>is_ssl()</code> returns FALSE.</p>";
    echo "<p><strong>This causes:</strong></p>";
    echo "<ul>";
    echo "<li>WordPress to generate HTTP URLs instead of HTTPS</li>";
    echo "<li>Admin redirects to fail</li>";
    echo "<li>Mixed content warnings</li>";
    echo "</ul>";
    echo "<p><strong>Solution:</strong> We need to force WordPress to recognize HTTPS in wp-config.php</p>";
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<h3>✓ WordPress correctly detects HTTPS</h3>";
    echo "<p>is_ssl() returns TRUE, which is correct.</p>";
    echo "</div>";
}

// Check if URLs have protocol
if (strpos($admin_url, 'http') !== 0) {
    echo "<div class='error'>";
    echo "<h3>❌ admin_url() is missing the protocol!</h3>";
    echo "<p>The URL is: <code>" . htmlspecialchars($admin_url) . "</code></p>";
    echo "<p>This will cause routing failures.</p>";
    echo "</div>";
}
?>

<h2>All $_SERVER Variables</h2>
<div class="info">
<pre><?php print_r($_SERVER); ?></pre>
</div>

</body>
</html>
