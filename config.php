<?php

if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
	define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
	define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
	define('DB_NAME', 'eproject');
}

function db(): mysqli {
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$conn->set_charset('utf8mb4');
	return $conn;
}

/**
 * Escape HTML safely.
 */
function e(string $value): string {
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


