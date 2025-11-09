<?php
session_start();

// اگر کاربر لاگین نکرده، اون رو به صفحه لاگین بفرست
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// اطلاعات کاربر از سشن خونده می‌شه
$user = $_SESSION['user'];

// تعیین ویوی فعلی، پیش‌فرض 'training'
$view = $_GET['view'] ?? 'training';

// آیکون‌ها به صورت رشته‌های SVG برای استفاده در هدر و نویگیشن
$icon_user_circle = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
$icon_book_open = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m0-11.494C10.344 3.714 6.686 2.5 3.001 5.007c-3.686 2.506-3.686 7.48 0 9.986 3.686 2.507 7.344 1.294 8.999-1.214 1.654-2.507 1.654-7.48 0-9.986z"></path></svg>';
$icon_clipboard_list = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>';
$icon_document_text = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';

$nav_items = [
    'practice' => ['label' => 'تمرین', 'icon' => $icon_clipboard_list],
    'training' => ['label' => 'آموزش', 'icon' => $icon_book_open],
    'test' => ['label' => 'آزمون', 'icon' => $icon_document_text],
];

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>داشبورد - پلتفرم آموزشی</title>
    <!-- فونت وزیرمتن -->
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Vazirmatn', 'sans-serif'],
            },
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up 0.3s ease-out forwards',
            },
          },
        },
      }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">

    <!-- این بخش معادل MainLayout.tsx (هدر) است -->
    <div class="flex flex-col min-h-screen font-sans text-gray-900 bg-gray-100 dark:bg-gray-900 dark:text-gray-200">
      <header class="fixed top-0 left-0 right-0 z-20 flex items-center justify-between p-4 bg-white shadow-md dark:bg-gray-800">
        <h1 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
          پلتفرم آموزشی
        </h1>
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2">
            <span class="text-sm font-medium">
              <?php echo htmlspecialchars($user['username']); ?> خوش آمدید! (گروه <?php echo htmlspecialchars($user['group']); ?>)
            </span>
            <a
              href="dashboard.php?view=profile"
              class="p-2 text-gray-500 rounded-full hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500"
              aria-label="نمایش پروفایل"
            >
              <?php echo $icon_user_circle; ?>
            </a>
          </div>
          <a
            href="logout.php"
            class="px-3 py-1.5 text-sm font-semibold text-white bg-red-500 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-red-500"
          >
            خروج
          </a>
        </div>
      </header>
      
      <main class="flex-grow pt-20 pb-24">
        <div class="container p-4 mx-auto">
            
            <?php
            // لود کردن ویوی مناسب بر اساس $view
            $view_file = "views/{$view}.php";
            if (file_exists($view_file)) {
                // متغیر $user برای فایل include شده قابل دسترس خواهد بود
                include $view_file;
            } else {
                // اگر فایل ویو وجود نداشت، به ویوی پیش‌فرض برگرد
                include 'views/training.php';
            }
            ?>

        </div>
      </main>
      
      <!-- این بخش معادل BottomNav.tsx است -->
      <nav class="fixed bottom-0 left-0 right-0 z-10 grid grid-cols-3 bg-white border-t border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
        <?php foreach ($nav_items as $view_name => $item):
            $is_active = ($view == $view_name);
            $active_classes = 'text-indigo-600 dark:text-indigo-400';
            $inactive_classes = 'text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400';
            $classes = $is_active ? $active_classes : $inactive_classes;
        ?>
          <a
            href="dashboard.php?view=<?php echo $view_name; ?>"
            class="flex flex-col items-center justify-center p-3 text-xs font-medium transition-colors duration-200 ease-in-out <?php echo $classes; ?>"
            aria-current="<?php echo $is_active ? 'page' : 'false'; ?>"
          >
            <?php echo $item['icon']; ?>
            <span class="mt-1"><?php echo $item['label']; ?></span>
          </a>
        <?php endforeach; ?>
      </nav>
      <!-- پایان BottomNav.tsx -->
    </div>

    <!-- 
    جاوا اسکریپت برای مودال‌ها
    این کد به دکمه‌ها در ویوهای training و practice گوش می‌دهد
    -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // --- مدیریت مودال تمرین (Practice Modal) ---
            const practiceModal = document.getElementById('practice-modal');
            if (practiceModal) {
                const modalTitle = practiceModal.querySelector('#modal-title');
                const modalDescription = practiceModal.querySelector('#modal-description');
                const closeButton = practiceModal.querySelector('[data-close-modal]');

                document.querySelectorAll('[data-open-practice-modal]').forEach(button => {
                    button.addEventListener('click', () => {
                        modalTitle.textContent = button.dataset.title;
                        modalDescription.textContent = button.dataset.description;
                        practiceModal.classList.remove('hidden');
                        practiceModal.classList.add('flex');
                    });
                });

                const closePracticeModal = () => {
                    practiceModal.classList.add('hidden');
                    practiceModal.classList.remove('flex');
                }
                
                closeButton.addEventListener('click', closePracticeModal);
                practiceModal.addEventListener('click', (e) => {
                    if (e.target === practiceModal) {
                        closePracticeModal();
                    }
                });
            }

            // --- مدیریت مودال ویدئو (Video Player Modal) ---
            const videoModal = document.getElementById('video-player-modal');
            if (videoModal) {
                const videoTitle = videoModal.querySelector('#video-modal-title');
                const videoImage = videoModal.querySelector('#video-modal-image');
                const videoProgress = videoModal.querySelector('#video-modal-progress');
                const closeButton = videoModal.querySelector('[data-close-modal]');
                let currentVideoId = null;
                let progressInterval = null;

                document.querySelectorAll('[data-open-video-modal]').forEach(card => {
                    card.addEventListener('click', () => {
                        // پر کردن مودال
                        videoTitle.textContent = card.dataset.title;
                        videoImage.src = card.dataset.thumbnail;
                        videoImage.alt = card.dataset.title;
                        currentVideoId = card.dataset.id;

                        // نمایش مودال
                        videoModal.classList.remove('hidden');
                        videoModal.classList.add('flex');
                        
                        // شروع شبیه‌سازی پخش
                        let progress = 0;
                        videoProgress.style.width = '0%';
                        
                        if (progressInterval) clearInterval(progressInterval);

                        progressInterval = setInterval(() => {
                            progress += 1;
                            videoProgress.style.width = `${progress}%`;
                            
                            if (progress >= 100) {
                                clearInterval(progressInterval);
                                // ویدئو "دیده" شد
                                handleVideoWatched(currentVideoId, card);
                                closeVideoModal();
                            }
                        }, 40); // 4 ثانیه
                    });
                });
                
                const closeVideoModal = () => {
                    if (progressInterval) clearInterval(progressInterval);
                    videoModal.classList.add('hidden');
                    videoModal.classList.remove('flex');
                }

                closeButton.addEventListener('click', closeVideoModal);
                videoModal.addEventListener('click', (e) => {
                    if (e.target === videoModal) {
                        closeVideoModal();
                    }
                });
            }

            // فانکشن برای ارسال ریکوئست "دیده شدن" به سرور
            function handleVideoWatched(videoId, cardElement) {
                // ارسال درخواست به سرور
                fetch('api_update_progress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ videoId: videoId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // آپدیت UI بدون رفرش صفحه
                        // اضافه کردن تگ "تکمیل شد"
                        if (!cardElement.querySelector('.watched-badge')) {
                            const badge = document.createElement('div');
                            badge.className = 'watched-badge absolute px-2 py-1 text-xs font-bold text-white bg-green-600 rounded-full top-2 right-2';
                            badge.textContent = 'تکمیل شد';
                            cardElement.querySelector('.relative').appendChild(badge);
                        }
                    } else {
                        console.error('Failed to update progress on server:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error sending watch progress:', error);
                });
            }
        });
    </script>
</body>
</html>