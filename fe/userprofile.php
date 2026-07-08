<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['nguoi_dung_id'])) {
    header('Location: login.php');
    exit();
}

header('Location: profile/info.php');
exit();
?>