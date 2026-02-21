<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入 - Ecount 專案管理系統</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo -->
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Ecount 專案管理系統
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    登入您的帳號
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                {{-- 鎖定提示 --}}
                @if($isLocked)
                <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">帳號已暫時鎖定</p>
                        <p class="text-xs mt-0.5">請 <span id="lockCountdown" class="font-mono font-bold">{{ $lockSeconds }}</span> 秒後再試</p>
                    </div>
                </div>
                @elseif($attempts > 0)
                <div class="mb-4 bg-orange-50 border border-orange-300 text-orange-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    還可嘗試 <strong>{{ $remaining }}</strong> 次，超過將鎖定 15 分鐘
                </div>
                @endif

                <form class="space-y-6" action="{{ route('tenant.login.submit') }}" method="POST" {{ $isLocked ? 'onsubmit=return false' : '' }}>
                    @csrf

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-400 px-4 py-3 rounded relative" role="alert">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email
                        </label>
                        <div class="mt-1">
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   autocomplete="email" 
                                   required
                                   data-rules="required|email"
                                   data-label="Email"
                                   value="{{ old('email') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            密碼
                        </label>
                        <div class="mt-1 relative">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="current-password" 
                                   required
                                   data-rules="required|min:6"
                                   data-label="密碼"
                                   class="appearance-none block w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <button type="button" onclick="togglePassword('password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg id="eye-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            驗證碼
                        </label>
                        <input id="captcha"
                               name="captcha"
                               type="text"
                               autocomplete="off"
                               maxlength="3"
                               required
                               data-rules="required|min:3|max:3"
                               data-label="驗證碼"
                               placeholder="輸入驗證碼（不區分大小寫）"
                               class="appearance-none block w-full px-3 py-2 border @error('captcha') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm tracking-widest font-mono">
                        @error('captcha')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <div class="flex items-center gap-3 mt-2">
                            <div id="captcha-img" class="border border-gray-200 dark:border-gray-600 rounded select-none">
                                {!! $captchaSvg !!}
                            </div>
                            <button type="button" onclick="refreshCaptcha()"
                                    class="text-sm text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                重整
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" 
                                   name="remember" 
                                   type="checkbox"
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                記住我
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                                忘記密碼？
                            </a>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                            登入
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 dark:text-gray-400">
                © 2026 Ecount. All rights reserved.
            </p>
        </div>
    </div>
    <script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.querySelector('svg').style.opacity = isText ? '1' : '0.5';
    }
    function refreshCaptcha() {
        fetch('{{ route('tenant.captcha.refresh') }}')
            .then(r => r.text())
            .then(svg => {
                document.getElementById('captcha-img').innerHTML = svg;
                document.getElementById('captcha').value = '';
                document.getElementById('captcha').focus();
            });
    }
    @if($isLocked)
    (function() {
        let sec = {{ $lockSeconds }};
        const el = document.getElementById('lockCountdown');
        if (!el) return;
        const t = setInterval(() => {
            sec--;
            if (sec <= 0) { clearInterval(t); location.reload(); return; }
            el.textContent = sec;
        }, 1000);
    })();
    @endif
    </script>
</body>
</html>
