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

// Remove DELIMITER statements
$sql = preg_replace('/^\s*DELIMITER\s+[^\n]*\n/m', '', $sql);

echo "Executing SQL (this may take a minute)...\n";
$count = 0;
$errors = [];

// Simple split by semicolon
$statements = explode(';', $sql);

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (empty($statement)) continue;

    if ($conn->query($statement) === false) {
        $errors[] = "Error: " . $conn->error;
    } else {
        $count++;
    }
}

if (!empty($errors)) {
    echo "⚠ Import completed with " . count($errors) . " error(s):\n";
    foreach (array_slice($errors, 0, 5) as $error) {
        echo "  - $error\n";
    }
    if (count($errors) > 5) {
        echo "  ... and " . (count($errors) - 5) . " more\n";
    }
} else {
    echo "✓ Database imported successfully! ({$count} statements executed)\n";
}

$conn->close();
echo "Done!\n";
