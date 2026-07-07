<?php
// Database import script for Railway deployment
require_once __DIR__ . '/../fe/db.php';

echo "db_host = {$db_host}\n";
echo "db_name = {$db_name}\n";
echo "Connecting to: {$db_host}:{$db_port}...\n";
$conn = new mysqli($db_host, $db_user, $db_pass, 'mysql', $db_port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "✓ Connected\n";
$conn->query("CREATE DATABASE IF NOT EXISTS `{$db_name}`");
$conn->select_db($db_name);
$conn->set_charset('utf8mb4');
$conn->query("SET FOREIGN_KEY_CHECKS=0");
$conn->query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");

echo "Reading SQL file...\n";
$lines = file(__DIR__ . '/../database.sql', FILE_IGNORE_NEW_LINES);

echo "Parsing statements...\n";
$count = 0;
$errors = [];
$skipped = 0;
$statement = '';
$skip_block = false;

foreach ($lines as $line) {
    $trimmed = trim($line);

    // Skip empty lines and pure comment lines
    if ($trimmed === '' || str_starts_with($trimmed, '--')) {
        continue;
    }

    // Check for trigger/procedure/function in /*!...*/ lines too
    if (preg_match('/(TRIGGER|PROCEDURE|FUNCTION|EVENT)/i', $trimmed) &&
        (str_starts_with($trimmed, '/*!') || preg_match('/^(CREATE.*TRIGGER|CREATE.*PROCEDURE|CREATE.*FUNCTION|CREATE.*EVENT)/i', $trimmed))) {
        $skip_block = true;
    }

    // Skip DELIMITER lines
    if (str_starts_with($trimmed, 'DELIMITER')) {
        continue;
    }

    if ($skip_block) {
        if (str_ends_with($trimmed, ';;') || $trimmed === ';;') {
            $skip_block = false;
            $skipped++;
        }
        $statement = '';
        continue;
    }

    // Skip pure /*!...*/ lines (MySQL version comments)
    if (preg_match('/^\/\*!.*\*\/\s*$/', $trimmed)) {
        continue;
    }

    $statement .= $line . "\n";

    if (str_ends_with(rtrim($trimmed), ';')) {
        $statement = trim($statement);
        if (!empty($statement)) {
            if ($conn->query($statement) === false) {
                $errors[] = $conn->error . ' | ' . substr($statement, 0, 80);
            } else {
                $count++;
            }
        }
        $statement = '';
    }
}

echo "✓ Done! {$count} statements executed, {$skipped} blocks skipped\n";

if (!empty($errors)) {
    echo "⚠ " . count($errors) . " error(s):\n";
    foreach (array_slice($errors, 0, 5) as $e) {
        echo "  - $e\n";
    }
}

$conn->query("SET FOREIGN_KEY_CHECKS=1");
$conn->close();
echo "Finished!\n";
