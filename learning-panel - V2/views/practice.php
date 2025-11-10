<?php // Filepath: /views/practice.php

// حتماً config.php قبل از این فایل include شده باشه (از dashboard.php)
if (!isset($user) || !isset($user['username'])) {
    displayError("دسترسی غیرمجاز", 403);
}

// دریافت اطلاعات کامل کاربر از دیتابیس
$currentUser = Config::getUser($user['username']);
if (!$currentUser) {
    displayError("کاربر یافت نشد", 404);
}

$userGroup = $currentUser['user_group'];
$userId = $currentUser['id'];

// دریافت تمرین‌ها از JSON با استفاده از متد Config
$content = Config::getGroupContent($userGroup, 'exercises');

// دریافت وضعیت ارسال‌های کاربر
$stmt = Config::db()->prepare("
    SELECT exercise_id, status FROM exercise_submissions 
    WHERE user_id = ? 
");
$stmt->execute([$userId]);
$submissions = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $submissions[$row['exercise_id']] = $row['status'];
}

// رنگ‌ها و بج‌ها
$difficulty_colors = [
    'آسان' => 'bg-green-100 text-green-800 border-green-500',
    'متوسط' => 'bg-yellow-100 text-yellow-800 border-yellow-500',
    'سخت' => 'bg-red-100 text-red-800 border-red-500',
];

$status_badges = [
    'pending' => '<span class="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full animate-pulse">در انتظار بررسی</span>',
    'approved' => '<span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">تأیید شده ✓</span>',
    'rejected' => '<span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">نیاز به اصلاح ✗</span>',
];
?>

<div class="space-y-6 animate-fade-in-up">
    
    <!-- هدر با آمار -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-black mb-2">چالش‌های تمرینی</h2>
                <p class="text-purple-100">مهارت خود را با حل تمرین‌ها تقویت کنید</p>
            </div>
            <div class="mt-4 md:mt-0 bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm">
                <div class="text-center">
                    <div class="text-4xl font-black mb-1"><?= count($content) ?></div>
                    <div class="text-sm font-medium">چالش در دسترس</div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($content)): ?>
        <div class="glass-card rounded-2xl shadow-xl p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 mb-2">تمرینی یافت نشد</h3>
            <p class="text-gray-500">هنوز تمرینی برای گروه شما اضافه نشده است.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($content as $index => $ex):
                $status = $submissions[$ex['id']] ?? null;
                $color = $difficulty_colors[$ex['difficulty']] ?? 'bg-gray-100 text-gray-800 border-gray-500';
            ?>
                <div class="glass-card rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 cursor-pointer group overflow-hidden"
                     data-open-practice-modal
                     data-id="<?= $ex['id'] ?>"
                     data-title="<?= htmlspecialchars($ex['title']) ?>"
                     data-description="<?= htmlspecialchars($ex['description']) ?>"
                     data-difficulty="<?= htmlspecialchars($ex['difficulty']) ?>">
                    
                    <div class="h-2 bg-gradient-to-r from-purple-500 to-pink-500"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-black text-xl shadow-lg">
                                <?= $index + 1 ?>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full border-2 <?= $color ?> flex items-center">
                                <?= htmlspecialchars($ex['difficulty']) ?>
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3 line-clamp-2 group-hover:text-purple-600 transition-colors">
                            <?= htmlspecialchars($ex['title']) ?>
                        </h3>

                        <?php if ($status): ?>
                            <div class="mb-4"><?= $status_badges[$status] ?></div>
                        <?php endif; ?>

                        <div class="w-full px-4 py-3 font-bold text-white bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg text-center">
                            <?= $status ? 'مشاهده / ویرایش' : 'شروع چالش' ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- مودال تمرین -->
<div id="practice-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-80 backdrop-blur-sm">
    <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 id="modal-title" class="text-2xl font-black text-white mb-2"></h3>
                    <span id="modal-difficulty-badge" class="inline-block"></span>
                </div>
                <button data-close-modal class="p-2 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6 max-h-screen overflow-y-auto">
            <div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-3">توضیحات چالش</h4>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p id="modal-description" class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap"></p>
                </div>
            </div>

            <form id="submit-code-form" class="space-y-4">
                <input type="hidden" name="exercise_id" id="modal-exercise-id">
                <textarea dir="ltr" name="code" rows="14" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-mono text-sm focus:ring-2 focus:ring-purple-500" placeholder="کد خود را اینجا بنویسید..." required></textarea>
                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-black rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all text-lg shadow-lg hover:shadow-xl">
                    ارسال کد برای بررسی
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('practice-modal');
    const form = document.getElementById('submit-code-form');
    const textarea = document.querySelector('#submit-code-form textarea[name="code"]');

    document.querySelectorAll('[data-open-practice-modal]').forEach(btn => {
        btn.onclick = async () => {
            // باز کردن مودال
            document.getElementById('modal-title').textContent = btn.dataset.title;
            document.getElementById('modal-description').textContent = btn.dataset.description;
            document.getElementById('modal-exercise-id').value = btn.dataset.id;

            const badge = document.getElementById('modal-difficulty-badge');
            const d = btn.dataset.difficulty;
            badge.className = d === 'آسان' ? 'px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold' :
                             d === 'متوسط' ? 'px-3 py-1 bg-yellow-500 text-white rounded-full text-xs font-bold' :
                             'px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold';
            badge.textContent = d;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // دریافت کد قبلی (اگر قبلاً ارسال کرده)
            const exerciseId = btn.dataset.id;
            const res = await fetch(`api_get_submission.php?exercise_id=${exerciseId}`);
            const data = await res.json();
            if (data.success && data.code) {
                textarea.value = data.code;
            } else {
                textarea.value = '';
            }
        };
    });

    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.onclick = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };
    });

    modal.onclick = (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    form.onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        const res = await fetch('api_submit_exercise.php', { method: 'POST', body: fd });
        const data = await res.json();
        alert(data.success ? 'کد با موفقیت ارسال شد! در انتظار بررسی...' : 'خطا: ' + data.message);
        if (data.success) location.reload();
    };
});
</script>