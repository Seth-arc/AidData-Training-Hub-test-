<?php
/**
 * Check database and table charset/collation
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
    <title>Database Charset Check</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; background: white; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
    </style>
</head>
<body>
    <h1>Database Charset Analysis</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='error'>Connection failed: " . htmlspecialchars($mysqli->connect_error) . "</div>";
    exit;
}

echo "<div class='success'>✓ Connected to database</div>";

// Check database charset
echo "<h2>Database Default Charset</h2>";
$result = $mysqli->query("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<div class='info'>";
    echo "Character Set: <strong>" . htmlspecialchars($row['DEFAULT_CHARACTER_SET_NAME']) . "</strong><br>";
    echo "Collation: <strong>" . htmlspecialchars($row['DEFAULT_COLLATION_NAME']) . "</strong>";
    echo "</div>";
}

// Check WordPress tables charset
echo "<h2>WordPress Tables Charset/Collation</h2>";
$result = $mysqli->query("
    SELECT
        TABLE_NAME,
        TABLE_COLLATION,
        CCSA.CHARACTER_SET_NAME
    FROM information_schema.TABLES T,
         information_schema.COLLATION_CHARACTER_SET_APPLICABILITY CCSA
    WHERE
        T.TABLE_SCHEMA = '$db_name'
        AND T.TABLE_COLLATION = CCSA.COLLATION_NAME
        AND T.TABLE_NAME LIKE 'wp_%'
    ORDER BY TABLE_NAME
");

if ($result) {
    echo "<table>";
    echo "<tr><th>Table</th><th>Character Set</th><th>Collation</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $charset = htmlspecialchars($row['CHARACTER_SET_NAME']);
        $collation = htmlspecialchars($row['TABLE_COLLATION']);
        $table = htmlspecialchars($row['TABLE_NAME']);

        // Highlight if not utf8mb4
        $class = ($charset !== 'utf8mb4') ? ' style="background: #fff3cd;"' : '';

        echo "<tr$class>";
        echo "<td>$table</td>";
        echo "<td>$charset</td>";
        echo "<td>$collation</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check wp_options table specifically
echo "<h2>wp_options Table Structure</h2>";
$result = $mysqli->query("SHOW FULL COLUMNS FROM wp_options");
if ($result) {
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Collation</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Collation'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='error'>Could not get wp_options structure: " . htmlspecialchars($mysqli->error) . "</div>";
}

// Test SET NAMES with different charsets
echo "<h2>Testing SET NAMES Commands</h2>";

$charsets_to_test = ['utf8', 'utf8mb3', 'utf8mb4'];

foreach ($charsets_to_test as $charset) {
    echo "<div class='info'>";
    echo "Testing: SET NAMES '$charset'<br>";

    $test_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($test_conn->connect_error) {
        echo "<span style='color: red;'>✗ Connection failed</span>";
    } else {
        // Try to set charset
        $result = $test_conn->query("SET NAMES '$charset'");
        if ($result) {
            echo "<span style='color: green;'>✓ Success</span>";
        } else {
            echo "<span style='color: red;'>✗ Failed: " . htmlspecialchars($test_conn->error) . "</span>";
        }
    }
    $test_conn->close();
    echo "</div>";
}

// Check MySQL version and utf8 support
echo "<h2>MySQL Version & Charset Support</h2>";
$version = $mysqli->query("SELECT VERSION() as version")->fetch_assoc()['version'];
echo "<div class='info'>";
echo "MySQL Version: <strong>" . htmlspecialchars($version) . "</strong><br>";

// Check if utf8 (utf8mb3) is deprecated
if (version_compare($version, '8.0.0', '>=')) {
    echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0;'>";
    echo "⚠ MySQL 8.0+ has deprecated 'utf8' (utf8mb3) charset.<br>";
    echo "WordPress should use 'utf8mb4' instead.";
    echo "</div>";
}

echo "</div>";

// Check current wp-config.php setting
echo "<h2>Current wp-config.php Setting</h2>";
$wp_config = file_get_contents(__DIR__ . '/wp-config.php');
if (preg_match("/define\(\s*'DB_CHARSET'\s*,\s*'([^']+)'/", $wp_config, $matches)) {
    $config_charset = $matches[1];
    echo "<div class='info'>";
    echo "DB_CHARSET in wp-config.php: <strong>" . htmlspecialchars($config_charset) . "</strong>";
    echo "</div>";
}

$mysqli->close();

?>

<hr>
<h2>Summary & Recommendations</h2>
<div class="info">
    <p><strong>Check the results above:</strong></p>
    <ul>
        <li>If tables are using 'utf8' (utf8mb3) but wp-config.php is set to 'utf8mb4', there may be a mismatch</li>
        <li>If 'SET NAMES utf8mb4' succeeds but 'SET NAMES utf8' fails, that confirms the issue</li>
        <li>MySQL 8.0+ requires utf8mb4 - older utf8 charset may not work properly</li>
    </ul>
</div>

</body>
</html>
