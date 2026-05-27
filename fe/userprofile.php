<?php
// userprofile.php - Main entry point, redirect to info page
if (session_status() === PHP_SESSION_NONE) session_start();

$API_BASE = 'http://localhost/PTUD_Final/public';
$cookie = session_name() . '=' . session_id();
session_write_close();

// Check authentication
$ch = curl_init($API_BASE . '/api/auth/me');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_COOKIE => $cookie,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_CONNECTTIMEOUT => 3,
]);

$res = curl_exec($ch); 
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($res ?: '', true);

if ($http !== 200 || !($data['ok'] ?? false) || !($data['authenticated'] ?? false)) {
    header('Location: login.php');
    exit();
}

// Redirect to profile info page
header('Location: profile/info.php');
exit();
?>