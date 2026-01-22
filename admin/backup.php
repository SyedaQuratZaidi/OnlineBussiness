<?php
// admin/backup.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
require_once '../config.php';

// Only allow download action
if (!isset($_GET['download'])) {
    echo "No download specified.";
    exit();
}

$dbHost = defined('DB_HOST') ? DB_HOST : 'localhost';
$dbUser = defined('DB_USER') ? DB_USER : 'root';
$dbPass = defined('DB_PASS') ? DB_PASS : '';
$dbName = defined('DB_NAME') ? DB_NAME : '';

$filename = sprintf('%s_%s.sql', $dbName ?: 'database', date('Ymd_His'));

// Try common mysqldump locations
$candidates = [
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    '/usr/bin/mysqldump',
    '/usr/local/mysql/bin/mysqldump',
    'mysqldump'
];
$dumpPath = null;
foreach ($candidates as $c) {
    // if it's just 'mysqldump' assume available in PATH
    if ($c === 'mysqldump') {
        $out = null;
        $ret = null;
        @exec('mysqldump --version 2>&1', $out, $ret);
        if ($ret === 0) { $dumpPath = 'mysqldump'; break; }
        continue;
    }
    if (file_exists($c) && is_executable($c)) {
        $dumpPath = $c;
        break;
    }
}

if ($dumpPath === null) {
    header('Content-Type: text/plain');
    echo "mysqldump not found on server. Backup via mysqldump is required for full SQL export.\n";
    echo "You can install mysqldump or run the SQL export manually from the database.\n";
    exit();
}

// Build command
// Use --routines and --triggers to include them. Use --single-transaction for InnoDB safety.
$cmd = escapeshellcmd($dumpPath) . ' --host=' . escapeshellarg($dbHost) . ' --user=' . escapeshellarg($dbUser) . ' --password=' . escapeshellarg($dbPass) . ' --default-character-set=utf8mb4 --routines --triggers --single-transaction --quick ' . escapeshellarg($dbName);

// Send headers
header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Flush output buffer
while (ob_get_level()) ob_end_clean();

// Execute and stream
$proc = popen($cmd . ' 2>&1', 'r');
if (!$proc) {
    echo "Failed to run mysqldump command.";
    exit();
}

// Stream output directly
while (!feof($proc)) {
    $buffer = fread($proc, 8192);
    echo $buffer;
    flush();
}

pclose($proc);
exit();
