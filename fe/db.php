<?php
// Database connection config — reads from env vars (Railway) with local fallbacks
$db_host = getenv('MYSQLHOST') ?: 'localhost';
$db_user = getenv('MYSQLUSER') ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: '';
$db_name = getenv('MYSQLDATABASE') ?: 'ecom_clothes_web';
$db_port = (int)(getenv('MYSQLPORT') ?: 3306);
