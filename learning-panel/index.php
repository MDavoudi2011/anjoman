<?php
session_start(); 

// اگر کاربر از قبل لاگین کرده، اون رو به داشبورد بفرست
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = null;

// بررسی اینکه آیا فرم ارسال شده؟
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users_json = @file_get_contents('data/users.json');
    if ($users_json === false) {
        $error = "خطا: فایل اطلاعات کاربران یافت نشد.";
    } else {
        $users = json_decode($users_json, true);
        $found_user = null;

        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                $found_user = $user;
                break;
            }
        }

        if ($found_user) {
            unset($found_user['password']); 
            $_SESSION['user'] = $found_user;
            header("Location: dashboard.php?view=training"); // با ویوی پیش‌فرض
            exit;
        } else {
            $error = 'نام کاربری یا رمز عبور اشتباه است.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ورود - پلتفرم آموزشی ساده</title>
    <!-- فونت وزیرمتن -->
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Vazirmatn', 'sans-serif'],
            },
            // انیمیشن‌های مشابه فایل React
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up 0.3s ease-out forwards',
            },
          },
        },
      }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans">
    
    <!-- این بخش معادل دقیق AuthScreen.tsx است -->
    <div class="flex items-center justify-center min-h-screen">
      <div class="w-full max-w-sm p-8 space-y-8 bg-white rounded-2xl shadow-xl dark:bg-gray-800">
        <div>
          <h2 class="text-3xl font-extrabold text-center text-gray-900 dark:text-white">
            ورود به پلتفرم آموزشی
          </h2>
          <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
            نام کاربری و رمز عبور خود را وارد کنید
          </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="index.php">
          <div class="space-y-4 rounded-md shadow-sm">
            <div>
              <label for="username" class="sr-only">نام کاربری</label>
              <input
                id="username"
                name="username"
                type="text"
                required
                class="relative block w-full px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                placeholder="نام کاربری"
              />
            </div>
            <div>
              <label for="password" class="sr-only">رمز عبور</label>
              <input
                id="password"
                name="password"
                type="password"
                required
                class="relative block w-full px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                placeholder="رمز عبور"
              />
            </div>
          </div>
          
          <?php if ($error): ?>
            <p class="text-sm text-center text-red-500"><?php echo $error; ?></p>
          <?php endif; ?>

          <div>
            <button
              type="submit"
              class="relative flex justify-center w-full px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md group hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-indigo-400 disabled:cursor-not-allowed"
            >
              ورود
            </button>
          </div>
        </form>
      </div>
    </div>
    <!-- پایان AuthScreen.tsx -->

</body>
</html>