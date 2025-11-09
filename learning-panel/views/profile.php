<?php // Filepath: /views/profile.php
// $user در این فایل از dashboard.php قابل دسترس است

// محاسبه پیشرفت
$videos_json = @file_get_contents('data/videos.json');
$all_videos = $videos_json ? json_decode($videos_json, true) : [];
$group_content = $all_videos[$user['group']] ?? [];

$total_videos = 0;
foreach ($group_content as $category) {
    $total_videos += count($category['videos']);
}

$watched_count = count($user['watchedVideos']);
$completion_percentage = $total_videos > 0 ? round(($watched_count / $total_videos) * 100) : 0;

?>
<!-- این بخش معادل ProfileView.tsx است -->
<div class="p-6 space-y-8 bg-white rounded-lg shadow-lg dark:bg-gray-800 animate-fade-in-up">
    <h2 class="pb-2 text-3xl font-extrabold text-gray-800 border-b-4 border-indigo-500 dark:text-white">پروفایل کاربری</h2>
    
    <div class="p-6 border border-gray-200 rounded-lg dark:border-gray-700">
        <p class="text-lg"><span class="font-bold">نام کاربری:</span> <?php echo htmlspecialchars($user['username']); ?></p>
        <p class="text-lg"><span class="font-bold">گروه تخصیص یافته:</span> گروه <?php echo htmlspecialchars($user['group']); ?></p>
    </div>

    <div>
        <h3 class="mb-4 text-2xl font-bold text-gray-700 dark:text-gray-300">میزان پیشرفت</h3>
        <div class="space-y-3">
            <div class="flex justify-between mb-1">
                <span class="text-base font-medium text-indigo-700 dark:text-white">پیشرفت دوره‌ها</span>
                <span class="text-sm font-medium text-indigo-700 dark:text-white"><?php echo $watched_count; ?> از <?php echo $total_videos; ?> ویدئو</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                <div 
                    class="bg-indigo-600 h-4 rounded-full transition-all duration-500 ease-out" 
                    style="width: <?php echo $completion_percentage; ?>%"
                ></div>
            </div>
             <p class="text-center text-indigo-600 dark:text-indigo-400"><?php echo $completion_percentage; ?>% تکمیل شده</p>
        </div>
    </div>
</div>
<!-- پایان ProfileView.tsx -->