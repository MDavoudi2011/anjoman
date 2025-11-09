<?php
// Filepath: /api_update_progress.php
session_start();

header('Content-Type: application/json');

// بررسی احراز هویت
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'error' => 'Authentication required',
        'message' => 'کاربر وارد سیستم نشده است'
    ]);
    exit;
}

// خواندن و اعتبارسنجی داده‌های ورودی
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON',
        'message' => 'فرمت داده‌های ارسالی نامعتبر است'
    ]);
    exit;
}

if (!isset($data['videoId']) || empty($data['videoId'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing videoId',
        'message' => 'شناسه ویدئو ارسال نشده است'
    ]);
    exit;
}

$videoId = trim($data['videoId']);
$userId = $_SESSION['user']['username'];
$userGroup = $_SESSION['user']['group'];

// مسیر فایل کاربران
$users_file_path = __DIR__ . '/data/users.json';

// بررسی وجود فایل
if (!file_exists($users_file_path)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Users file not found',
        'message' => 'فایل اطلاعات کاربران یافت نشد'
    ]);
    exit;
}

// قفل کردن فایل برای جلوگیری از مشکلات همزمانی
$file_handle = fopen($users_file_path, 'r+');
if (!$file_handle) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Could not open users file',
        'message' => 'خطا در باز کردن فایل کاربران'
    ]);
    exit;
}

// قفل انحصاری
if (!flock($file_handle, LOCK_EX)) {
    fclose($file_handle);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Could not lock file',
        'message' => 'خطا در قفل کردن فایل'
    ]);
    exit;
}

// خواندن محتوای فایل
$users_json = stream_get_contents($file_handle);
$users = json_decode($users_json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    flock($file_handle, LOCK_UN);
    fclose($file_handle);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid users data',
        'message' => 'داده‌های فایل کاربران نامعتبر است'
    ]);
    exit;
}

$user_found = false;
$data_changed = false;

// پیدا کردن کاربر و آپدیت لیست ویدئوهای دیده شده
foreach ($users as $i => $user) {
    if ($user['username'] === $userId && $user['group'] === $userGroup) {
        $user_found = true;
        
        // بررسی اینکه آیا ویدئو قبلاً اضافه شده یا نه
        if (!in_array($videoId, $user['watchedVideos'])) {
            $users[$i]['watchedVideos'][] = $videoId;
            $_SESSION['user']['watchedVideos'] = $users[$i]['watchedVideos'];
            $data_changed = true;
        }
        break;
    }
}

if (!$user_found) {
    flock($file_handle, LOCK_UN);
    fclose($file_handle);
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'User not found',
        'message' => 'کاربر در سیستم یافت نشد'
    ]);
    exit;
}

// اگر تغییری رخ داده باشد، فایل را بازنویسی کن
if ($data_changed) {
    // بازنویسی فایل
    rewind($file_handle);
    ftruncate($file_handle, 0);
    $write_result = fwrite($file_handle, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    if ($write_result === false) {
        flock($file_handle, LOCK_UN);
        fclose($file_handle);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Write failed',
            'message' => 'خطا در نوشتن اطلاعات'
        ]);
        exit;
    }
}

// آزاد کردن قفل و بستن فایل
flock($file_handle, LOCK_UN);
fclose($file_handle);

// محاسبه آمار پیشرفت
$videos_json = @file_get_contents(__DIR__ . '/data/videos.json');
$all_videos = $videos_json ? json_decode($videos_json, true) : [];
$group_content = $all_videos[$userGroup] ?? [];

$total_videos = 0;
foreach ($group_content as $category) {
    $total_videos += count($category['videos']);
}

$watched_count = count($_SESSION['user']['watchedVideos']);
$completion_percentage = $total_videos > 0 ? round(($watched_count / $total_videos) * 100) : 0;

// ارسال پاسخ موفقیت‌آمیز
echo json_encode([
    'success' => true,
    'message' => $data_changed ? 'پیشرفت با موفقیت ذخیره شد' : 'این ویدئو قبلاً تکمیل شده بود',
    'data' => [
        'videoId' => $videoId,
        'totalVideos' => $total_videos,
        'watchedCount' => $watched_count,
        'completionPercentage' => $completion_percentage,
        'alreadyWatched' => !$data_changed
    ]
]);
?>