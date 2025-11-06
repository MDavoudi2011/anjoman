<?php
session_start();
$ADMIN_PASSWORD = 'veryverysecret';
$DATA_FILE = 'data/results.json';

if (isset($_GET['logout'])) { session_destroy(); header('Location: admin.php'); exit; }
if (isset($_POST['password']) && hash_equals(crypt($_POST['password'], $ADMIN_PASSWORD), crypt($ADMIN_PASSWORD, $ADMIN_PASSWORD))) {
    $_SESSION['admin'] = true;
    header('Location: admin.php'); exit;
}

$isLoggedIn = isset($_SESSION['admin']);
$results = $isLoggedIn && file_exists($DATA_FILE) ? json_decode(file_get_contents($DATA_FILE), true) : [];
$results = is_array($results) ? array_reverse($results) : [];

$totalUsers = count($results);
$totalScore = array_sum(array_column($results, 'score'));
$avgScore = $totalUsers ? round($totalScore / $totalUsers, 1) : 0;
$cheaters = count(array_filter($results, fn($r) => $r['cheated'] ?? false));
$perfect = count(array_filter($results, fn($r) => $r['score'] == $r['total']));

if (isset($_GET['export'])) {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="نتایج_آزمون_' . date('Y-m-d') . '.xls"');
    echo "نام\tامتیاز\tکل\tتقلب\tتاریخ\tIP\n";
    foreach ($results as $r) {
        echo "{$r['name']}\t{$r['score']}\t{$r['total']}\t" . ($r['cheated'] ? 'بله' : 'خیر') . "\t{$r['timestamp']}\t{$r['ip']}\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت آزمون</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.0.3/Vazirmatn-font-face.css" rel="stylesheet" />
    <style>
        body, * { 
            font-family: 'Vazirmatn', sans-serif !important; 
        }
        .stat-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border: 1px solid #475569;
        }
        .table-container {
            background: #1e293b;
            border: 1px solid #475569;
        }
        .modal-box {
            background: #1e293b;
            border: 1px solid #475569;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100">

<?php if (!$isLoggedIn): ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="card w-96 bg-slate-800 shadow-2xl border border-slate-700">
        <form method="POST" class="card-body">
            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-center mb-6">ورود ادمین</h2>
            <input type="password" name="password" class="input input-bordered bg-slate-700 border-slate-600 text-white" placeholder="رمز عبور" required />
            <button type="submit" class="btn btn-primary mt-4">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                ورود
            </button>
        </form>
    </div>
</div>
<?php else: ?>

<!-- هدر -->
<div class="navbar bg-slate-800 shadow-lg border-b border-slate-700">
    <div class="flex-1">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <h1 class="text-2xl font-bold">پنل مدیریت آزمون</h1>
        </div>
    </div>
    <div class="flex-none gap-2">
        <a href="?logout=1" class="btn btn-error btn-sm gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            خروج
        </a>
    </div>
</div>

<div class="drawer lg:drawer-open">
    <input id="drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content flex flex-col">

        <!-- موبایل منو -->
        <div class="navbar bg-slate-800 lg:hidden border-b border-slate-700">
            <label for="drawer" class="btn btn-square btn-ghost">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
        </div>

        <main class="p-6">
            <h1 class="text-3xl font-bold mb-6 flex items-center gap-3">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                داشبورد نتایج
            </h1>

            <!-- آمار -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stat-card rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <div class="text-sm text-slate-400">کل شرکت‌کنندگان</div>
                    </div>
                    <div class="text-3xl font-bold text-blue-500"><?php echo $totalUsers; ?></div>
                </div>
                <div class="stat-card rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <div class="text-sm text-slate-400">میانگین</div>
                    </div>
                    <div class="text-3xl font-bold text-purple-500"><?php echo $avgScore; ?> / <?php echo $results[0]['total'] ?? 0; ?></div>
                </div>
                <div class="stat-card rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-sm text-slate-400">تقلب‌کار</div>
                    </div>
                    <div class="text-3xl font-bold text-red-500"><?php echo $cheaters; ?></div>
                </div>
                <div class="stat-card rounded-2xl shadow-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-slate-400">نمره کامل</div>
                    </div>
                    <div class="text-3xl font-bold text-green-500"><?php echo $perfect; ?></div>
                </div>
            </div>

            <!-- جستجو -->
            <div class="flex gap-4 mb-6">
                <div class="relative flex-1">
                    <svg class="absolute right-3 top-3 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="search" placeholder="جستجو نام، IP یا تاریخ..." class="input input-bordered w-full pr-10 bg-slate-800 border-slate-600 text-white" onkeyup="filter()" />
                </div>
                <button onclick="location.reload()" class="btn btn-outline gap-2 border-slate-600 text-slate-300 hover:bg-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    رفرش
                </button>
            </div>

            <!-- جدول -->
            <div class="table-container rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table w-full" id="resultsTable">
                        <thead>
                            <tr class="bg-slate-700 border-b border-slate-600">
                                <th class="text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        نام
                                    </div>
                                </th>
                                <th class="text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                        امتیاز
                                    </div>
                                </th>
                                <th class="text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        تقلب
                                    </div>
                                </th>
                                <th class="text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        تاریخ
                                    </div>
                                </th>
                                <th class="text-slate-200">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                        </svg>
                                        IP
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $r): ?>
                            <tr class="border-b border-slate-700 hover:bg-slate-800 transition-colors">
                                <td class="text-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                            <?php echo mb_substr($r['name'], 0, 1); ?>
                                        </div>
                                        <b><?php echo htmlspecialchars($r['name']); ?></b>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge <?php echo $r['score'] >= $r['total']*0.8 ? 'badge-success' : 'badge-warning'; ?> gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <?php echo $r['score']; ?>/<?php echo $r['total']; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge <?php echo $r['cheated'] ? 'badge-error' : 'badge-success'; ?> gap-2">
                                        <?php if ($r['cheated']): ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <?php else: ?>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <?php endif; ?>
                                        <?php echo $r['cheatCount'] ?? 0; ?> بار
                                    </div>
                                </td>
                                <td class="text-slate-300"><?php echo $r['timestamp']; ?></td>
                                <td class="text-slate-400 font-mono text-sm"><?php echo $r['ip'] ?? 'نامشخص'; ?></td>عع
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- سایدبار -->
    <div class="drawer-side">
        <label for="drawer" class="drawer-overlay"></label>
        <ul class="menu p-4 w-80 h-full bg-slate-800 border-l border-slate-700">
            <li class="mb-6">
                <div class="flex items-center gap-3 text-2xl font-bold pointer-events-none">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    منو
                </div>
            </li>
            <li>
                <a class="active bg-blue-600 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    داشبورد
                </a>
            </li>
            <li class="mt-auto">
                <a href="?logout=1" class="text-error hover:bg-red-900/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    خروج
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- مودال جزئیات -->
<dialog id="modal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
        <div class="flex items-center gap-3 mb-4">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 id="modalTitle" class="text-2xl font-bold"></h3>
        </div>
        <div id="modalBody"></div>
        <div class="modal-action">
            <button class="btn btn-error gap-2" onclick="document.getElementById('modal').close()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                بستن
            </button>
        </div>
    </div>
</dialog>

<script>
function filter() {
    const q = document.getElementById('search').value.toLowerCase();
    document.querySelectorAll('#resultsTable tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function showDetail(d) {
    document.getElementById('modalTitle').textContent = d.name + ' - جزئیات';
    let html = `<div class="grid grid-cols-2 gap-6 mb-6">
        <div class="stat-card rounded-xl p-6">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <div class="text-sm text-slate-400">امتیاز</div>
            </div>
            <div class="text-3xl font-bold text-blue-500">${d.score}/${d.total}</div>
        </div>
        <div class="stat-card rounded-xl p-6">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm text-slate-400">تقلب</div>
            </div>
            <div class="text-3xl font-bold ${d.cheated?'text-red-500':'text-green-500'}">${d.cheatCount ?? 0} بار</div>
        </div>
    </div>`;
    html += '<div class="table-container rounded-xl overflow-hidden"><table class="table w-full"><thead><tr class="bg-slate-700 border-b border-slate-600"><th class="text-slate-200">سوال</th><th class="text-slate-200">وضعیت</th><th class="text-slate-200">زمان</th></tr></thead><tbody>';
    (d.answers || []).forEach(a => {
        html += `<tr class="border-b border-slate-700 hover:bg-slate-800">
            <td class="text-slate-100">${a.q}</td>
            <td>${a.correct ? '<span class="badge badge-success gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>صحیح</span>' : '<span class="badge badge-error gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>غلط</span>'}</td>
            <td class="text-slate-300">${a.timeUsed ?? 0}ث</td>
        </tr>`;
    });
    html += '</tbody></table></div>';
    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('modal').showModal();
}
</script>

<?php endif; ?>
</body>
</html>