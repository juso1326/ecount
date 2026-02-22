<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>找不到此租戶 — Ecount</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="text-8xl mb-4">🏢</div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">找不到此租戶</h1>
        <p class="text-gray-500 mb-2">
            網域 <code class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 text-sm">{{ $domain }}</code> 尚未開通或已停用。
        </p>
        <p class="text-sm text-gray-400 mb-8">請確認網址是否正確，或聯絡系統管理員。</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="http://{{ config('app.domain', 'ecount.test') }}"
               class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                回到官網
            </a>
            <a href="mailto:support@{{ config('app.domain', 'ecount.test') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-sm font-medium">
                聯絡支援
            </a>
        </div>
    </div>
</body>
</html>
