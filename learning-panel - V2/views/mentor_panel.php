<?php
// views/mentor_panel.php
if (!isset($_SESSION['user']['username'])) {
    echo '<div class="p-10 text-center"><h2 class="text-3xl font-black text-red-600">لطفاً وارد شوید!</h2></div>';
    return;
}

$user = $_SESSION['user'];
$username = $user['username'];

try {
    $currentUser = Config::getUser($username);
} catch (Exception $e) {
    echo '<div class="p-10 text-center text-red-600">خطا در بارگذاری اطلاعات</div>';
    return;
}

if (!$currentUser) {
    echo '<div class="p-10 text-center text-red-600">کاربر یافت نشد!</div>';
    return;
}

$userId = $currentUser['id'];
$userGroup = $currentUser['user_group'] ?? 'all';
$userName = htmlspecialchars($currentUser['name'] ?? $username);

$role = trim(strtolower($currentUser['role'] ?? ''));
if (!in_array($role, ['mentor', 'admin', 'supermentor'])) {
    echo '<div class="p-10 text-center"><h2 class="text-4xl font-black text-red-600">فقط منتورها!</h2></div>';
    return;
}

$pdo = Config::db();

// --- آمار ---
$filter = $userGroup !== 'all' ? "AND u.user_group = ?" : "";
$params = $userGroup !== 'all' ? [$userId, $userGroup] : [$userId];

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users u WHERE u.mentor_id = ? $filter AND u.role = 'student'");
    $stmt->execute($params);
    $total_students = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM exercise_submissions es JOIN users u ON es.user_id = u.id WHERE u.mentor_id = ? $filter AND es.status = 'pending'");
    $stmt->execute($params);
    $pending_exercises = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM exam_submissions es JOIN users u ON es.user_id = u.id WHERE u.mentor_id = ? $filter AND es.status = 'submitted'");
    $stmt->execute($params);
    $pending_exams = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM exams WHERE group_name = ? AND is_active = 1");
    $stmt->execute([$userGroup]);
    $active_exams = (int)$stmt->fetchColumn();

} catch (Exception $e) {
    $total_students = $pending_exercises = $pending_exams = $active_exams = 0;
}

if ($_POST['action'] ?? '' === 'grade_exercise') {
    $sub_id = (int)($_POST['submission_id'] ?? 0);
    $status = $_POST['status'] === 'approved' ? 'approved' : 'rejected';
    $comment = trim($_POST['comment'] ?? '');

    try {
        $stmt = $pdo->prepare("UPDATE exercise_submissions SET status = ?, mentor_comment = ?, reviewed_at = NOW() WHERE id = ? AND status = 'pending'");
        $stmt->execute([$status, $comment, $sub_id]);

        // --- فیکس شماره ۱: بررسی اینکه آیا ردیفی واقعا آپدیت شد؟ ---
        if ($stmt->rowCount() > 0) {
            $message = 'تمرین با موفقیت تصحیح شد!';
        } else {
            $message = 'خطا: این تمرین قبلاً تصحیح شده یا یافت نشد.';
        }
    } catch (Exception $e) {
        $message = 'خطای پایگاه داده: ' . $e->getMessage();
    }

    // --- فیکس شماره ۲: استفاده از location.replace و خروج از اسکریپت ---
    // (location.replace بهتر از reload است چون از هشدار resubmit فرم جلوگیری می‌کند)
    echo "<script>
            alert('" . addslashes($message) . "'); 
            location.replace(location.pathname + location.search);
          </script>";
    exit; // بسیار مهم: از رندر شدن بقیه صفحه جلوگیری می‌کند
}

// --- پردازش تصحیح آزمون ---
if ($_POST['action'] ?? '' === 'grade_exam') {
    $sub_id = (int)($_POST['exam_submission_id'] ?? 0);
    $score = (int)($_POST['score'] ?? 0);
    $comment = trim($_POST['exam_comment'] ?? '');

    try {
        $stmt = $pdo->prepare("UPDATE exam_submissions SET status = 'graded', score = ?, mentor_comment = ?, reviewed_at = NOW() WHERE id = ? AND status = 'submitted'");
        $stmt->execute([$score, $comment, $sub_id]);

        // --- فیکس شماره ۱: بررسی اینکه آیا ردیفی واقعا آپدیت شد؟ ---
        if ($stmt->rowCount() > 0) {
            $message = 'آزمون با موفقیت نمره‌دهی شد!';
        } else {
            $message = 'خطا: این آزمون قبلاً نمره‌دهی شده یا یافت نشد.';
        }
    } catch (Exception $e) {
        $message = 'خطای پایگاه داده: ' . $e->getMessage();
    }

    // --- فیکس شماره ۲: استفاده از location.replace و خروج از اسکریپت ---
    echo "<script>
            alert('" . addslashes($message) . "'); 
            location.replace(location.pathname + location.search);
          </script>";
    exit; // بسیار مهم
}
?>

