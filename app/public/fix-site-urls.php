<?php
/**
 * Fix empty siteurl and home options
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
    <title>Fix WordPress URLs</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Fix WordPress Site URLs</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='error'>Connection failed: " . htmlspecialchars($mysqli->connect_error) . "</div>";
    exit;
}

echo "<div class='success'>✓ Connected to database</div>";

// Check current URLs
echo "<h2>Current URLs in Database</h2>";
$result = $mysqli->query("SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home')");

echo "<div class='info'>";
$current_urls = [];
while ($row = $result->fetch_assoc()) {
    $current_urls[$row['option_name']] = $row['option_value'];
    $value = empty($row['option_value']) ? '<em style="color: red;">(EMPTY - This is the problem!)</em>' : htmlspecialchars($row['option_value']);
    echo "<strong>{$row['option_name']}:</strong> $value<br>";
}
echo "</div>";

echo "<div class='info'>";
echo "<strong>Correct URL should be:</strong> <code>$correct_url</code>";
echo "</div>";

if (!isset($_POST['fix_urls'])) {
    echo "<div class='info'>";
    echo "<p><strong>The empty URLs are causing WordPress to fail!</strong></p>";
    echo "<p>Click the button below to set the correct URLs:</p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_urls'>Fix URLs Now</button>";
    echo "</form>";
    echo "</div>";
} else {
    echo "<h2>Fixing URLs...</h2>";

    // Update siteurl
    $stmt = $mysqli->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'siteurl'");
    $stmt->bind_param('s', $correct_url);
    $result1 = $stmt->execute();

    // Update home
    $stmt = $mysqli->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'home'");
    $stmt->bind_param('s', $correct_url);
    $result2 = $stmt->execute();

    if ($result1 && $result2) {
        echo "<div class='success'>";
        echo "<h3>✓ URLs Updated Successfully!</h3>";
        echo "<p><strong>siteurl:</strong> $correct_url</p>";
        echo "<p><strong>home:</strong> $correct_url</p>";
        echo "</div>";

        echo "<div class='info'>";
        echo "<h3>Next Steps:</h3>";
        echo "<ol>";
        echo "<li><strong>Try accessing the homepage:</strong><br>";
        echo "<a href='$correct_url' target='_blank' style='font-size: 18px;'>$correct_url</a></li>";
        echo "<li>If it works, try logging into admin:<br>";
        echo "<a href='$correct_url/wp-admin/' target='_blank' style='font-size: 18px;'>$correct_url/wp-admin/</a></li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div class='error'>Failed to update URLs: " . htmlspecialchars($mysqli->error) . "</div>";
    }

    // Verify the update
    echo "<h2>Verification</h2>";
    $result = $mysqli->query("SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home')");
    echo "<div class='info'>";
    while ($row = $result->fetch_assoc()) {
        echo "<strong>{$row['option_name']}:</strong> " . htmlspecialchars($row['option_value']) . "<br>";
    }
    echo "</div>";
}

$mysqli->close();

?>

</body>
</html>
