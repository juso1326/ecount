<!-- 日期資訊 -->
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
        日期資訊
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 到職日 -->
        <div>
            <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">到職日</label>
            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $user->hire_date ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 離職日 -->
        <div>
            <label for="resign_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">離職日</label>
            <input type="date" name="resign_date" id="resign_date" value="{{ old('resign_date', $user->resign_date ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- 停權日 -->
        <div>
            <label for="suspend_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">停權日</label>
            <input type="date" name="suspend_date" id="suspend_date" value="{{ old('suspend_date', $user->suspend_date ?? '') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
</div>
