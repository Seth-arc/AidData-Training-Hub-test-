<?php
/**
 * Diagnose login redirect issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Redirect Diagnostic</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Login Redirect Diagnostic</h1>

<?php

define('WP_USE_THEMES', false);
require_once(__DIR__ . '/wp-load.php');

echo "<h2>WordPress Configuration</h2>";
echo "<div class='info'>";
echo "WP_SITEURL: " . WP_SITEURL . "<br>";
echo "WP_HOME: " . WP_HOME . "<br>";
echo "site_url(): " . site_url() . "<br>";
echo "home_url(): " . home_url() . "<br>";
echo "admin_url(): " . admin_url() . "<br>";
echo "wp_login_url(): " . wp_login_url() . "<br>";
echo "</div>";

echo "<h2>Allowed Redirect Hosts</h2>";
echo "<div class='info'>";
$allowed_hosts = apply_filters('allowed_redirect_hosts', array($_SERVER['HTTP_HOST']));
echo "<pre>";
print_r($allowed_hosts);
echo "</pre>";
echo "</div>";

echo "<h2>Test Redirect Validation</h2>";
$test_urls = [
    admin_url(),
    admin_url('index.php'),
    home_url('/wp-admin/'),
    'https://aiddata-training-hub-test-production.up.railway.app/wp-admin/',
    '/wp-admin/',
];

echo "<div class='info'>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>URL</th><th>wp_validate_redirect()</th></tr>";

foreach ($test_urls as $url) {
    $validated = wp_validate_redirect($url, false);
    $status = $validated ? "<span style='color: green;'>✓ Valid</span>" : "<span style='color: red;'>✗ Invalid</span>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($url) . "</td>";
    echo "<td>$status (returns: " . htmlspecialchars($validated) . ")</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

echo "<h2>Login URL Test</h2>";
echo "<div class='info'>";
$redirect_to = admin_url();
$login_url = wp_login_url($redirect_to);
echo "<p>Login URL with redirect: <code>" . htmlspecialchars($login_url) . "</code></p>";
echo "<p>Redirect parameter: <code>" . htmlspecialchars($redirect_to) . "</code></p>";

// Parse the URL
$parsed = parse_url($login_url);
if (isset($parsed['query'])) {
    parse_str($parsed['query'], $query_params);
    if (isset($query_params['redirect_to'])) {
        echo "<p>redirect_to parameter: <code>" . htmlspecialchars($query_params['redirect_to']) . "</code></p>";
        $is_valid = wp_validate_redirect($query_params['redirect_to'], false);
        echo "<p>Is valid? " . ($is_valid ? "<span style='color: green;'>YES</span>" : "<span style='color: red;'>NO</span>") . "</p>";
    }
}
echo "</div>";

echo "<h2>Active Plugins Check</h2>";
echo "<div class='info'>";
$active_plugins = get_option('active_plugins', array());
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

echo "<h2>Security Settings</h2>";
echo "<div class='info'>";
echo "FORCE_SSL_ADMIN: " . (defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ? 'true' : 'false') . "<br>";
echo "FORCE_SSL_LOGIN: " . (defined('FORCE_SSL_LOGIN') && FORCE_SSL_LOGIN ? 'true' : 'false') . "<br>";
echo "is_ssl(): " . (is_ssl() ? 'true' : 'false') . "<br>";
echo "</div>";

// Check for redirect filters
echo "<h2>Redirect Filters</h2>";
echo "<div class='info'>";
global $wp_filter;
$redirect_filters = ['allowed_redirect_hosts', 'wp_redirect', 'wp_safe_redirect_fallback'];
foreach ($redirect_filters as $filter) {
    if (isset($wp_filter[$filter])) {
        echo "<p><strong>$filter:</strong> " . count($wp_filter[$filter]->callbacks) . " callbacks registered</p>";
    } else {
        echo "<p><strong>$filter:</strong> No callbacks</p>";
    }
}
echo "</div>";

?>

<hr>
<h2>Direct Login Test</h2>
<div class="info">
    <p>Try logging in with a simple redirect:</p>
    <form method="get" action="/wp-login.php">
        <input type="hidden" name="redirect_to" value="/wp-admin/">
        <button type="submit">Login (redirect to /wp-admin/)</button>
    </form>
    <br>
    <form method="get" action="/wp-login.php">
        <button type="submit">Login (no redirect parameter)</button>
    </form>
</div>

</body>
</html>
