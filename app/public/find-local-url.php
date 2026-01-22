<?php
/**
 * Find local development URL specifically
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
    <title>Find Local Development URL</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Find Local Development URL</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='warning'>Connection failed</div>";
    exit;
}

echo "<div class='success'>✓ Connected to database</div>";

echo "<h2>Searching for Local Development Patterns...</h2>";

// Check wp_posts guid column (usually contains the original URL)
$result = $mysqli->query("SELECT DISTINCT guid FROM wp_posts WHERE guid LIKE '%local%' OR guid LIKE '%localhost%' OR guid LIKE '%.test%' LIMIT 10");

$found = [];

if ($result && $result->num_rows > 0) {
    echo "<div class='info'>";
    echo "<h3>Found in wp_posts (guid column):</h3>";
    while ($row = $result->fetch_assoc()) {
        preg_match('/(https?:\/\/[^\/]+)/', $row['guid'], $matches);
        if (!empty($matches[1])) {
            $found[$matches[1]] = true;
            echo "<div><code>" . htmlspecialchars($row['guid']) . "</code></div>";
        }
    }
    echo "</div>";
}

// Check wp_options for local URLs
$result = $mysqli->query("SELECT option_name, option_value FROM wp_options WHERE
    (option_value LIKE '%localhost%' OR
     option_value LIKE '%.local%' OR
     option_value LIKE '%.test%' OR
     option_value LIKE '%127.0.0.1%')
    AND option_name NOT IN ('active_plugins', 'cron')
    LIMIT 20");

if ($result && $result->num_rows > 0) {
    echo "<div class='info'>";
    echo "<h3>Found in wp_options:</h3>";
    while ($row = $result->fetch_assoc()) {
        preg_match_all('/(https?:\/\/[^\/"\s]+)/', $row['option_value'], $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $url) {
                if (strpos($url, 'localhost') !== false ||
                    strpos($url, '.local') !== false ||
                    strpos($url, '.test') !== false ||
                    strpos($url, '127.0.0.1') !== false) {
                    $found[$url] = true;
                }
            }
        }
        echo "<div><strong>" . htmlspecialchars($row['option_name']) . ":</strong><br>";
        echo "<pre>" . htmlspecialchars(substr($row['option_value'], 0, 200)) . "...</pre></div>";
    }
    echo "</div>";
}

// Check wp_postmeta
$result = $mysqli->query("SELECT DISTINCT meta_value FROM wp_postmeta WHERE
    (meta_value LIKE '%localhost%' OR
     meta_value LIKE '%.local%' OR
     meta_value LIKE '%.test%')
    LIMIT 10");

if ($result && $result->num_rows > 0) {
    echo "<div class='info'>";
    echo "<h3>Found in wp_postmeta:</h3>";
    while ($row = $result->fetch_assoc()) {
        preg_match_all('/(https?:\/\/[^\/"\s]+)/', $row['meta_value'], $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $url) {
                if (strpos($url, 'localhost') !== false ||
                    strpos($url, '.local') !== false ||
                    strpos($url, '.test') !== false) {
                    $found[$url] = true;
                }
            }
        }
        echo "<pre>" . htmlspecialchars(substr($row['meta_value'], 0, 200)) . "...</pre>";
    }
    echo "</div>";
}

if (!empty($found)) {
    echo "<div class='success'>";
    echo "<h2>✓ Found Local Development URLs:</h2>";
    foreach (array_keys($found) as $url) {
        echo "<div style='margin: 10px 0; padding: 10px; background: white;'>";
        echo "<code style='font-size: 16px;'>" . htmlspecialchars($url) . "</code><br><br>";
        echo "<a href='/search-replace-urls.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; display: inline-block;'>Go to Search & Replace Tool</a>";
        echo "</div>";
    }
    echo "<p><strong>Copy one of these URLs and use it in the search-replace tool.</strong></p>";
    echo "</div>";
} else {
    echo "<div class='warning'>";
    echo "<h3>⚠ No Local Development URLs Found</h3>";
    echo "<p>The database might have already been cleaned, or the URLs use a different pattern.</p>";
    echo "<p><strong>Please manually check what your local site URL was and enter it in the search-replace tool:</strong></p>";
    echo "<p><a href='/search-replace-urls.php'>Go to Search & Replace Tool</a></p>";
    echo "</div>";
}

$mysqli->close();

?>

</body>
</html>
