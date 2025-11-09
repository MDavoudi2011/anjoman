 
<?php
// $user در این فایل از dashboard.php قابل دسترس است

// خواندن محتوای تمرین
$exercises_json = @file_get_contents('data/exercises.json');
$all_content = $exercises_json ? json_decode($exercises_json, true) : [];
$content = $all_content[$user['group']] ?? [];

$difficulty_colors = [
    'آسان' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'متوسط' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'سخت' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
];

?>
<!-- این بخش معادل PracticeView.tsx است -->
<div class="space-y-8 animate-fade-in-up">
  <h2 class="pb-2 text-3xl font-extrabold text-gray-800 border-b-4 border-indigo-500 dark:text-white">چالش‌های تمرینی</h2>
  
  <?php if (empty($content)): ?>
    <div class="text-center p-8 bg-white rounded-lg shadow-lg dark:bg-gray-800">
        <p class="text-lg text-gray-600 dark:text-gray-400">چالش تمرینی برای گروه شما یافت نشد.</p>
    </div>
  <?php else: ?>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($content as $challenge):
          $color_class = $difficulty_colors[$challenge['difficulty']] ?? 'bg-gray-100 text-gray-800';
      ?>
        <div class="flex flex-col justify-between p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800">
          <div>
            <div class="flex items-start justify-between mb-2">
              <h3 class="text-xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($challenge['title']); ?></h3>
              <span class="px-2.5 py-1 text-xs font-semibold rounded-full <?php echo $color_class; ?>">
                <?php echo htmlspecialchars($challenge['difficulty']); ?>
              </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              سطح سختی: <?php echo htmlspecialchars($challenge['difficulty']); ?>
            </p>
          </div>
          <button
            class="w-full px-4 py-2 mt-4 font-semibold text-white transition-colors bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            data-open-practice-modal
            data-title="<?php echo htmlspecialchars($challenge['title']); ?>"
            data-description="<?php echo htmlspecialchars($challenge['description']); ?>"
          >
            مشاهده جزئیات
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<!-- پایان PracticeView.tsx -->

<!-- 
  مودال جزئیات تمرین
  این بخش معادل Modal.tsx است
  این مودال توسط جاوا اسکریپت در dashboard.php کنترل می‌شود
-->
<div
  id="practice-modal"
  class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black bg-opacity-70"
>
  <div
    class="relative w-full max-w-lg p-6 bg-white rounded-lg shadow-xl dark:bg-gray-800 animate-fade-in-up"
  >
    <div class="flex items-start justify-between pb-4 border-b rounded-t dark:border-gray-600">
      <h3 id="modal-title" class="text-2xl font-semibold text-gray-900 dark:text-white">
        <!-- عنوان مودال اینجا قرار می‌گیرد -->
      </h3>
      <button
        type="button"
        class="inline-flex items-center p-1.5 mr-auto text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white"
        data-close-modal
      >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd"></path></svg>
        <span class="sr-only">بستن مودال</span>
      </button>
    </div>
    <div class="pt-4 space-y-4">
        <p id="modal-description" class="text-gray-600 dark:text-gray-300">
            <!-- توضیحات مودال اینجا قرار می‌گیرد -->
        </p>
        <button class="w-full px-4 py-2 font-semibold text-white bg-gray-500 rounded-md cursor-not-allowed hover:bg-gray-600 focus:outline-none">
          آپلود کد (غیرفعال)
        </button>
    </div>
  </div>
</div>