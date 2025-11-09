<?php // Filepath: /views/practice.php

$exercises_json = @file_get_contents('data/exercises.json');
$all_content = $exercises_json ? json_decode($exercises_json, true) : [];
$content = $all_content[$user['group']] ?? [];

$difficulty_colors = [
    'آسان' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 border-green-500',
    'متوسط' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 border-yellow-500',
    'سخت' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 border-red-500',
];

$difficulty_icons = [
    'آسان' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'متوسط' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>',
    'سخت' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>',
];

?>
<div class="space-y-6 animate-fade-in-up">
    
    <!-- هدر صفحه -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-black mb-2 flex items-center">
                    <svg class="w-10 h-10 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    چالش‌های تمرینی
                </h2>
                <p class="text-purple-100">مهارت‌های خود را با حل این تمرین‌ها تقویت کنید</p>
            </div>
            <div class="mt-4 md:mt-0 bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm">
                <div class="text-center">
                    <div class="text-4xl font-black mb-1"><?php echo count($content); ?></div>
                    <div class="text-sm font-medium">چالش در دسترس</div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (empty($content)): ?>
        <div class="glass-card rounded-2xl shadow-xl p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">چالشی یافت نشد</h3>
            <p class="text-gray-500 dark:text-gray-400">هنوز تمرینی برای گروه شما اضافه نشده است.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($content as $index => $challenge):
                $color_class = $difficulty_colors[$challenge['difficulty']] ?? 'bg-gray-100 text-gray-800 border-gray-500';
                $icon_path = $difficulty_icons[$challenge['difficulty']] ?? '';
            ?>
                <div class="glass-card rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden group">
                    <!-- هدر کارت با گرادیانت -->
                    <div class="h-2 bg-gradient-to-r from-purple-500 to-pink-500"></div>
                    
                    <div class="p-6">
                        <!-- شماره چالش -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-black text-xl shadow-lg">
                                <?php echo $index + 1; ?>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full border-2 <?php echo $color_class; ?> flex items-center">
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <?php echo $icon_path; ?>
                                </svg>
                                <?php echo htmlspecialchars($challenge['difficulty']); ?>
                            </span>
                        </div>
                        
                        <!-- عنوان -->
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                            <?php echo htmlspecialchars($challenge['title']); ?>
                        </h3>
                        
                        <!-- دکمه -->
                        <button
                            class="w-full px-4 py-3 font-bold text-white bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg hover:from-purple-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg transform hover:scale-105"
                            data-open-practice-modal
                            data-title="<?php echo htmlspecialchars($challenge['title']); ?>"
                            data-description="<?php echo htmlspecialchars($challenge['description']); ?>"
                            data-difficulty="<?php echo htmlspecialchars($challenge['difficulty']); ?>">
                            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            مشاهده جزئیات
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- راهنما -->
        <div class="glass-card rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                <svg class="w-6 h-6 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                راهنمای سطح سختی
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="flex items-center p-3 bg-green-50 dark:bg-green-900 dark:bg-opacity-20 rounded-lg">
                    <div class="w-3 h-3 bg-green-500 rounded-full ml-2"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">آسان - مناسب مبتدی‌ها</span>
                </div>
                <div class="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900 dark:bg-opacity-20 rounded-lg">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full ml-2"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">متوسط - نیاز به تمرین</span>
                </div>
                <div class="flex items-center p-3 bg-red-50 dark:bg-red-900 dark:bg-opacity-20 rounded-lg">
                    <div class="w-3 h-3 bg-red-500 rounded-full ml-2"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">سخت - چالش پیشرفته</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- مودال جزئیات تمرین -->
<div id="practice-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black bg-opacity-80 backdrop-blur-sm animate-fade-in">
    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden animate-scale-in" onclick="event.stopPropagation()">
        
        <!-- هدر مودال -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 id="modal-title" class="text-2xl font-black text-white mb-2"></h3>
                    <div id="modal-difficulty-badge" class="inline-block"></div>
                </div>
                <button type="button" class="p-2 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all" data-close-modal>
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- محتوای مودال -->
        <div class="p-6 space-y-6">
            <div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-3 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    توضیحات چالش
                </h4>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p id="modal-description" class="text-gray-700 dark:text-gray-300 leading-relaxed"></p>
                </div>
            </div>
            
            <!-- دکمه آپلود (غیرفعال) -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <button class="w-full px-6 py-3 font-bold text-gray-500 bg-gray-200 dark:bg-gray-700 dark:text-gray-400 rounded-lg cursor-not-allowed flex items-center justify-center" disabled>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    آپلود کد (به زودی فعال می‌شود)
                </button>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-2">این قابلیت به زودی اضافه خواهد شد</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('practice-modal');
    if (modal) {
        const difficultyColors = {
            'آسان': 'px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full',
            'متوسط': 'px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full',
            'سخت': 'px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full'
        };
        
        document.querySelectorAll('[data-open-practice-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('modal-title').textContent = btn.dataset.title;
                document.getElementById('modal-description').textContent = btn.dataset.description;
                
                const badge = document.getElementById('modal-difficulty-badge');
                badge.className = difficultyColors[btn.dataset.difficulty] || '';
                badge.textContent = btn.dataset.difficulty;
            });
        });
    }
});
</script>