<?php
session_start();

// اول APP_ACCESS رو تعریف کن
define('APP_ACCESS', true);

// بعد config.php رو لود کن
require_once 'config.php';

// حالا بقیه کد
if (isset($_SESSION['user'])) {
    redirect('dashboard.php?view=training');
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'لطفاً نام کاربری و رمز عبور را وارد کنید.';
    } else {
        // دریافت کاربر از دیتابیس
        $user = Config::getUser($username);

        if ($user && password_verify($password, $user['password'])) {
            // ورود موفق
            unset($user['password']); // امنیت بیشتر
            $_SESSION['user'] = [
                'username' => $user['username'],
                'name' => $user['name'],
                'user_group' => $user['user_group'],
                'role' => $user['role'] ?? 'user',
                'points' => $user['points'] ?? 0,
                'level' => $user['level'] ?? 1
            ];
            $_SESSION['name'] = $user['name'];

            // لاگ ورود
            Config::log("ورود موفق به سیستم", $user['id']);

            redirect('dashboard.php?view=training');
        } else {
            $error = 'نام کاربری یا رمز عبور اشتباه است.';
            Config::log("تلاش ناموفق برای ورود با نام کاربری: $username");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ورود - انجمن برنامه‌نویسی باهنر</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['Vazirmatn', 'sans-serif'] },
            animation: {
              'float': 'float 6s ease-in-out infinite',
              'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
              'slide-up': 'slideUp 0.5s ease-out',
              'fade-in': 'fadeIn 0.6s ease-out',
            },
            keyframes: {
              float: { '0%, 100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-20px)' } },
              slideUp: { '0%': { transform: 'translateY(100px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
              fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
            },
          },
        },
      }
    </script>
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass-effect { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    
    <!-- پس‌زمینه تزئینی -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"></div>
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: 4s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10 animate-slide-up">
        <div class="text-center mb-8 animate-fade-in">
            <div class="inline-block p-4 bg-white rounded-full shadow-2xl mb-4">
                <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-black text-white mb-2">انجمن برنامه‌نویسی</h1>
            <h2 class="text-2xl font-bold text-white opacity-90">باهنر ۳</h2>
            <p class="text-white opacity-75 mt-2">پلتفرم آموزش و پیشرفت</p>
        </div>

        <div class="glass-effect rounded-3xl shadow-2xl p-8 backdrop-blur-lg">
            <form class="space-y-6" method="POST">
                <div>
                    <label for="username" class="block text-sm font-bold text-white mb-2">نام کاربری</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input id="username" name="username" type="text" required autocomplete="username"
                               class="block w-full pr-10 px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-60 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all"
                               placeholder="نام کاربری خود را وارد کنید" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-white mb-2">رمز عبور</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               class="block w-full pr-10 px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-60 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all"
                               placeholder="رمز عبور خود را وارد کنید" />
                    </div>
                </div>
                
                <?php if ($error): ?>
                    <div class="bg-red-500 bg-opacity-20 border border-red-400 text-white px-4 py-3 rounded-xl text-sm font-medium text-center animate-pulse">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <button type="submit"
                        class="w-full bg-white text-indigo-600 py-3 px-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    ورود به سیستم
                </button>
            </form>
        </div>

        <div class="text-center mt-6 text-white text-sm opacity-75">
            <p>تمامی حقوق برای محمد داوودی و محمدامین مدنی محمدی محفوظ است</p>
            <p class="mt-1">© 2025 انجمن برنامه‌نویسی باهنر</p>
        </div>
    </div>
</body>
</html>