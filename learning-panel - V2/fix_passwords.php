<?php
define('APP_ACCESS', true);

require_once 'config.php';

$pdo = Config::db();

$stmt = $pdo->query("SELECT id, username, password FROM users");
while ($user = $stmt->fetch()) {
    if (!password_get_info($user['password'])['algo']) {
        $newHash = password_hash($user['password'], PASSWORD_DEFAULT);
        $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upd->execute([$newHash, $user['id']]);
        echo "پسورد {$user['username']} آپدیت شد<br>";
    }
}
echo "تموم شد!";
?>