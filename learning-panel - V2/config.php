<?php
/**
 * فایل تنظیمات پلتفرم آموزشی - نسخه دیتابیس
 * انجمن برنامه‌نویسی باهنر ۳
 * 
 * @author محمد داوودی و محمدامین مدنی محمدی
 * @version 2.1.0-DB
 */

if (!defined('APP_ACCESS')) {
    die('Direct access not permitted');
}

// تنظیمات پایه
define('APP_NAME', 'انجمن برنامه‌نویسی باهنر ۳');
define('APP_VERSION', '2.1.0');
define('APP_DESCRIPTION', 'پلتفرم آموزشی مدرن با دیتابیس MySQL');

// مسیرها
define('BASE_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/views');

// تنظیمات دیتابیس
define('DB_HOST', 'localhost');
define('DB_NAME', 'bahonar3');
define('DB_USER', 'root');
define('DB_PASS', '0315324457Mm');
define('DB_CHARSET', 'utf8mb4');

// تنظیمات Session

ini_set('session.cookie_samesite', 'Strict');

// تنظیمات امنیتی
define('SESSION_LIFETIME', 3600 * 24); // 24 ساعت
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 دقیقه
define('DEBUG_MODE', false);

// نمایش خطا
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

date_default_timezone_set('Asia/Tehran');

/**
 * کلاس مدیریت دیتابیس و تنظیمات
 */
class Config {
    private static $pdo = null;

    // اتصال به دیتابیس (Singleton)
    public static function db() {
        if (self::$pdo === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    die("خطای دیتابیس: " . $e->getMessage());
                }
                displayError("خطا در اتصال به سرور. لطفاً بعداً تلاش کنید.", 503);
            }
        }
        return self::$pdo;
    }

    // دریافت کاربر
    public static function getUser($username) {
        $stmt = self::db()->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    // به‌روزرسانی کاربر
    public static function updateUser($username, $updates) {
        $sets = [];
        $values = [];
        foreach ($updates as $key => $value) {
            $sets[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $username;
        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE username = ?";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($values);
    }

    // دریافت محتوای گروه (ویدیو، تمرین، آزمون)
    public static function getGroupContent($group, $type = 'videos') {
        $pdo = self::db();
        switch ($type) {
            case 'videos':
                $stmt = $pdo->prepare("
                    SELECT vc.title as categoryTitle, v.*
                    FROM videos v
                    JOIN video_categories vc ON v.category_id = vc.id
                    WHERE vc.group_name = ?
                    ORDER BY vc.id, v.id
                ");
                $stmt->execute([$group]);
                $rows = $stmt->fetchAll();

                $result = [];
                $currentCat = null;
                foreach ($rows as $row) {
                    if ($currentCat !== $row['categoryTitle']) {
                        $currentCat = $row['categoryTitle'];
                        $result[] = ['categoryTitle' => $currentCat, 'videos' => []];
                    }
                    $result[count($result)-1]['videos'][] = [
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'duration' => $row['duration'],
                        'thumbnailUrl' => $row['thumbnail_url'],
                        'videoUrl' => $row['video_url']
                    ];
                }
                return $result;

            case 'exercises':
                $stmt = $pdo->prepare("SELECT * FROM exercises WHERE group_name = ? ORDER BY id");
                $stmt->execute([$group]);
                return $stmt->fetchAll();

            case 'tests':
                $stmt = $pdo->prepare("SELECT * FROM tests WHERE group_name = ? LIMIT 1");
                $stmt->execute([$group]);
                return $stmt->fetch();

            default:
                return null;
        }
    }

    // محاسبه پیشرفت
    public static function calculateProgress($group, $watchedVideos) {
        $totalStmt = self::db()->prepare("
            SELECT COUNT(*) FROM videos v
            JOIN video_categories vc ON v.category_id = vc.id
            WHERE vc.group_name = ?
        ");
        $totalStmt->execute([$group]);
        $total = $totalStmt->fetchColumn();

        $watched = is_array($watchedVideos) ? count($watchedVideos) : 0;
        $percentage = $total > 0 ? round(($watched / $total) * 100) : 0;

        return [
            'total' => (int)$total,
            'watched' => $watched,
            'percentage' => $percentage,
            'remaining' => $total - $watched
        ];
    }

    // لاگ کردن
    public static function log($message, $userId = null, $type = 'info') {
        $stmt = self::db()->prepare("
            INSERT INTO logs (user_id, action, description, ip, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $type, $message, self::getUserIP()]);
    }

    // دریافت IP
    public static function getUserIP() {
        $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }
        return 'UNKNOWN';
    }

    // تمیز کردن ورودی
    public static function sanitize($input, $type = 'string') {
        switch ($type) {
            case 'int': return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'email': return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'url': return filter_var($input, FILTER_SANITIZE_URL);
            default: return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
}

// توابع کمکی
function displayError($message, $code = 500) {
    http_response_code($code);
    ?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>خطا - <?= APP_NAME ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap" rel="stylesheet">
        <style>body{font-family:'Vazirmatn',sans-serif}</style>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">خطا</h1>
            <p class="text-gray-600 mb-4"><?= htmlspecialchars($message) ?></p>
            <a href="index.php" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">بازگشت</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

function redirect($url, $message = null) {
    if ($message) $_SESSION['flash_message'] = $message;
    header("Location: $url");
    exit;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}