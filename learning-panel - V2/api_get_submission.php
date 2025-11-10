<?php
session_start();
define('APP_ACCESS', true);
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = Config::getUser($_SESSION['user']['username']);
if (!$user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$exercise_id = $_GET['exercise_id'] ?? null;
if (!$exercise_id || empty(trim($exercise_id))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Exercise ID required']);
    exit;
}

// اینجا دیگه عدد نمی‌کنیم — رشته بمونه!
$exercise_id = trim($exercise_id);

try {
    $stmt = Config::db()->prepare("
        SELECT code FROM exercise_submissions 
        WHERE user_id = ? AND exercise_id = ? 
        ORDER BY submitted_at DESC LIMIT 1
    ");
    $stmt->execute([$user['id'], $exercise_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'code' => $row ? $row['code'] : ''
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}