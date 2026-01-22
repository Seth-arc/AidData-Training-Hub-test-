<?php
/**
 * Simple WordPress load test with full error reporting
 */

// Enable ALL error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Register error handler to catch everything
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<div style='background: #ffe0e0; padding: 10px; margin: 10px; border-left: 4px solid red;'>";
    echo "<strong>PHP Error ($errno):</strong> $errstr<br>";
    echo "<strong>File:</strong> $errfile<br>";
    echo "<strong>Line:</strong> $errline";
    echo "</div>";
    return false; // Don't suppress the error
});

// Register exception handler
set_exception_handler(function($exception) {
    echo "<div style='background: #ffe0e0; padding: 10px; margin: 10px; border-left: 4px solid red;'>";
    echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
    echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    echo "</div>";
});

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && $error['type'] === E_ERROR) {
        echo "<div style='background: #ffe0e0; padding: 10px; margin: 10px; border-left: 4px solid red;'>";
        echo "<strong>Fatal Error:</strong> {$error['message']}<br>";
        echo "<strong>File:</strong> {$error['file']}<br>";
        echo "<strong>Line:</strong> {$error['line']}";
        echo "</div>";
    }
});

?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple WordPress Load Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .success { background: #d4edda; padding: 10px; margin: 10px; border-left: 4px solid green; }
        .info { background: #d1ecf1; padding: 10px; margin: 10px; border-left: 4px solid #0c5460; }
    </style>
</head>
<body>
    <h1>Simple WordPress Load Test</h1>

    <div class="info">
        This page attempts to load WordPress normally and reports any errors that occur.
    </div>

    <div class="success">Starting WordPress load...</div>

<?php

echo "<div class='success'>Step 1: About to load WordPress via wp-blog-header.php...</div>";
flush();

// This is what index.php does - it just loads wp-blog-header.php
try {
    require_once(__DIR__ . '/wp-blog-header.php');
    echo "<div class='success'>✓ WordPress loaded successfully!</div>";
    echo "<div class='info'>WordPress Version: " . (defined('WP_VERSION') ? WP_VERSION : 'unknown') . "</div>";
} catch (Throwable $e) {
    echo "<div style='background: #ffe0e0; padding: 10px; margin: 10px; border-left: 4px solid red;'>";
    echo "<strong>Exception loading WordPress:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

?>

    <hr>
    <h2>Debug Info</h2>
    <div class="info">
        <strong>PHP Version:</strong> <?php echo phpversion(); ?><br>
        <strong>Memory Usage:</strong> <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?> MB<br>
        <strong>Peak Memory:</strong> <?php echo round(memory_get_peak_usage() / 1024 / 1024, 2); ?> MB
    </div>

</body>
</html>
