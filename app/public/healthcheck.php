<?php
/**
 * Railway healthcheck endpoint
 * Returns 200 OK without loading WordPress
 */

// Check if PHP-FPM is running by verifying we can execute PHP
http_response_code(200);
header('Content-Type: text/plain');
echo 'OK';
exit;
