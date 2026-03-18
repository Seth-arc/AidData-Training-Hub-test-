<?php
/**
 * Railway healthcheck endpoint.
 * Verifies PHP runtime and database connectivity without loading WordPress.
 */

header('Content-Type: text/plain');

$dbHost = getenv('DB_HOST') ?: 'mysql:3306';
$dbUser = getenv('DB_USER') ?: '';
$dbPass = getenv('DB_PASSWORD') ?: '';
$dbName = getenv('DB_NAME') ?: '';

$dbHostParts = explode(':', $dbHost, 2);
$dbHostname = $dbHostParts[0] !== '' ? $dbHostParts[0] : 'mysql';
$dbPort = isset($dbHostParts[1]) ? (int) $dbHostParts[1] : 3306;

$mysqli = mysqli_init();
if (!$mysqli) {
	http_response_code(500);
	echo 'UNHEALTHY: mysqli init failed';
	exit;
}

mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
$connected = @mysqli_real_connect($mysqli, $dbHostname, $dbUser, $dbPass, $dbName, $dbPort);

if (!$connected) {
	http_response_code(500);
	echo 'UNHEALTHY: db connection failed';
	exit;
}

mysqli_close($mysqli);

http_response_code(200);
echo 'OK';
exit;
