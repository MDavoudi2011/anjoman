<?php
if (!isset($user) || !isset($user['username'])) {
    header("Location: login.php");
    exit;
}
$currentUser = Config::getUser($user['username']);
$userGroup = $currentUser['user_group'];
?>

<div class="max-w-4xl mx-auto p-6">
    <div id="exam-loading" class="text-center py-20">
        <div class="text-2xl font-bold">در حال بارگذاری آزمون...</div>
    </div>

    <div id="exam-container" class="hidden space-y-8"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('exam-container');
    const loading = document.getElementById('exam-loading');

    const res = await fetch('exam_api.php?action=get_exam');
    const data = await res.json();

    if (!data.success) {
        loading.innerHTML = `<div class="glass-card p-12 text-center"><h2 class="text-3xl font-black mb-4">آزمونی فعال نیست</h2><p>${data.message}</p></div>`;
        return;
    }

    const exam = data.exam;
    loading.classList.add('hidden');
    container.classList.remove('hidden');

    let seconds = exam.duration * 60;
    const timerEl = `<div class="text-4xl font-bold text-red-600 text-center mb-8" id="timer">00:${String(exam.duration).padStart(2, '0')}:00</div>`;

    container.innerHTML = `
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl p-8 text-white text-center">
            <h1 class="text-4xl font-black mb-3">${exam.title}</h1>
            <p class="text-xl">${exam.description || ''}</p>
        </div>
        ${timerEl}
        <form id="exam-form">
            <input type="hidden" name="exam_id" value="${exam.id}">
            <div class="glass-card rounded-2xl p-8 space-y-10">
                ${exam.questions.map((q, i) => `
                    <div class="border-b pb-8 last:border-0">
                        <div class="flex gap-4">
                            <span class="text-2xl font-black text-purple-600">${i + 1}</span>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold mb-6">${q.question}</h3>
                                ${q.type === 'multiple' ? q.options.map(opt => `
                                    <label class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg mb-3 cursor-pointer hover:bg-gray-100">
                                        <input type="radio" name="q${q.id}" value="${opt}" class="w-5 h-5 text-purple-600">
                                        <span class="mr-3">${opt}</span>
                                    </label>
                                `).join('') : `
                                    <textarea name="q${q.id}" rows="5" class="w-full p-4 border rounded-lg" placeholder="پاسخ خود را بنویسید..."></textarea>
                                `}
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="flex gap-4 justify-center mt-10">
                <button type="button" id="save-btn" class="px-8 py-4 bg-gray-600 text-white font-bold rounded-xl">ذخیره موقت</button>
                <button type="submit" class="px-10 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-black rounded-xl text-xl">تحویل آزمون</button>
            </div>
        </form>
    `;

    // شروع آزمون
    await fetch('exam_api.php?action=start', { method: 'POST', body: new FormData(document.getElementById('exam-form')) });

    // بارگذاری پاسخ‌های قبلی
    const ansRes = await fetch(`exam_api.php?action=get_answers&exam_id=${exam.id}`);
    const ansData = await ansRes.json();
    if (ansData.answers) {
        Object.keys(ansData.answers).forEach(key => {
            const el = document.querySelector(`[name="${key}"]`);
            if (el) {
                if (el.type === 'radio') {
                    document.querySelector(`[name="${key}"][value="${ansData.answers[key]}"]`)?.setAttribute('checked', true);
                } else {
                    el.value = ansData.answers[key];
                }
            }
        });
    }

    // تایمر
    const timer = setInterval(() => {
        seconds--;
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        document.getElementById('timer').textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        if (seconds <= 0) {
            clearInterval(timer);
            alert('زمان تمام شد!');
            document.querySelector('#exam-form').requestSubmit();
        }
    }, 1000);

    // ذخیره موقت
    document.getElementById('save-btn').onclick = async () => {
        const fd = new FormData(document.getElementById('exam-form'));
        fd.append('action', 'save');
        fd.append('answers', JSON.stringify(Object.fromEntries(fd)));
        await fetch('exam_api.php', { method: 'POST', body: fd });
        alert('ذخیره شد!');
    };

    // ارسال نهایی
    document.getElementById('exam-form').onsubmit = async (e) => {
        e.preventDefault();
        if (!confirm('مطمئنی می‌خوای تحویل بدی؟')) return;
        const fd = new FormData(e.target);
        fd.append('action', 'submit');
        await fetch('exam_api.php', { method: 'POST', body: fd });
        alert('آزمون با موفقیت تحویل شد!');
        location.reload();
    };

    window.onbeforeunload = () => "آزمون در حال انجام است!";
});
</script>