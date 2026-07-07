<?php
require_once __DIR__ . '/../fe/db.php';
$conn = db_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

$r = $conn->query("SELECT url_anh FROM anh_san_pham LIMIT 5");
echo "anh_san_pham.url_anh samples:\n";
while ($row = $r->fetch_row()) echo "  " . $row[0] . "\n";

$r = $conn->query("SHOW TABLES LIKE 'banner'");
if ($r->num_rows > 0) {
    $r = $conn->query("SELECT * FROM banner LIMIT 3");
    echo "\nbanner table:\n";
    while ($row = $r->fetch_assoc()) echo "  " . json_encode($row) . "\n";
}

$conn->close();
