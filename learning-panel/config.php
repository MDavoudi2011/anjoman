<?php
/**
 * فایل تنظیمات پلتفرم آموزشی
 * انجمن برنامه‌نویسی باهنر ۳
 * 
 * @author محمد داوودی و محمد امین مدنی
 * @version 2.0.0
 */

// جلوگیری از دسترسی مستقیم
if (!defined('APP_ACCESS')) {
    die('Direct access not permitted');
}

// تنظیمات پایه
define('APP_NAME', 'انجمن برنامه‌نویسی باهنر ۳');
define('APP_VERSION', '2.0.0');
define('APP_DESCRIPTION', 'پلتفرم آموزشی مدرن برای دوره‌های آنلاین');

// مسیرها
define('BASE_PATH', __DIR__);
define('DATA_PATH', BASE_PATH . '/data');
define('VIEWS_PATH', BASE_PATH . '/views');

// فایل‌های داده
define('USERS_FILE', DATA_PATH . '/users.json');
define('VIDEOS_FILE', DATA_PATH . '/videos.json');
define('EXERCISES_FILE', DATA_PATH . '/exercises.json');
define('TESTS_FILE', DATA_PATH . '/tests.json');

// تنظیمات Session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');

// تنظیمات امنیتی
define('SESSION_LIFETIME', 3600 * 24); // 24 ساعت
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 دقیقه

// تنظیمات نمایش خطا (فقط برای توسعه)
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// تنظیمات Timezone
date_default_timezone_set('Asia/Tehran');

/**
 * کلاس کمکی برای مدیریت پیکربندی
 */
class Config {
    
    /**
     * خواندن فایل JSON
     */
    public static function readJsonFile($filepath) {
        if (!file_exists($filepath)) {
            return null;
        }
        
        $content = @file_get_contents($filepath);
        if ($content === false) {
            return null;
        }
        
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        
        return $data;
    }
    
    /**
     * نوشتن فایل JSON
     */
    public static function writeJsonFile($filepath, $data) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }
        
        // ایجاد backup
        if (file_exists($filepath)) {
            $backupPath = $filepath . '.backup.' . date('YmdHis');
            @copy($filepath, $backupPath);
        }
        
        $result = @file_put_contents($filepath, $json, LOCK_EX);
        return $result !== false;
    }
    
    /**
     * دریافت تنظیمات کاربر
     */
    public static function getUserConfig($username, $group) {
        $users = self::readJsonFile(USERS_FILE);
        if (!$users) {
            return null;
        }
        
        foreach ($users as $user) {
            if ($user['username'] === $username && $user['group'] === $group) {
                return $user;
            }
        }
        
        return null;
    }
    
    /**
     * به‌روزرسانی تنظیمات کاربر
     */
    public static function updateUserConfig($username, $group, $updates) {
        $users = self::readJsonFile(USERS_FILE);
        if (!$users) {
            return false;
        }
        
        $updated = false;
        foreach ($users as $index => $user) {
            if ($user['username'] === $username && $user['group'] === $group) {
                $users[$index] = array_merge($user, $updates);
                $updated = true;
                break;
            }
        }
        
        if (!$updated) {
            return false;
        }
        
        return self::writeJsonFile(USERS_FILE, $users);
    }
    
    /**
     * دریافت محتوای گروه
     */
    public static function getGroupContent($group, $type = 'videos') {
        $file_map = [
            'videos' => VIDEOS_FILE,
            'exercises' => EXERCISES_FILE,
            'tests' => TESTS_FILE,
        ];
        
        if (!isset($file_map[$type])) {
            return null;
        }
        
        $data = self::readJsonFile($file_map[$type]);
        if (!$data) {
            return null;
        }
        
        return $data[$group] ?? null;
    }
    
    /**
     * اعتبارسنجی ورودی
     */
    public static function sanitizeInput($input, $type = 'string') {
        switch ($type) {
            case 'string':
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
            
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * لاگ کردن رویدادها
     */
    public static function log($message, $type = 'info') {
        if (!DEBUG_MODE) {
            return;
        }
        
        $log_file = DATA_PATH . '/app.log';
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] [$type] $message" . PHP_EOL;
        
        @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * دریافت IP کاربر
     */
    public static function getUserIP() {
        $ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }
        
        return 'UNKNOWN';
    }
    
    /**
     * محاسبه آمار پیشرفت
     */
    public static function calculateProgress($group, $watchedVideos) {
        $videos = self::getGroupContent($group, 'videos');
        if (!$videos) {
            return [
                'total' => 0,
                'watched' => 0,
                'percentage' => 0,
                'remaining' => 0
            ];
        }
        
        $total = 0;
        foreach ($videos as $category) {
            $total += count($category['videos']);
        }
        
        $watched = count($watchedVideos);
        $percentage = $total > 0 ? round(($watched / $total) * 100) : 0;
        $remaining = $total - $watched;
        
        return [
            'total' => $total,
            'watched' => $watched,
            'percentage' => $percentage,
            'remaining' => $remaining
        ];
    }
    
    /**
     * بررسی دسترسی‌های فایل
     */
    public static function checkFilePermissions() {
        $files = [
            USERS_FILE,
            VIDEOS_FILE,
            EXERCISES_FILE,
            TESTS_FILE
        ];
        
        $issues = [];
        
        foreach ($files as $file) {
            if (!file_exists($file)) {
                $issues[] = "File not found: $file";
            } elseif (!is_readable($file)) {
                $issues[] = "File not readable: $file";
            } elseif (!is_writable($file)) {
                $issues[] = "File not writable: $file";
            }
        }
        
        return [
            'ok' => empty($issues),
            'issues' => $issues
        ];
    }
    
    /**
     * دریافت اطلاعات سیستم
     */
    public static function getSystemInfo() {
        return [
            'app_name' => APP_NAME,
            'version' => APP_VERSION,
            'php_version' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'timezone' => date_default_timezone_get(),
            'date' => date('Y-m-d H:i:s'),
        ];
    }
}

/**
 * توابع کمکی سراسری
 */

/**
 * نمایش خطا با فرمت زیبا
 */
function displayError($message, $code = 500) {
    http_response_code($code);
    ?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>خطا - <?php echo APP_NAME; ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap" rel="stylesheet">
        <style>body { font-family: 'Vazirmatn', sans-serif; }</style>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">خطا</h1>
            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($message); ?></p>
            <a href="index.php" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                بازگشت به صفحه اصلی
            </a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/**
 * ریدایرکت با پیام
 */
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['flash_message'] = $message;
    }
    header("Location: $url");
    exit;
}

/**
 * نمایش پیام Flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// بررسی دسترسی‌های فایل در حالت توسعه
if (DEBUG_MODE) {
    $permissions = Config::checkFilePermissions();
    if (!$permissions['ok']) {
        Config::log('File permission issues: ' . implode(', ', $permissions['issues']), 'error');
    }
}