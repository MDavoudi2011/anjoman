<?php // Filepath: /api_update_progress.php
session_start();

// اطمینان از اینکه کاربر لاگین کرده
if (!isset($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// خواندن دیتای ارسالی
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['videoId'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No videoId provided']);
    exit;
}

$videoId = $data['videoId'];
$userId = $_SESSION['user']['username'];
$userGroup = $_SESSION['user']['group'];

$users_file_path = 'data/users.json';
$users_json = @file_get_contents($users_file_path);
if ($users_json === false) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Could not read users file']);
    exit;
}

$users = json_decode($users_json, true);
$user_found = false;
$data_changed = false;

// پیدا کردن کاربر و آپدیت لیست ویدئوهای دیده شده
foreach ($users as $i => $user) {
    if ($user['username'] === $userId && $user['group'] === $userGroup) {
        $user_found = true;
        if (!in_array($videoId, $user['watchedVideos'])) {
            $users[$i]['watchedVideos'][] = $videoId;
            $_SESSION['user']['watchedVideos'] = $users[$i]['watchedVideos']; // آپدیت سشن
            $data_changed = true;
        }
        break;
    }
}

if (!$user_found) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'User not found in JSON file']);
    exit;
}

// اگر دیتایی تغییر کرده بود، فایل جیسان را بازنویسی کن
if ($data_changed) {
    // بازنویسی فایل
    if (file_put_contents($users_file_path, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Could not write to users file']);
        exit;
    }
}

// ارسال پاسخ موفقیت‌آمیز
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>