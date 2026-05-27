<?php
ini_set('session.cookie_path', '/PTUD_Final');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');

// nếu không dùng HTTPS
ini_set('session.cookie_secure', '0');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
