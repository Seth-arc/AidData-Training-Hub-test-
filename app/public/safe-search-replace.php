<?php
/**
 * Serialization-safe search and replace
 * Based on WordPress's own search-replace logic
 */

header('Content-Type: text/html; charset=utf-8');

$db_host = 'mysql.railway.internal';
$db_name = 'railway';
$db_user = 'root';
$db_pass = 'mopzmAdFBAdfFWjwhNcznxdyZzNuoFNx';
$old_url = 'http://localhost:10004';
$new_url = 'https://aiddata-training-hub-test-production.up.railway.app';

/**
 * Recursive function to replace URLs in serialized data
 */
function safe_replace_serialized($data, $from, $to) {
    if (is_string($data) && ($unserialized = @unserialize($data)) !== false) {
        $data = safe_replace_serialized($unserialized, $from, $to);
        $data = serialize($data);
    } elseif (is_array($data)) {
        $_tmp = array();
        foreach ($data as $key => $value) {
            $_tmp[$key] = safe_replace_serialized($value, $from, $to);
        }
        $data = $_tmp;
        unset($_tmp);
    } elseif (is_object($data)) {
        $_tmp = clone $data;
        foreach ($_tmp as $key => $value) {
            $_tmp->$key = safe_replace_serialized($value, $from, $to);
        }
        $data = $_tmp;
        unset($_tmp);
    } else {
        if (is_string($data)) {
            $data = str_replace($from, $to, $data);
        }
    }
    return $data;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Serialization-Safe Search & Replace</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        button { padding: 10px 20px; background: #dc3545; color: white; border: none; cursor: pointer; font-size: 16px; }
        button:hover { background: #c82333; }
    </style>
</head>
<body>
    <h1>Serialization-Safe Search & Replace</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='warning'>Connection failed</div>";
    exit;
}

echo "<div class='success'>✓ Connected to database</div>";

echo "<div class='warning'>";
echo "<h3>⚠️ This will perform a DEEP search & replace</h3>";
echo "<p><strong>OLD URL:</strong> <code>$old_url</code></p>";
echo "<p><strong>NEW URL:</strong> <code>$new_url</code></p>";
echo "<p>This properly handles serialized data (unlike simple REPLACE).</p>";

if (!isset($_POST['confirm'])) {
    echo "<form method='post'>";
    echo "<button type='submit' name='confirm' onclick='return confirm(\"This will modify your database. Continue?\");'>Start Deep Search & Replace</button>";
    echo "</form>";
    echo "</div>";
} else {
    echo "</div>";

    echo "<h2>Processing...</h2>";

    // Tables and columns to process
    $tables_columns = [
        'wp_options' => ['option_value'],
        'wp_posts' => ['post_content', 'post_excerpt', 'guid'],
        'wp_postmeta' => ['meta_value'],
        'wp_comments' => ['comment_content', 'comment_author_url'],
        'wp_commentmeta' => ['meta_value'],
        'wp_users' => ['user_url'],
        'wp_usermeta' => ['meta_value']
    ];

    $total_updated = 0;

    foreach ($tables_columns as $table => $columns) {
        // Check if table exists
        $check = $mysqli->query("SHOW TABLES LIKE '$table'");
        if ($check->num_rows == 0) {
            continue;
        }

        echo "<div class='info'>Processing <strong>$table</strong>...";

        // Get primary key
        $pk_result = $mysqli->query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
        $primary_key = 'ID';
        if ($pk_result && $row = $pk_result->fetch_assoc()) {
            $primary_key = $row['Column_name'];
        }

        $table_updates = 0;

        foreach ($columns as $column) {
            // Get all rows for this column
            $result = $mysqli->query("SELECT $primary_key, $column FROM $table");

            if (!$result) {
                continue;
            }

            while ($row = $result->fetch_assoc()) {
                $id = $row[$primary_key];
                $original = $row[$column];

                if (empty($original)) {
                    continue;
                }

                // Try to replace (handles both serialized and non-serialized)
                $updated = safe_replace_serialized($original, $old_url, $new_url);

                // Only update if changed
                if ($updated !== $original) {
                    $stmt = $mysqli->prepare("UPDATE $table SET $column = ? WHERE $primary_key = ?");
                    $stmt->bind_param('si', $updated, $id);
                    if ($stmt->execute()) {
                        $table_updates++;
                    }
                }
            }
        }

        if ($table_updates > 0) {
            echo " <strong style='color: green;'>✓ $table_updates rows updated</strong>";
            $total_updated += $table_updates;
        } else {
            echo " (no changes)";
        }

        echo "</div>";
    }

    echo "<div class='success'>";
    echo "<h2>✓ Deep Search & Replace Complete!</h2>";
    echo "<p><strong>Total rows updated:</strong> $total_updated</p>";

    // Also update the main options directly
    echo "<h3>Updating Core WordPress Options...</h3>";
    $mysqli->query("UPDATE wp_options SET option_value = '$new_url' WHERE option_name = 'siteurl'");
    $mysqli->query("UPDATE wp_options SET option_value = '$new_url' WHERE option_name = 'home'");
    echo "<p>✓ Core options updated</p>";

    // Clear any cached redirects
    $mysqli->query("DELETE FROM wp_options WHERE option_name LIKE '%_transient_%'");
    echo "<p>✓ Cleared transients</p>";

    echo "<hr>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Clear your browser cache and cookies</li>";
    echo "<li><a href='$new_url/wp-login.php' target='_blank'>Try logging in again</a></li>";
    echo "<li>If still having issues, try incognito/private browsing mode</li>";
    echo "</ol>";
    echo "</div>";
}

$mysqli->close();

?>

</body>
</html>
