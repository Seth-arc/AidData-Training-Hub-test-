<?php
/**
 * Aggressive error catching to see what's really happening
 */

// Set up error logging to a variable
$errors = [];

set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$errors) {
    $errors[] = [
        'type' => 'error',
        'errno' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    return false;
});

set_exception_handler(function($exception) use (&$errors) {
    $errors[] = [
        'type' => 'exception',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
});

register_shutdown_function(function() use (&$errors) {
    $error = error_get_last();
    if ($error !== null) {
        $errors[] = [
            'type' => 'shutdown',
            'errno' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ];
    }

    // Output all captured errors
    if (!empty($errors)) {
        echo "<h2>Captured Errors During WordPress Load:</h2>";
        echo "<pre style='background: #ffe0e0; padding: 20px; white-space: pre-wrap;'>";
        print_r($errors);
        echo "</pre>";
    }
});

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>WordPress Error Capture</title>
    <style>
        body { font-family: monospace; margin: 20px; }
        .step { background: #d4edda; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>WordPress Error Capture Test</h1>

<?php

echo "<div class='step'>Starting WordPress load with error capture...</div>\n";
flush();

// Override the wp_die() function before WordPress loads
function wp_die($message = '', $title = '', $args = []) {
    echo "<div style='background: #ffe0e0; padding: 20px; margin: 10px;'>";
    echo "<h2>wp_die() was called:</h2>";
    echo "<strong>Message:</strong><br>";
    echo "<pre>" . htmlspecialchars(print_r($message, true)) . "</pre>";
    echo "<strong>Title:</strong> " . htmlspecialchars($title) . "<br>";
    echo "<strong>Args:</strong><br>";
    echo "<pre>" . htmlspecialchars(print_r($args, true)) . "</pre>";
    echo "</div>";

    // Show backtrace
    echo "<h3>Call Stack:</h3>";
    echo "<pre>" . htmlspecialchars(print_r(debug_backtrace(), true)) . "</pre>";
    exit;
}

// Prevent WordPress from using its own error handler
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);

echo "<div class='step'>Loading WordPress...</div>\n";
flush();

try {
    // Load WordPress
    require_once(__DIR__ . '/wp-load.php');

    echo "<div class='step'>✓ WordPress loaded successfully!</div>\n";

    global $wpdb;
    if (isset($wpdb)) {
        echo "<div class='step'>✓ wpdb object exists</div>\n";

        // Check for database errors
        if (!empty($wpdb->error)) {
            echo "<div style='background: #ffe0e0; padding: 10px;'>";
            echo "wpdb error: " . htmlspecialchars($wpdb->error);
            echo "</div>";
        }

        // Check last error
        if (!empty($wpdb->last_error)) {
            echo "<div style='background: #ffe0e0; padding: 10px;'>";
            echo "wpdb last_error: " . htmlspecialchars($wpdb->last_error);
            echo "</div>";
        }

        // Try a query
        $result = $wpdb->get_var("SELECT 1");
        echo "<div class='step'>Query result: " . htmlspecialchars($result) . "</div>\n";
    }

} catch (Throwable $e) {
    echo "<div style='background: #ffe0e0; padding: 20px;'>";
    echo "<h2>Exception caught:</h2>";
    echo "<strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

?>

</body>
</html>
