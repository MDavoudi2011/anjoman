<?php
session_start();
define('APP_ACCESS', true);
require_once './config.php';

if (!isset($_SESSION['user']['username'])) {
    redirect('index.php');
    exit;
}

$username = $_SESSION['user']['username'];
$user = Config::getUser($username); // این درست‌ترین متغیره
$view = $_GET['view'] ?? 'training';

// منوهای ناوبار پایین
$nav_items = [
    'training' => ['label' => 'آموزش', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'],
    'practice' => ['label' => 'تمرین', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'],
    'exam' => ['label' => 'آزمون', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'],
];

// فقط برای منتورها آیتم اضافه کن
if ($user['role'] === 'mentor') {
    $nav_items['mentor_panel'] = ['label' => 'منتور', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H9a2 2 0 01-2-2v-1a2 2 0 012-2h6a2 2 0 012 2v1a2 2 0 01-2 2zM12 14a4 4 0 100-8 4 4 0 000 8z"></path></svg>'];
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>داشبورد - انجمن برنامه نویسی باهنر ۳</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['Vazirmatn', 'sans-serif'] },
            animation: {
              'fade-in-up': 'fadeInUp 0.5s ease-out',
              'fade-in': 'fadeIn 0.3s ease-out',
            },
            keyframes: {
              fadeInUp: { '0%': { opacity: '0', transform: 'translateY(30px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
              fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
            },
          },
        },
      }
    </script>
    <style>
        .gradient-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        @media (prefers-color-scheme: dark) { .glass-card { background: rgba(31, 41, 55, 0.95); } }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 gradient-header shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="p-2 bg-white rounded-lg shadow-md">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg md:text-xl font-black text-white">انجمن برنامه نویسی باهنر ۳</h1>
                    </div>
                </div>

                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="hidden md:block text-right">
                        <p class="text-white text-xs opacity-75">خوش آمدید</p>
                        <p class="text-white font-bold text-sm"><?= htmlspecialchars($user['name']) ?></p>
                    </div>
                    <a href="dashboard.php?view=profile" class="p-2 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                    <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded-lg font-bold text-sm hover:bg-red-600 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                        خروج
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- محتوای اصلی -->
    <main class="pt-24 pb-24 min-h-screen">
        <div class="container mx-auto px-4 max-w-7xl">
            <?php
            $view_file = "views/{$view}.php";
            if (file_exists($view_file)) {
                include $view_file;
            } else {
                include 'views/training.php';
            }
            ?>
        </div>
    </main>

    <!-- ناوبار پایین -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-2xl">
        <div class="container mx-auto">
            <div class="grid grid-cols-<?php echo count($nav_items); ?> gap-1">
                <?php foreach ($nav_items as $view_name => $item):
                    $is_active = ($view === $view_name);
                    $active_classes = 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 border-t-4 border-indigo-600';
                    $inactive_classes = 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700';
                    $classes = $is_active ? $active_classes : $inactive_classes;
                ?>
                    <a href="dashboard.php?view=<?= $view_name ?>" 
                       class="flex flex-col items-center justify-center py-3 px-2 transition-all duration-200 <?= $classes ?>">
                        <div class="<?= $is_active ? 'transform scale-110' : '' ?> transition-transform">
                            <?= $item['icon'] ?>
                        </div>
                        <span class="text-xs font-bold mt-1"><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- Footer -->
    <footer class="pb-20 pt-8 text-center text-gray-600 dark:text-gray-400 text-sm">
        <div class="container mx-auto px-4">
            <p class="font-medium">طراحی توسط محمد داودی و محمدامین مدنی محمدی</p>
            <p class="mt-1 text-xs">انجمن برنامه نویسی باهنر ۳</p>
        </div>
    </footer>

</body>
</html>