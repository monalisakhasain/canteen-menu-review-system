<?php
/**
 * config/db.php — Database connection
 * Uses MySQLi.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Default WAMP username
define('DB_PASS', '');           // Default WAMP password (empty)
define('DB_NAME', 'canteen_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:2rem;background:#fde8ec;color:#c0404f;border-radius:12px;margin:2rem;">
        <strong>⚠️ Database Connection Failed</strong><br><br>
        Error: ' . htmlspecialchars($conn->connect_error) . '<br><br>
        <small>Please ensure WAMP is running and the database <em>canteen_db</em> exists.<br>
        See the README for setup instructions.</small>
    </div>');
}

// Set charset
$conn->set_charset('utf8mb4');
?>
