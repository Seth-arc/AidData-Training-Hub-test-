<?php
/**
 * Check what URLs are actually stored in the database
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
    <title>Check Stored URLs</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
    </style>
</head>
<body>
    <h1>Database URL Check</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='error'>Connection failed</div>";
    exit;
}

// Get siteurl and home
$result = $mysqli->query("SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home')");

echo "<h2>URLs Stored in Database</h2>";
$urls = [];
while ($row = $result->fetch_assoc()) {
    $urls[$row['option_name']] = $row['option_value'];

    $value = htmlspecialchars($row['option_value']);
    $has_protocol = (strpos($row['option_value'], 'http://') === 0 || strpos($row['option_value'], 'https://') === 0);

    if (!$has_protocol) {
        echo "<div class='error'>";
        echo "<strong>{$row['option_name']}:</strong> $value<br>";
        echo "❌ <strong>MISSING PROTOCOL! This is the problem.</strong>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "<strong>{$row['option_name']}:</strong> $value";
        echo "</div>";
    }
}

// Show what it should be
$correct_url = 'https://aiddata-training-hub-test-production.up.railway.app';
echo "<div class='info'>";
echo "<strong>Correct URL should be:</strong> <code>$correct_url</code>";
echo "</div>";

// Offer to fix
if (!empty($urls)) {
    $needs_fix = false;
    foreach ($urls as $name => $value) {
        if (strpos($value, 'http://') !== 0 && strpos($value, 'https://') !== 0) {
            $needs_fix = true;
            break;
        }
    }

    if ($needs_fix) {
        echo "<div class='warning'>";
        echo "<h3>Fix Required</h3>";
        echo "<p>The URLs are missing the protocol (https://). This causes WordPress to generate broken admin URLs.</p>";

        if (!isset($_POST['fix_protocol'])) {
            echo "<form method='post'>";
            echo "<button type='submit' name='fix_protocol' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;'>Fix URLs - Add HTTPS Protocol</button>";
            echo "</form>";
        } else {
            // Fix the URLs
            echo "<h3>Fixing URLs...</h3>";

            $stmt = $mysqli->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'siteurl'");
            $stmt->bind_param('s', $correct_url);
            $r1 = $stmt->execute();

            $stmt = $mysqli->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'home'");
            $stmt->bind_param('s', $correct_url);
            $r2 = $stmt->execute();

            if ($r1 && $r2) {
                echo "<div class='success'>";
                echo "<h3>✓ URLs Fixed!</h3>";
                echo "<p>Both siteurl and home now have the correct HTTPS protocol.</p>";
                echo "<p><strong>Now try:</strong></p>";
                echo "<ul>";
                echo "<li><a href='$correct_url/wp-admin/'>Admin Dashboard</a></li>";
                echo "<li><a href='$correct_url/wp-login.php'>Login Page</a></li>";
                echo "</ul>";
                echo "</div>";
            } else {
                echo "<div class='error'>Failed to update URLs</div>";
            }
        }

        echo "</div>";
    }
}

$mysqli->close();

?>

</body>
</html>
