<?php
/**
 * Search and Replace URLs across entire database
 */

header('Content-Type: text/html; charset=utf-8');

$db_host = 'mysql.railway.internal';
$db_name = 'railway';
$db_user = 'root';
$db_pass = 'mopzmAdFBAdfFWjwhNcznxdyZzNuoFNx';
$correct_url = 'https://aiddata-training-hub-test-production.up.railway.app';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database URL Search & Replace</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; margin: 5px; }
        button:hover { background: #0056b3; }
        input { padding: 8px; width: 400px; font-family: monospace; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Database URL Search & Replace</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='error'>Connection failed</div>";
    exit;
}

echo "<div class='success'>✓ Connected to database</div>";

// Show what URL we're replacing to
echo "<div class='info'>";
echo "<strong>Target URL (what we're replacing TO):</strong><br>";
echo "<code>$correct_url</code>";
echo "</div>";

if (!isset($_POST['search_url']) && !isset($_POST['auto_detect'])) {
    // Show form to enter old URL
    echo "<div class='warning'>";
    echo "<h3>Enter Your Old URL</h3>";
    echo "<p>Enter the URL from your local/staging site that needs to be replaced:</p>";
    echo "<form method='post'>";
    echo "Old URL: <input type='text' name='search_url' placeholder='e.g., http://localhost:10004 or https://old-site.com' required><br><br>";
    echo "<button type='submit'>Search & Replace</button>";
    echo "</form>";
    echo "<hr>";
    echo "<p><strong>Don't know your old URL?</strong> Click below to auto-detect it:</p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='auto_detect'>Auto-Detect Old URL</button>";
    echo "</form>";
    echo "</div>";
}

// Auto-detect old URL
if (isset($_POST['auto_detect'])) {
    echo "<h2>Auto-Detecting Old URL...</h2>";

    // Check common places for old URLs
    $patterns_to_check = [
        "SELECT option_value FROM wp_options WHERE option_name = 'template' LIMIT 1",
        "SELECT guid FROM wp_posts WHERE post_type = 'attachment' LIMIT 1",
        "SELECT meta_value FROM wp_postmeta WHERE meta_key = '_wp_attached_file' LIMIT 1"
    ];

    $found_urls = [];

    // Check wp_posts for URLs in content
    $result = $mysqli->query("SELECT post_content FROM wp_posts WHERE post_content LIKE '%http%' LIMIT 10");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            preg_match_all('/(https?:\/\/[^\/\s"\']+)/i', $row['post_content'], $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $url) {
                    if ($url !== $correct_url) {
                        $found_urls[$url] = ($found_urls[$url] ?? 0) + 1;
                    }
                }
            }
        }
    }

    // Check wp_options for serialized data with URLs
    $result = $mysqli->query("SELECT option_value FROM wp_options WHERE option_value LIKE '%http%' LIMIT 50");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            preg_match_all('/(https?:\/\/[^\/\s"\']+)/i', $row['option_value'], $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $url) {
                    if ($url !== $correct_url) {
                        $found_urls[$url] = ($found_urls[$url] ?? 0) + 1;
                    }
                }
            }
        }
    }

    if (!empty($found_urls)) {
        arsort($found_urls);
        echo "<div class='info'>";
        echo "<h3>Found URLs in Database:</h3>";
        echo "<table>";
        echo "<tr><th>URL</th><th>Occurrences</th><th>Action</th></tr>";
        foreach (array_slice($found_urls, 0, 5) as $url => $count) {
            echo "<tr>";
            echo "<td><code>" . htmlspecialchars($url) . "</code></td>";
            echo "<td>$count</td>";
            echo "<td><form method='post' style='margin:0'><input type='hidden' name='search_url' value='" . htmlspecialchars($url) . "'><button type='submit'>Replace This</button></form></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div class='warning'>No old URLs found. The database might already be clean.</div>";
    }
}

// Perform search and replace
if (isset($_POST['search_url']) && !empty($_POST['search_url'])) {
    $old_url = $_POST['search_url'];

    echo "<div class='warning'>";
    echo "<h3>Search & Replace Operation</h3>";
    echo "<p><strong>OLD URL:</strong> <code>" . htmlspecialchars($old_url) . "</code></p>";
    echo "<p><strong>NEW URL:</strong> <code>$correct_url</code></p>";
    echo "</div>";

    // Tables to search
    $tables = ['wp_posts', 'wp_postmeta', 'wp_options', 'wp_comments', 'wp_commentmeta', 'wp_users', 'wp_usermeta'];

    $total_replacements = 0;

    echo "<h3>Processing Tables...</h3>";

    foreach ($tables as $table) {
        // Check if table exists
        $check = $mysqli->query("SHOW TABLES LIKE '$table'");
        if ($check->num_rows == 0) {
            continue;
        }

        echo "<div class='info'>Processing <strong>$table</strong>...";

        // Get columns
        $columns_result = $mysqli->query("SHOW COLUMNS FROM $table");
        $text_columns = [];

        while ($col = $columns_result->fetch_assoc()) {
            $type = strtolower($col['Type']);
            if (strpos($type, 'text') !== false || strpos($type, 'varchar') !== false || strpos($type, 'char') !== false) {
                $text_columns[] = $col['Field'];
            }
        }

        $table_updates = 0;

        foreach ($text_columns as $column) {
            // Simple replace for non-serialized data
            $sql = "UPDATE $table SET $column = REPLACE($column, ?, ?) WHERE $column LIKE ?";
            $stmt = $mysqli->prepare($sql);
            $search_pattern = '%' . $old_url . '%';
            $stmt->bind_param('sss', $old_url, $correct_url, $search_pattern);
            $stmt->execute();
            $affected = $stmt->affected_rows;

            if ($affected > 0) {
                $table_updates += $affected;
            }
        }

        if ($table_updates > 0) {
            echo " <strong style='color: green;'>✓ $table_updates rows updated</strong>";
            $total_replacements += $table_updates;
        } else {
            echo " (no changes)";
        }

        echo "</div>";
    }

    echo "<div class='success'>";
    echo "<h3>✓ Search & Replace Complete!</h3>";
    echo "<p><strong>Total replacements:</strong> $total_replacements</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='$correct_url/wp-login.php'>Try logging in again</a></li>";
    echo "<li>Clear your browser cache if you still have issues</li>";
    echo "</ul>";
    echo "</div>";
}

$mysqli->close();

?>

<hr>
<div class="info">
    <p><strong>Common old URL formats:</strong></p>
    <ul>
        <li>Local by Flywheel: <code>http://aiddata-training-hub-test.local</code></li>
        <li>Local WP: <code>http://localhost:10004</code></li>
        <li>MAMP: <code>http://localhost:8888/sitename</code></li>
        <li>Previous production: <code>https://old-domain.com</code></li>
    </ul>
</div>

</body>
</html>
