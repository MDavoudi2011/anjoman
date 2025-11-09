<?php // Filepath: /views/profile.php
// $user (از سشن) در این فایل از dashboard.php قابل دسترس است

// --- شروع اصلاح ---
// به جای اعتماد به سشن، اطلاعات کاربر را مستقیماً از فایل JSON (منبع حقیقت) می‌خوانیم
// چون می‌دانیم این فایل همیشه آپدیت می‌شود.
$users_json = @file_get_contents('data/users.json');
$all_users = $users_json ? json_decode($users_json, true) : [];

$current_user_data = $user; // استفاده از دیتای سشن به عنوان پیش‌فرض

// جستجو برای پیدا کردن دیتای به‌روز شده کاربر در فایل JSON
foreach ($all_users as $u) {
    if ($u['username'] === $user['username'] && $u['group'] === $user['group']) {
        $current_user_data = $u; // دیتای به‌روز شده از فایل JSON جایگزین شد
        break;
    }
}
// حالا $current_user_data حاوی تازه‌ترین اطلاعات از users.json است
// --- پایان اصلاح ---


// محاسبه پیشرفت با استفاده از دیتای به‌روز شده
$videos_json = @file_get_contents('data/videos.json');
$all_videos = $videos_json ? json_decode($videos_json, true) : [];
$group_content = $all_videos[$current_user_data['group']] ?? [];

$total_videos = 0;
foreach ($group_content as $category) {
    $total_videos += count($category['videos']);
}

// از $current_user_data (که از JSON آمده) برای محاسبه استفاده می‌کنیم
$watched_count = count($current_user_data['watchedVideos']);
$completion_percentage = $total_videos > 0 ? round(($watched_count / $total_videos) * 100) : 0;

?>
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