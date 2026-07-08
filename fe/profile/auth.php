<?php
/**
 * Shared auth guard for all profile/* pages.
 * Replaces the broken curl-to-self pattern that fails on Railway.
 * Requires session to already be started by the caller.
 *
 * Sets $user (id, email, ho_ten, vai_tro, trang_thai, ngay_sinh, so_dien_thoai)
 * and $API_BASE for JS fetch calls.
 */

if (empty($_SESSION['nguoi_dung_id'])) {
    header('Location: ../login.php');
    exit();
}

$API_BASE = '/public';

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_once dirname(__DIR__, 2) . '/app/Database.php';
require_once dirname(__DIR__, 2) . '/app/models/User.php';

$user = User::layHoSoCongKhai((int)$_SESSION['nguoi_dung_id']);

if (!$user) {
    // User deleted or deactivated — clear session and redirect
    session_destroy();
    header('Location: ../login.php');
    exit();
}
