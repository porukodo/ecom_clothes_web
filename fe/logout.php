<?php
declare(strict_types=1);

// Đồng bộ cookie path giống FE + BE
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/PTUD_Final',
  'httponly' => true,
  'samesite' => 'Lax',
]);

session_start();

$API_BASE = 'http://localhost/PTUD_Final/public';

// Lưu cookie session hiện tại để gửi cho API
$cookieName = session_name();
$sid = session_id();
$cookieHeader = $cookieName . '=' . $sid;

// ✅ QUAN TRỌNG: nhả session lock trước khi gọi API (tránh treo)
session_write_close();

// Gọi API backend /api/auth/dang-xuat
$ch = curl_init($API_BASE . '/api/auth/dang-xuat');
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_COOKIE => $cookieHeader,
  CURLOPT_CONNECTTIMEOUT => 3,
  CURLOPT_TIMEOUT => 8,
]);
curl_exec($ch);
curl_close($ch);

// Clear session FE + cookie
session_id($sid);
session_start();
$_SESSION = [];

if (ini_get("session.use_cookies")) {
  setcookie($cookieName, '', time() - 42000, '/PTUD_Final');
}
session_destroy();

// Redirect về home FE
header('Location: index.php');
exit;
