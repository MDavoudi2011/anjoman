<?php // Filepath: /views/training.php

// $user در این فایل از dashboard.php قابل دسترس است

// خواندن محتوای آموزشی
$videos_json = @file_get_contents('data/videos.json');
$all_content = $videos_json ? json_decode($videos_json, true) : [];
$content = $all_content[$user['group']] ?? [];

$icon_play = '<svg class="w-16 h-16 text-white opacity-70 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>';

?>
<!-- این بخش معادل TrainingView.tsx است -->
<div class="space-y-8 animate-fade-in-up">
  <h2 class="pb-2 text-3xl font-extrabold text-gray-800 border-b-4 border-indigo-500 dark:text-white">دوره‌های آموزشی</h2>
  
  <?php if (empty($content)): ?>
    <div class="text-center p-8 bg-white rounded-lg shadow-lg dark:bg-gray-800">
        <p class="text-lg text-gray-600 dark:text-gray-400">محتوای آموزشی برای گروه شما یافت نشد.</p>
    </div>
  <?php else: ?>
    <?php foreach ($content as $category): ?>
      <section>
        <h3 class="mb-4 text-2xl font-bold text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($category['categoryTitle']); ?></h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          
          <?php foreach ($category['videos'] as $video):
              $is_watched = in_array($video['id'], $user['watchedVideos']);
          ?>
            <div
              class="overflow-hidden bg-white rounded-lg shadow-lg cursor-pointer group dark:bg-gray-800"
              data-open-video-modal
              data-id="<?php echo htmlspecialchars($video['id']); ?>"
              data-title="<?php echo htmlspecialchars($video['title']); ?>"
              data-thumbnail="<?php echo htmlspecialchars($video['thumbnailUrl']); ?>"
              data-video-url="<?php echo htmlspecialchars($video['videoUrl']); ?>"
            >
              <div class="relative">
                <img src="<?php echo htmlspecialchars($video['thumbnailUrl']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="object-cover w-full h-40 transition-transform duration-300 group-hover:scale-105" />
                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40">
                  <?php echo $icon_play; ?>
                </div>
                <?php if ($is_watched): ?>
                   <div class="watched-badge absolute px-2 py-1 text-xs font-bold text-white bg-green-600 rounded-full top-2 right-2">تکمیل شد</div>
                <?php endif; ?>
              </div>
              <div class="p-4">
                <h4 class="font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($video['title']); ?></h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">مدت: <?php echo htmlspecialchars($video['duration']); ?></p>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<!-- پایان TrainingView.tsx -->

<!-- 
  مودال پخش ویدئو
  این بخش معادل VideoPlayerModal.tsx است
  این مودال توسط جاوا اسکریپت در dashboard.php کنترل می‌شود
-->
<div
  id="video-player-modal"
  class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black bg-opacity-70"
>
  <div
    class="relative w-full max-w-2xl overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800 animate-fade-in-up"
    onclick="(e) => e.stopPropagation()"
  >
    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
         <h3 id="video-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
            <!-- عنوان ویدئو اینجا قرار می‌گیرد -->
         </h3>
         <button
            type="button"
            class="inline-flex items-center p-1.5 mr-auto text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white"
            data-close-modal
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd"></path></svg>
            <span class="sr-only">بستن مودال</span>
          </button>
    </div>
    <div class="p-4 text-center bg-gray-900">
        <!-- به جای <img> و شبیه‌سازی، تگ video واقعی -->
        <video
            id="video-modal-player"
            class="w-full mx-auto max-h-80"
            controls
            src=""
        >
            مرورگر شما از تگ ویدئو پشتیبانی نمی‌کند.
        </video>
    </div>
    <!-- نوار پیشرفت شبیه‌سازی شده حذف شد -->
  </div>
</div>