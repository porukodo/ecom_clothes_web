<?php
// Database import script for Railway deployment
require_once __DIR__ . '/fe/db.php';

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
$sql = file_get_contents(__DIR__ . '/database.sql');

echo "Executing SQL (this may take a minute)...\n";
if ($conn->multi_query($sql)) {
    $count = 0;
    do {
        if ($conn->query_result === true) {
            $count++;
        }
    } while ($conn->next_result());
    echo "✓ Database imported successfully! ({$count} statements executed)\n";
} else {
    die("Error importing database: " . $conn->error . "\n");
}

$conn->close();
echo "Done!\n";
