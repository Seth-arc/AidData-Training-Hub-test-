<?php
/**
 * Reset WordPress admin password
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
    <title>Reset WordPress Password</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .info { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .success { background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid green; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid orange; }
        .error { background: #ffe0e0; padding: 15px; margin: 10px 0; border-left: 4px solid red; }
        input { padding: 8px; width: 300px; font-family: monospace; margin: 5px 0; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; margin: 5px; }
        button:hover { background: #0056b3; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Reset WordPress Password</h1>

<?php

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "<div class='error'>Connection failed</div>";
    exit;
}

echo "<div class='success'>✓ Connected to database</div>";

// Show all users
echo "<h2>WordPress Users</h2>";
$result = $mysqli->query("SELECT ID, user_login, user_email, display_name FROM wp_users ORDER BY ID");

if ($result && $result->num_rows > 0) {
    echo "<div class='info'>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Display Name</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['user_login']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['user_email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['display_name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "<div class='error'>No users found in database!</div>";
}

// Handle password reset
if (isset($_POST['reset_password']) && !empty($_POST['username']) && !empty($_POST['new_password'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];

    echo "<h2>Resetting Password...</h2>";

    // Check if user exists
    $stmt = $mysqli->prepare("SELECT ID FROM wp_users WHERE user_login = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<div class='error'>User not found: " . htmlspecialchars($username) . "</div>";
    } else {
        $user = $result->fetch_assoc();
        $user_id = $user['ID'];

        // Hash the password using WordPress's method
        require_once(__DIR__ . '/wp-includes/class-phpass.php');
        $wp_hasher = new PasswordHash(8, true);
        $hashed_password = $wp_hasher->HashPassword($new_password);

        // Update the password
        $stmt = $mysqli->prepare("UPDATE wp_users SET user_pass = ? WHERE ID = ?");
        $stmt->bind_param('si', $hashed_password, $user_id);

        if ($stmt->execute()) {
            echo "<div class='success'>";
            echo "<h3>✓ Password Reset Successfully!</h3>";
            echo "<p><strong>Username:</strong> " . htmlspecialchars($username) . "</p>";
            echo "<p><strong>New Password:</strong> " . htmlspecialchars($new_password) . "</p>";
            echo "<hr>";
            echo "<p><strong>Important:</strong> First deactivate the WP-CASsify plugin:</p>";
            echo "<p><a href='/deactivate-cassify.php' style='padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; display: inline-block;'>Deactivate WP-CASsify</a></p>";
            echo "<p>Then try logging in:</p>";
            echo "<p><a href='/wp-login.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; display: inline-block;'>Go to Login Page</a></p>";
            echo "</div>";
        } else {
            echo "<div class='error'>Failed to update password: " . htmlspecialchars($mysqli->error) . "</div>";
        }
    }
}

// Show reset form if not submitted
if (!isset($_POST['reset_password'])) {
    echo "<div class='warning'>";
    echo "<h2>Reset Password</h2>";
    echo "<p>Enter the username and new password below:</p>";
    echo "<form method='post'>";
    echo "<p><label>Username:<br><input type='text' name='username' placeholder='Enter username' required></label></p>";
    echo "<p><label>New Password:<br><input type='text' name='new_password' placeholder='Enter new password' required></label></p>";
    echo "<p><small>Note: Password will be shown in plain text for you to copy. Change it after logging in.</small></p>";
    echo "<button type='submit' name='reset_password'>Reset Password</button>";
    echo "</form>";
    echo "</div>";
}

$mysqli->close();

?>

<hr>
<div class="info">
    <h3>After resetting your password:</h3>
    <ol>
        <li><strong>First:</strong> <a href="/deactivate-cassify.php">Deactivate WP-CASsify plugin</a> (causing login issues)</li>
        <li><strong>Then:</strong> Go to the <a href="/wp-login.php">login page</a></li>
        <li>Use your username and new password</li>
        <li><strong>Important:</strong> Change your password after logging in for security</li>
    </ol>
</div>

</body>
</html>
