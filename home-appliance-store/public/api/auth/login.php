<?php
// api/auth/login.php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
if (!$email || !$password) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing fields']);
  exit;
}
// TODO: verify credentials against DB and return token/session
echo json_encode(['status' => 'ok', 'message' => 'Login endpoint (stub)']);
