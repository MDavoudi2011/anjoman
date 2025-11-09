 
<?php
// $user در این فایل از dashboard.php قابل دسترس است

// خواندن محتوای آزمون
$tests_json = @file_get_contents('data/tests.json');
$all_content = $tests_json ? json_decode($tests_json, true) : [];
$test_data = $all_content[$user['group']] ?? null;

?>
<!-- این بخش معادل TestView.tsx است -->
<div class="animate-fade-in-up">
    <?php if (!$test_data): ?>
        <div class="flex flex-col items-center justify-center h-full p-8 text-center bg-white rounded-lg shadow-lg dark:bg-gray-800">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">خطا</h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                محتوای آزمون یافت نشد.
            </p>
      </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center h-full p-8 text-center bg-white rounded-lg shadow-lg dark:bg-gray-800">
          <h2 class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($test_data['title']); ?></h2>
          <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
            <?php echo htmlspecialchars($test_data['message']); ?>
          </p>
        </div>
    <?php endif; ?>
</div>
<!-- پایان TestView.tsx -->