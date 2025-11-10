<?php
// Filepath: /api_update_progress.php
session_start();
header('Content-Type: application/json');

// تنظیمات دیتابیس (همون که تو migrate استفاده کردی)
$host = 'localhost';
$db   = 'bahonar3';
$user = 'root';
$pass = '0315324457Mm';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطا در اتصال به دیتابیس']);
    exit;
}

// بررسی لاگین
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'کاربر وارد نشده']);
    exit;
}

$username = $_SESSION['user']['username'];
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['videoId']) || empty(trim($data['videoId']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'داده نامعتبر']);
    exit;
}

$videoId = trim($data['videoId']);

// پیدا کردن کاربر و گروهش
$stmt = $pdo->prepare("SELECT id, user_group FROM users WHERE username = ?");
$stmt->execute([$username]);
$userDb = $stmt->fetch();

if (!$userDb) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'کاربر یافت نشد']);
    exit;
}

$userId = $userDb['id'];
$userGroup = $userDb['user_group'];

// بررسی اینکه آیا قبلاً دیده یا نه
$stmt = $pdo->prepare("SELECT id FROM watched_videos WHERE user_id = ? AND video_id = ?");
$stmt->execute([$userId, $videoId]);
$alreadyWatched = $stmt->fetchColumn() !== false;

// اگه ندیده بود، ثبت کن
if (!$alreadyWatched) {
    $stmt = $pdo->prepare("INSERT INTO watched_videos (user_id, video_id) VALUES (?, ?)");
    $stmt->execute([$userId, $videoId]);
    
    // آپدیت سشن (برای نمایش فوری)
    if (!isset($_SESSION['user']['watchedVideos'])) {
        $_SESSION['user']['watchedVideos'] = [];
    }
    if (!in_array($videoId, $_SESSION['user']['watchedVideos'])) {
        $_SESSION['user']['watchedVideos'][] = $videoId;
    }
}

// ==========================================================
// START: کد اصلاح شده
// ==========================================================

// محاسبه آمار پیشرفت (با خواندن مستقیم از دیتابیس)
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM videos v 
    JOIN video_categories vc ON v.category_id = vc.id 
    WHERE vc.group_name = ?
");
$stmt->execute([$userGroup]);
$totalVideos = (int)$stmt->fetchColumn(); // تبدیل به عدد

// این خط مهم‌ترین تغییر است: شمارش مستقیم از دیتابیس
$stmt = $pdo->prepare("SELECT COUNT(*) FROM watched_videos WHERE user_id = ?");
$stmt->execute([$userId]);
$watchedCount = (int)$stmt->fetchColumn(); // تبدیل به عدد

$completionPercentage = $totalVideos > 0 ? round(($watchedCount / $totalVideos) * 100) : 0;

// ==========================================================
// END: کد اصلاح شده
// ==========================================================


// پاسخ نهایی
echo json_encode([
    'success' => true,
    'message' => $alreadyWatched ? 'قبلاً دیده شده' : 'با موفقیت ثبت شد',
    'data' => [
        'videoId' => $videoId,
        'totalVideos' => $totalVideos,
        'watchedCount' => $watchedCount,
        'completionPercentage' => $completionPercentage,
        'alreadyWatched' => $alreadyWatched
    ]
]);
?>