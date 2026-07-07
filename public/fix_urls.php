<?php
require_once __DIR__ . '/../fe/db.php';
$conn = db_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $table = $row[0];
    $cols = $conn->query("SHOW COLUMNS FROM `{$table}` WHERE Type LIKE '%varchar%' OR Type LIKE '%text%'");
    while ($col = $cols->fetch_assoc()) {
        $colName = $col['Field'];
        // Fix any remaining localhost URLs
        $check = $conn->query("SELECT COUNT(*) as c FROM `{$table}` WHERE `{$colName}` LIKE '%localhost%'");
        $count = $check->fetch_assoc()['c'];
        if ($count > 0) {
            $conn->query("UPDATE `{$table}` SET `{$colName}` = REGEXP_REPLACE(`{$colName}`, 'https?://localhost/ecom_clothes_web', '') WHERE `{$colName}` LIKE '%localhost%'");
            echo "✓ Fixed {$table}.{$colName}: {$conn->affected_rows} rows\n";
        }
    }
}

$conn->close();
echo "Done!\n";
