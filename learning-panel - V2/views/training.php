<?php // Filepath: /views/training.php

// حتماً config.php قبل از این فایل include شده باشه
// مثلاً تو dashboard.php باید این باشه: require_once '../config.php';

if (!isset($user) || !isset($user['username'])) {
    displayError("دسترسی غیرمجاز", 403);
}

// دریافت گروه کاربر از دیتابیس (نه سشن!)
$currentUser = Config::getUser($user['username']);
if (!$currentUser) {
    displayError("کاربر یافت نشد", 404);
}

$userGroup = $currentUser['user_group'];
$userId = $currentUser['id'];

// دریافت ویدیوهای گروه
$content = Config::getGroupContent($userGroup, 'videos');

// دریافت لیست ویدیوهای دیده شده
$stmt = Config::db()->prepare("
    SELECT video_id FROM watched_videos 
    WHERE user_id = ? 
");
$stmt->execute([$userId]);
$watchedVideos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// آپدیت سشن برای سازگاری با کدهای قبلی (اختیاری)
$_SESSION['user']['watchedVideos'] = $watchedVideos;

// محاسبه آمار پیشرفت
$progress = Config::calculateProgress($userGroup, $watchedVideos);
$total_videos = $progress['total'];
$watched_count = $progress['watched'];
$completion_percentage = $progress['percentage'];

?>
<div class="space-y-6 animate-fade-in-up">
    
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-black mb-2">دوره‌های آموزشی</h2>
                <p class="text-indigo-100">یادگیری را با بهترین محتوا شروع کنید</p>
            </div>
            <div class="mt-4 md:mt-0 bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm">
                <div class="text-center">
                    <div class="text-4xl font-black mb-1"><?php echo $watched_count; ?> از <?php echo $total_videos; ?></div>
                    <div class="text-sm font-medium">ویدئوی تکمیل شده</div>
                    <div class="mt-2 w-full bg-white bg-opacity-30 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full transition-all duration-500" 
                             style="width: <?php echo $completion_percentage; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (empty($content)): ?>
        <div class="glass-card rounded-2xl shadow-xl p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">محتوایی یافت نشد</h3>
            <p class="text-gray-500 dark:text-gray-400">هنوز دوره‌ای برای گروه شما اضافه نشده است.</p>
        </div>
    <?php else: ?>
        <?php foreach ($content as $category): ?>
            <section class="animate-fade-in">
                <div class="flex items-center mb-4">
                    <div class="w-1 h-8 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full ml-3"></div>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white">
                        <?php echo htmlspecialchars($category['categoryTitle']); ?>
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    
                    
                <?php
// --- شروع کد اصلاح شده ---
// کپی کردن آرایه ویدئوها
$videos_in_category = $category['videos']; 

// مرتب‌سازی آرایه بر اساس 'id' (سازگار با PHP 5.x به بالا)
usort($videos_in_category, function($a, $b) {
    if ($a['id'] == $b['id']) {
        return 0;
    }
    // مرتب‌سازی صعودی (از کم به زیاد)
    return ($a['id'] < $b['id']) ? -1 : 1; 
});
// --- پایان کد اصلاح شده ---

// حالا از آرایه مرتب شده $videos_in_category در حلقه استفاده می‌کنیم
foreach ($videos_in_category as $video):
    $is_watched = in_array($video['id'], $watchedVideos);
?>
                        <div class="glass-card rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 cursor-pointer group overflow-hidden"
                             data-open-video-modal
                             data-id="<?php echo htmlspecialchars($video['id']); ?>"
                             data-title="<?php echo htmlspecialchars($video['title']); ?>"
                             data-thumbnail="<?php echo htmlspecialchars($video['thumbnailUrl']); ?>"
                             data-video-url="<?php echo htmlspecialchars($video['videoUrl']); ?>">
                            
                            <div class="relative overflow-hidden">
                                <img src="<?php echo htmlspecialchars($video['thumbnailUrl']); ?>" 
                                     alt="<?php echo htmlspecialchars($video['title']); ?>" 
                                     class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110" />
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>
                                
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-16 h-16 bg-white bg-opacity-90 rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform shadow-xl">
                                        <svg class="w-8 h-8 text-indigo-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <?php if ($is_watched): ?>
                                    <div class="watched-badge absolute px-3 py-1 text-xs font-bold text-white bg-gradient-to-r from-green-500 to-emerald-600 rounded-full top-2 right-2 shadow-lg">
                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        تکمیل شد
                                    </div>
                                <?php endif; ?>
                                
                                <div class="absolute bottom-2 left-2 px-2 py-1 bg-black bg-opacity-75 text-white text-xs font-bold rounded">
                                    <?php echo htmlspecialchars($video['duration']); ?>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h4 class="font-bold text-gray-800 dark:text-white mb-1 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    <?php echo htmlspecialchars($video['title']); ?>
                                </h4>
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($video['duration']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="video-player-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black bg-opacity-80 backdrop-blur-sm animate-fade-in">
    <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
        
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 id="video-modal-title" class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span></span>
            </h3>
            <button type="button" class="p-2 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all" data-close-modal>
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        
        <div class="bg-black">
            <video id="video-modal-player" class="w-full" controls style="max-height: 70vh;">
                مرورگر شما از تگ ویدئو پشتیبانی نمی‌کند.
            </video>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("video-player-modal");
  const modalTitle = document.querySelector("#video-modal-title span");
  const videoPlayer = document.getElementById("video-modal-player");
  let currentVideoId = null;

  // باز کردن مودال و پخش ویدیو
  document.querySelectorAll("[data-open-video-modal]").forEach(card => {
    card.addEventListener("click", () => {
      currentVideoId = card.getAttribute("data-id");
      const title = card.getAttribute("data-title");
      const videoUrl = card.getAttribute("data-video-url");

      modalTitle.textContent = title;
      videoPlayer.src = videoUrl;

      modal.classList.remove("hidden");
      modal.classList.add("flex");

      // جلوگیری از جلو زدن ⛔
      videoPlayer.addEventListener('seeking', e => {
        if (videoPlayer.currentTime < videoPlayer.duration - 1) {
          if (videoPlayer.currentTime > (videoPlayer.lastTime || 0) + 2) {
            videoPlayer.currentTime = videoPlayer.lastTime || 0;
          }
        }
      });

      videoPlayer.addEventListener('timeupdate', () => {
        videoPlayer.lastTime = videoPlayer.currentTime;
      });

      // ==========================================================
      // START: کد اصلاح شده
      // ==========================================================
      
      // وقتی ویدیو تموم شد ✅
      videoPlayer.addEventListener('ended', () => {
        // به محض تمام شدن، درخواست رو به API صحیح می‌فرستیم
        fetch('api_update_progress.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ videoId: currentVideoId }) // ارسال به‌صورت JSON
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log('ویدیو با موفقیت ثبت شد:', data.message);
            // بهترین کار: صفحه رو رفرش کن تا همه آمارها و تیک‌ها آپدیت بشن
            location.reload(); 
          } else {
            console.error('خطا در ثبت ویدیو:', data.message);
          }
        })
        .catch(error => {
          console.error('خطای شبکه:', error);
        });
      });
      
      // ==========================================================
      // END: کد اصلاح شده
      // ==========================================================

      videoPlayer.play().catch(() => {});
    });
  });

  // بستن مودال
  document.querySelectorAll("[data-close-modal]").forEach(btn => {
    btn.addEventListener("click", closeModal);
  });
  modal.addEventListener("click", closeModal);

  function closeModal() {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    videoPlayer.pause();
    videoPlayer.currentTime = 0;
    videoPlayer.src = "";
    currentVideoId = null;
  }
});
</script>