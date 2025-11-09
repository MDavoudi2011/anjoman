 
<?php
session_start();

// پاک کردن تمام متغیرهای سشن
$_SESSION = array();

// حذف کوکی سشن
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// در نهایت، سشن رو از بین ببر
session_destroy();

// کاربر رو به صفحه لاگین برگردون
header("Location: index.php");
exit;
?>