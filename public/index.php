<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

set_exception_handler(function($e) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'error' => 'Unhandled exception',
    'message' => $e->getMessage(),
    'file' => $e->getFile(),
    'line' => $e->getLine(),
  ], JSON_UNESCAPED_UNICODE);
  exit;
});

register_shutdown_function(function() {
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
      'error' => 'Fatal error',
      'message' => $e['message'],
      'file' => $e['file'],
      'line' => $e['line'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
  }
});

session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/PTUD_Final',
  'httponly' => true,
  'samesite' => 'Lax',
]);

session_start();

require __DIR__ . '/../app/Database.php';

header('Content-Type: application/json; charset=utf-8');

function json($data, int $code = 200): void {
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$prefix = '/PTUD_Final/public';
if (str_starts_with($path, $prefix)) {
  $path = substr($path, strlen($prefix));
}
$path = $path ?: '/';

function doc_json_body(): array {
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

require __DIR__ . '/../app/routes.php';
