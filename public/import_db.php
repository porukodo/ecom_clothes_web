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

// Remove DELIMITER statements (MySQL CLI-only syntax)
$sql = preg_replace('/DELIMITER\s+[^;\n]+;/i', '', $sql);
$sql = preg_replace('/DELIMITER\s+;/i', '', $sql);

echo "Executing SQL (this may take a minute)...\n";
$count = 0;
$errors = [];

// Split by semicolon but be careful with quoted strings
$statements = array_filter(array_map('trim', preg_split('/;(?=(?:[^\']*\'[^\']*\')*[^\'"]*$)/', $sql)));

foreach ($statements as $statement) {
    if (empty($statement)) continue;

    if ($conn->query($statement) === false) {
        $errors[] = "Error: " . $conn->error . "\n  Statement: " . substr($statement, 0, 100) . "...\n";
    } else {
        $count++;
    }
}

if (!empty($errors)) {
    echo "⚠ Import completed with some errors:\n";
    foreach ($errors as $error) {
        echo $error;
    }
} else {
    echo "✓ Database imported successfully! ({$count} statements executed)\n";
}

$conn->close();
echo "Done!\n";
