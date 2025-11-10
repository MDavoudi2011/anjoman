<?php
// migrate_to_db.php
$host = 'localhost';
$db   = 'bahonar3';
$user = 'root';        // اگه یوزر دیگه‌ای داری عوض کن
$pass = '0315324457Mm';            // پسورد دیتابیس
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<h2 style='color:green;'>اتصال به دیتابیس اوکی ✅</h2>";
} catch (PDOException $e) {
    die("اتصال شکست: " . $e->getMessage());
}

// 1. انتقال کاربران
$users = json_decode(file_get_contents('data/users.json'), true);
$stmt = $pdo->prepare("INSERT INTO users (username, password, name, user_group) VALUES (?, ?, ?, ?)");
foreach ($users as $u) {
    $stmt->execute([$u['username'], $u['password'], $u['name'], $u['group']]);
}
echo "<p>کاربرها منتقل شدن ({count($users)} نفر) ✅</p>";

// 2. انتقال ویدیوها
$videos = json_decode(file_get_contents('data/videos.json'), true);
$catStmt = $pdo->prepare("INSERT INTO video_categories (group_name, title) VALUES (?, ?)");
$vidStmt = $pdo->prepare("INSERT INTO videos (id, category_id, title, duration, thumbnail_url, video_url) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($videos as $group => $categories) {
    foreach ($categories as $cat) {
        $catStmt->execute([$group, $cat['categoryTitle']]);
        $catId = $pdo->lastInsertId();

        foreach ($cat['videos'] as $v) {
            $vidStmt->execute([
                $v['id'],
                $catId,
                $v['title'],
                $v['duration'],
                $v['thumbnailUrl'],
                $v['videoUrl']
            ]);
        }
    }
}
echo "<p>ویدیوها منتقل شدن ✅</p>";

// 3. تمرین‌ها
$exercises = json_decode(file_get_contents('data/exercises.json'), true);
$exStmt = $pdo->prepare("INSERT INTO exercises (id, group_name, title, difficulty, description) VALUES (?, ?, ?, ?, ?)");
foreach ($exercises as $group => $items) {
    foreach ($items as $ex) {
        $exStmt->execute([
            $ex['id'],
            $group,
            $ex['title'],
            $ex['difficulty'],
            $ex['description']
        ]);
    }
}
echo "<p>تمرین‌ها منتقل شدن ✅</p>";

// 4. آزمون‌ها
$tests = json_decode(file_get_contents('data/tests.json'), true);
$testStmt = $pdo->prepare("INSERT INTO tests (group_name, title, active, message) VALUES (?, ?, ?, ?)");
foreach ($tests as $group => $t) {
    $testStmt->execute([$group, $t['title'], $t['active'] ? 1 : 0, $t['message']]);
}
echo "<p>آزمون‌ها منتقل شدن ✅</p>";

echo "<h1 style='color:green; text-align:center;'>همه چیز با موفقیت منتقل شد! 🎉🎉🎉</h1>";
echo "<p>حالا می‌تونی فایل‌های JSON رو پاک کنی یا نگه داری برای بکاپ.</p>";
?>