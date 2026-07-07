<?php
// Database import script for Railway deployment
require_once __DIR__ . '/../fe/db.php';

echo "DEBUG: db_host = {$db_host}\n";
echo "DEBUG: db_user = {$db_user}\n";
echo "DEBUG: db_name = {$db_name}\n";
echo "Connecting to: {$db_host}:{$db_port}...\n";
$conn = new mysqli($db_host, $db_user, $db_pass, 'mysql', $db_port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "✓ Connected\n";
echo "Creating database: {$db_name}\n";
$conn->query("CREATE DATABASE IF NOT EXISTS `{$db_name}`");
$conn->select_db($db_name);

echo "Reading SQL file...\n";
$sql = file_get_contents(__DIR__ . '/../database.sql');

// Remove DELIMITER lines and MySQL version-specific syntax
$sql = preg_replace('/^\s*DELIMITER\s+[^\n]*\n/m', '', $sql);
$sql = preg_replace('/\/\*!\d+\s+/m', '', $sql);  // Remove /*!50003 style comments
$sql = preg_replace('/\s+\*\//m', ' ', $sql);      // Remove closing */

echo "Executing SQL (this may take a minute)...\n";
$count = 0;
$errors = [];
$skipped = 0;

// Split by semicolon
$statements = explode(';', $sql);

foreach ($statements as $statement) {
    $statement = trim($statement);

    // Skip empty statements or comment-only lines
    if (empty($statement) || preg_match('/^\s*\/\*.*\*\/\s*$/', $statement)) {
        $skipped++;
        continue;
    }

    // Execute statement
    if ($conn->query($statement) === false) {
        $errors[] = $conn->error;
    } else {
        $count++;
    }
}

if (!empty($errors)) {
    echo "⚠ Import completed with " . count($errors) . " error(s), " . $count . " successful, " . $skipped . " skipped\n";
    foreach (array_slice($errors, 0, 3) as $error) {
        echo "  - $error\n";
    }
} else {
    echo "✓ Database imported successfully! ({$count} statements executed, {$skipped} skipped)\n";
}

$conn->close();
echo "Done!\n";
