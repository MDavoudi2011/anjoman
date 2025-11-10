<?php
session_start();
define('APP_ACCESS', true);
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['username'])) {
    echo json_encode(['success' => false, 'message' => 'لطفاً وارد شوید']);
    exit;
}

$user = Config::getUser($_SESSION['user']['username']);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'کاربر یافت نشد']);
    exit;
}

$exercise_id = $_POST['exercise_id'] ?? null;
$code = trim($_POST['code'] ?? '');

if (!$exercise_id || $code === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'تمرین یا کد خالی است']);
    exit;
}

// exercise_id رشته بمونه — مثل a_prac_1
$exercise_id = trim($exercise_id);

try {
    $pdo = Config::db();
    $stmt = $pdo->prepare("
        INSERT INTO exercise_submissions (user_id, exercise_id, code, status) 
        VALUES (?, ?, ?, 'pending')
        ON DUPLICATE KEY UPDATE 
            code = VALUES(code), 
            status = 'pending', 
            submitted_at = NOW()
    ");
    $stmt->execute([$user['id'], $exercise_id, $code]);

    echo json_encode(['success' => true, 'message' => 'کد با موفقیت ارسال شد!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطای سرور']);
}