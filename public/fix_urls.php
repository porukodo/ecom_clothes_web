<?php
require_once __DIR__ . '/../fe/db.php';
$conn = db_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

$old = 'http://localhost/ecom_clothes_web';
$new = '';

$tables = [
    ['anh_san_pham', 'url_anh'],
    ['san_pham', 'anh_chinh'],
    ['banner', 'url_anh'],
];

foreach ($tables as [$table, $col]) {
    $result = $conn->query("SHOW TABLES LIKE '{$table}'");
    if ($result->num_rows === 0) continue;

    $sql = "UPDATE `{$table}` SET `{$col}` = REPLACE(`{$col}`, '{$old}', '{$new}') WHERE `{$col}` LIKE '%localhost%'";
    $conn->query($sql);
    echo "✓ Fixed {$table}.{$col}: {$conn->affected_rows} rows updated\n";
}

// Also check all tables dynamically
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $table = $row[0];
    $cols = $conn->query("SHOW COLUMNS FROM `{$table}` WHERE Type LIKE '%varchar%' OR Type LIKE '%text%'");
    while ($col = $cols->fetch_assoc()) {
        $colName = $col['Field'];
        $check = $conn->query("SELECT COUNT(*) as c FROM `{$table}` WHERE `{$colName}` LIKE '%localhost/ecom_clothes_web%'");
        $count = $check->fetch_assoc()['c'];
        if ($count > 0) {
            $conn->query("UPDATE `{$table}` SET `{$colName}` = REPLACE(`{$colName}`, '{$old}', '{$new}') WHERE `{$colName}` LIKE '%localhost%'");
            echo "✓ Fixed {$table}.{$colName}: {$conn->affected_rows} rows\n";
        }
    }
}

$conn->close();
echo "Done!\n";