<div class="max-w-6xl mx-auto space-y-8 p-6">

    <!-- خوش‌آمدگویی -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-3xl shadow-2xl p-10 text-white">
        <h1 class="text-5xl font-black mb-2">سلام منتور <?= $userName ?>! 👋</h1>
        <p class="text-2xl opacity-90">داشبورد کامل تصحیح و نظارت</p>
    </div>

    <!-- آمار سریع -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card rounded-2xl p-8 text-center">
            <div class="text-5xl font965-black text-blue-600"><?= $total_students ?></div>
            <p class="text-lg font-bold text-gray-600 mt-2">دانش‌آموز</p>
        </div>
        <div class="glass-card rounded-2xl p-8 text-center">
            <div class="text-5xl font-black text-yellow-600"><?= $pending_exercises ?></div>
            <p class="text-lg font-bold text-gray-600 mt-2">تمرین در انتظار</p>
        </div>
        <div class="glass-card rounded-2xl p-8 text-center">
            <div class="text-5xl font-black text-purple-600"><?= $pending_exams ?></div>
            <p class="text-lg font-bold text-gray-600 mt-2">آزمون تحویل‌شده</p>
        </div>
        <div class="glass-card rounded-2xl p-8 text-center">
            <div class="text-5xl font-black text-green-600"><?= $active_exams ?></div>
            <p class="text-lg font-bold text-gray-600 mt-2">آزمون فعال</p>
        </div>
    </div>

    <!-- تمرین‌های در انتظار تصحیح -->
    <?php if ($pending_exercises > 0): ?>
    <div class="glass-card rounded-2xl p-8">
        <h2 class="text-3xl font-black mb-6 text-center">تمرین‌های در انتظار تصحیح 🚀</h2>
        <div class="space-y-6">
            <?php
            $stmt = $pdo->prepare("
                SELECT es.*, u.name as student_name, u.username 
                FROM exercise_submissions es 
                JOIN users u ON es.user_id = u.id 
                WHERE u.mentor_id = ? $filter AND es.status = 'pending'
                ORDER BY es.submitted_at DESC
            ");
            $stmt->execute($params);
            while ($row = $stmt->fetch()):
            ?>
            <div class="border-2 border-yellow-400 rounded-xl p-6 bg-yellow-50">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <strong><?= htmlspecialchars($row['student_name'] ?? $row['username']) ?></strong>
                        <span class="text-sm text-gray-600"> — <?= $row['exercise_id'] ?></span>
                    </div>
                    <span class="text-sm text-gray-500"><?= $row['submitted_at'] ?></span>
                </div>
                <pre class="bg-white p-4 rounded-lg border overflow-x-auto mb-4"><code><?= htmlspecialchars($row['code']) ?></code></pre>
                <form method="post" class="flex gap-4">
                    <input type="hidden" name="submission_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="action" value="grade_exercise">
                    <textarea name="comment" placeholder="نظر شما..." class="flex-1 p-3 border rounded-lg"></textarea>
                    <button type="submit" name="status" value="approved" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">تأیید ✅</button>
                    <button type="submit" name="status" value="rejected" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">رد ❌</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- آزمون‌های تحویل‌شده -->
    <?php if ($pending_exams > 0): ?>
    <div class="glass-card rounded-2xl p-8">
        <h2 class="text-3xl font-black mb-6 text-center">آزمون‌های تحویل‌شده 📝</h2>
        <div class="space-y-6">
            <?php
            $stmt = $pdo->prepare("
                SELECT es.*, u.name as student_name, u.username, ex.title 
                FROM exam_submissions es 
                JOIN users u ON es.user_id = u.id 
                JOIN exams ex ON es.exam_id = ex.id 
                WHERE u.mentor_id = ? $filter AND es.status = 'submitted'
                ORDER BY es.submitted_at DESC
            ");
            $stmt->execute($params);
            while ($row = $stmt->fetch()):
            ?>
            <div class="border-2 border-purple-400 rounded-xl p-6 bg-purple-50">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <strong><?= htmlspecialchars($row['student_name'] ?? $row['username']) ?></strong>
                        <span class="text-sm text-gray-600"> — <?= htmlspecialchars($row['title']) ?></span>
                    </div>
                    <span class="text-sm text-gray-500"><?= $row['submitted_at'] ?></span>
                </div>
                <div class="bg-white p-4 rounded-lg border mb-4">
                    <strong>پاسخ‌ها:</strong><br>
                    <?= nl2br(htmlspecialchars($row['answers'])) ?>
                </div>
                <form method="post" class="flex gap-4 items-center">
                    <input type="hidden" name="exam_submission_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="action" value="grade_exam">
                    <input type="number" name="score" min="0" max="100" placeholder="نمره" class="w-24 p-3 border rounded-lg" required>
                    <textarea name="exam_comment" placeholder="نظر شما..." class="flex-1 p-3 border rounded-lg"></textarea>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">ثبت نمره ✅</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- پیام پایانی -->
    <?php if ($pending_exercises == 0 && $pending_exams == 0): ?>
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-3xl p-12 text-white text-center">
        <h3 class="text-4xl font-black mb-4">عالیه! همه چیز تصحیح شده ✅</h3>
        <p class="text-2xl">منتور فوق‌العاده‌ای هستی! 🌟</p>
    </div>
    <?php else: ?>
    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-3xl p-12 text-white text-center">
        <h3 class="text-4xl font-black mb-4">هنوز <?= $pending_exercises + $pending_exams ?> مورد در انتظار داری!</h3>
        <p class="text-2xl">زود باش تصحیح کن قهرمان! 💪</p>
    </div>
    <?php endif; ?>

</div>