<?php // Filepath: /views/test.php

$tests_json = @file_get_contents('data/tests.json');
$all_content = $tests_json ? json_decode($tests_json, true) : [];
$test_data = $all_content[$user['group']] ?? null;

?>
<div class="animate-fade-in-up max-w-3xl mx-auto">
    <?php if (!$test_data): ?>
        <div class="glass-card rounded-xl shadow-lg p-8 text-center">
                    <div class="relative inline-block mb-6">
                        <div class="w-24 h-24 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center shadow-xl">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center border-4 border-white dark:border-gray-800">
                            <svg class="w-4 h-4 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white mb-4">آزمونی فعال نیست</h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto"><?php echo htmlspecialchars($test_data['message']); ?></p>
                    
                    <!-- اطلاعات اضافی -->
                    <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20 rounded-xl">
                        <div class="flex items-start text-right">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 ml-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white mb-2">نکات مهم:</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                                    <li>آزمون‌ها به صورت دوره‌ای فعال می‌شوند</li>
                                    <li>زمان دقیق برگزاری از طریق اعلان اطلاع‌رسانی می‌شود</li>
                                    <li>برای آمادگی بهتر، دوره‌های آموزشی را کامل کنید</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- کارت‌های راهنما -->
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="glass-card rounded-xl p-6 text-center hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-1">مطالعه محتوا</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">دوره‌های آموزشی را مطالعه کنید</p>
                    </div>

                    <div class="glass-card rounded-xl p-6 text-center hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-1">حل تمرین‌ها</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">چالش‌های تمرینی را انجام دهید</p>
                    </div>

                    <div class="glass-card rounded-xl p-6 text-center hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-1">آماده باشید</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">منتظر اعلام زمان آزمون باشید</p>
                    </div>
                </div>
    <?php else: ?>
        <div class="space-y-6">
            <!-- هدر -->
            <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-black text-center mb-2"><?php echo htmlspecialchars($test_data['title']); ?></h2>
            </div>

            <!-- محتوای اصلی -->
            <?php if ($test_data['active']): ?>
                <!-- اگر آزمون فعال باشد -->
                <div class="glass-card rounded-xl shadow-lg p-8 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white mb-4">آزمون فعال است!</h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8"><?php echo htmlspecialchars($test_data['message']); ?></p>
                    <button class="px-8 py-4 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-bold rounded-lg hover:from-blue-600 hover:to-cyan-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                        شروع آزمون
                    </button>
                </div>
            <?php else: ?>
                <!-- اگر آزمون غیرفعال باشد -->
                <div class="glass-card rounded-xl shadow-lg p-8 text-center">
                    <div class="relative inline-block mb-6">
                        <div class="w-24 h-24 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center shadow-xl">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center border-4 border-white dark:border-gray-800">
                            <svg class="w-4 h-4 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white mb-4">آزمونی فعال نیست</h3>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto"><?php echo htmlspecialchars($test_data['message']); ?></p>
                    
                    <!-- اطلاعات اضافی -->
                    <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20 rounded-xl">
                        <div class="flex items-start text-right">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 ml-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white mb-2">نکات مهم:</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                                    <li>آزمون‌ها به صورت دوره‌ای فعال می‌شوند</li>
                                    <li>زمان دقیق برگزاری از طریق اعلان اطلاع‌رسانی می‌شود</li>
                                    <li>برای آمادگی بهتر، دوره‌های آموزشی را کامل کنید</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- کارت‌های راهنما -->
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="glass-card rounded-xl p-6 text-center hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-1">مطالعه محتوا</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">دوره‌های آموزشی را مطالعه کنید</p>
                    </div>

                    <div class="glass-card rounded-xl p-6 text-center hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-1">حل تمرین‌ها</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">چالش‌های تمرینی را انجام دهید</p>
                    </div>

                    <div class="glass-card rounded-xl p-6 text-center hover:shadow-lg transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-1">آماده باشید</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">منتظر اعلام زمان آزمون باشید</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>