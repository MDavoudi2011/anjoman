<?php // Filepath: /views/profile.php

$videos_json = @file_get_contents('data/videos.json');
$all_videos = $videos_json ? json_decode($videos_json, true) : [];
$group_content = $all_videos[$user['group']] ?? [];

$exercises_json = @file_get_contents('data/exercises.json');
$all_exercises = $exercises_json ? json_decode($exercises_json, true) : [];
$group_exercises = $all_exercises[$user['group']] ?? [];

// محاسبه آمار دقیق
$total_videos = 0;
$categories_count = count($group_content);
foreach ($group_content as $category) {
    $total_videos += count($category['videos']);
}

$watched_count = count($user['watchedVideos']);
$completion_percentage = $total_videos > 0 ? round(($watched_count / $total_videos) * 100) : 0;
$remaining_videos = $total_videos - $watched_count;

// محاسبه تخمین زمان (فرض: هر ویدئو 15 دقیقه)
$estimated_hours = ceil($remaining_videos * 15 / 60);

// تعیین سطح پیشرفت
$level = 'مبتدی';
$level_color = 'text-gray-600';
if ($completion_percentage >= 75) {
    $level = 'حرفه‌ای';
    $level_color = 'text-purple-600';
} elseif ($completion_percentage >= 50) {
    $level = 'پیشرفته';
    $level_color = 'text-blue-600';
} elseif ($completion_percentage >= 25) {
    $level = 'متوسط';
    $level_color = 'text-green-600';
}

?>
<div class="space-y-6 animate-fade-in-up max-w-5xl mx-auto">
    
    <!-- کارت پروفایل اصلی -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <!-- آواتار -->
            <div class="relative">
                <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-2xl">
                    <svg class="w-20 h-20 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs font-black shadow-lg">
                    گروه <?php echo htmlspecialchars($user['group']); ?>
                </div>
            </div>
            
            <!-- اطلاعات کاربر -->
            <div class="flex-1 text-center md:text-right">
                <h2 class="text-4xl font-black mb-2"><?php echo htmlspecialchars($user['username']); ?></h2>
                <p class="text-xl font-medium text-indigo-100 mb-4">دانشجوی انجمن برنامه نویسی باهنر ۳</p>
                <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                    <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-bold backdrop-blur-sm">
                        <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        سطح: <?php echo $level; ?>
                    </span>
                    <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-bold backdrop-blur-sm">
                        <?php echo $watched_count; ?> ویدئو تکمیل شده
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- آمار پیشرفت -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- کل ویدئوها -->
        <div class="glass-card rounded-xl shadow-lg p-6 text-center transform hover:scale-105 transition-all">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-800 dark:text-white mb-1"><?php echo $total_videos; ?></h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">کل ویدئوها</p>
        </div>

        <!-- ویدئوهای تکمیل شده -->
        <div class="glass-card rounded-xl shadow-lg p-6 text-center transform hover:scale-105 transition-all">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-800 dark:text-white mb-1"><?php echo $watched_count; ?></h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">تکمیل شده</p>
        </div>

        <!-- باقیمانده -->
        <div class="glass-card rounded-xl shadow-lg p-6 text-center transform hover:scale-105 transition-all">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-800 dark:text-white mb-1"><?php echo $remaining_videos; ?></h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">باقیمانده</p>
        </div>
    </div>

    <!-- نوار پیشرفت کلی -->
    <div class="glass-card rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                <svg class="w-6 h-6 ml-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                پیشرفت کلی دوره
            </h3>
            <span class="text-3xl font-black text-indigo-600"><?php echo $completion_percentage; ?>%</span>
        </div>
        
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-6 overflow-hidden shadow-inner">
            <div class="h-6 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition-all duration-1000 ease-out flex items-center justify-end px-2"
                 style="width: <?php echo $completion_percentage; ?>%">
                <?php if ($completion_percentage > 10): ?>
                    <span class="text-white text-xs font-bold"><?php echo $completion_percentage; ?>%</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-4 flex flex-wrap gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full ml-2"></div>
                <span class="text-gray-600 dark:text-gray-400"><?php echo $watched_count; ?> تکمیل شده</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-gray-300 rounded-full ml-2"></div>
                <span class="text-gray-600 dark:text-gray-400"><?php echo $remaining_videos; ?> باقیمانده</span>
            </div>
            <?php if ($remaining_videos > 0): ?>
                <div class="flex items-center mr-auto">
                    <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">تخمین: ~<?php echo $estimated_hours; ?> ساعت</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- جزئیات دسته‌بندی‌ها -->
    <?php if (!empty($group_content)): ?>
        <div class="glass-card rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 ml-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                پیشرفت به تفکیک دسته‌بندی
            </h3>
            
            <div class="space-y-4">
                <?php foreach ($group_content as $category): 
                    $cat_total = count($category['videos']);
                    $cat_watched = 0;
                    foreach ($category['videos'] as $video) {
                        if (in_array($video['id'], $user['watchedVideos'])) {
                            $cat_watched++;
                        }
                    }
                    $cat_percentage = $cat_total > 0 ? round(($cat_watched / $cat_total) * 100) : 0;
                ?>
                    <div class="border-r-4 border-indigo-500 pr-4">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($category['categoryTitle']); ?></h4>
                            <span class="text-sm font-bold text-indigo-600"><?php echo $cat_watched; ?>/<?php echo $cat_total; ?></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500"
                                 style="width: <?php echo $cat_percentage; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- تمرینات -->
    <div class="glass-card rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
            <svg class="w-6 h-6 ml-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            چالش‌های تمرینی
        </h3>
        <div class="flex items-center justify-between">
            <p class="text-gray-600 dark:text-gray-400">تعداد کل چالش‌ها:</p>
            <span class="text-2xl font-black text-indigo-600"><?php echo count($group_exercises); ?></span>
        </div>
    </div>

    <!-- پیام انگیزشی -->
    <?php if ($completion_percentage < 100): ?>
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl shadow-lg p-6 text-white text-center">
            <svg class="w-16 h-16 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <h3 class="text-2xl font-black mb-2">همچنان پیش برو! 💪</h3>
            <p class="text-lg font-medium">تو داری عالی پیش میری! فقط <?php echo $remaining_videos; ?> ویدئو دیگه مونده.</p>
        </div>
    <?php else: ?>
        <div class="bg-gradient-to-r from-green-400 to-emerald-500 rounded-xl shadow-lg p-6 text-white text-center">
            <svg class="w-16 h-16 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <h3 class="text-2xl font-black mb-2">تبریک! 🎉</h3>
            <p class="text-lg font-medium">تو تمام دوره‌ها رو با موفقیت تکمیل کردی!</p>
        </div>
    <?php endif; ?>

</div>