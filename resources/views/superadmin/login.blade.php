<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>超級管理員登入 - ECount</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-block bg-white rounded-full p-4 shadow-lg mb-4">
                <span class="text-5xl">🛡️</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">ECount 超級管理員</h1>
            <p class="text-indigo-100">系統管理後台</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">登入</h2>

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

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

            <form method="POST" action="{{ route('superadmin.login') }}" {{ $isLocked ? 'onsubmit=return false' : '' }}>
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        required
                        data-rules="required|email"
                        data-label="Email"
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="admin@ecount.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        密碼
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            data-rules="required|min:6"
                            data-label="密碼"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror"
                            placeholder="••••••••"
                        >
                        <button type="button" onclick="togglePassword('password', this)"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Captcha -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">驗證碼</label>
                    <input
                        type="text"
                        name="captcha"
                        id="captcha"
                        autocomplete="off"
                        maxlength="3"
                        required
                        data-rules="required|min:3|max:3"
                        data-label="驗證碼"
                        placeholder="輸入驗證碼（不區分大小寫）"
                        class="w-full px-4 py-3 border @error('captcha') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 tracking-widest font-mono mb-2"
                    >
                    @error('captcha')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="flex items-center gap-3">
                        <div id="captcha-img" class="border border-gray-200 rounded select-none">
                            {!! $captchaSvg !!}
                        </div>
                        <button type="button" onclick="refreshCaptcha()"
                                class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            重整
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        記住我
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105"
                >
                    登入
                </button>
            </form>

            <!-- 安全資訊 -->
            <div class="mt-5 pt-4 border-t border-gray-100">
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        圖形驗證碼保護
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        登入失敗 3 次後鎖定 15 分鐘
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        CSRF Token 防護
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-indigo-100 text-sm">
                © 2026 ECount. All rights reserved.
            </p>
        </div>
    </div>
    <script>
    function refreshCaptcha() {
        fetch('{{ route('superadmin.captcha.refresh') }}')
            .then(r => r.text())
            .then(svg => {
                document.getElementById('captcha-img').innerHTML = svg;
                document.getElementById('captcha').value = '';
                document.getElementById('captcha').focus();
            });
    }
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        const paths = btn.querySelectorAll('path');
        if (isHidden) {
            paths[0].setAttribute('d', 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21');
            paths[1] && paths[1].remove();
        } else {
            paths[0].setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z');
            if (!paths[1]) {
                const p = document.createElementNS('http://www.w3.org/2000/svg','path');
                p.setAttribute('stroke-linecap','round');
                p.setAttribute('stroke-linejoin','round');
                p.setAttribute('stroke-width','2');
                p.setAttribute('d','M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z');
                btn.querySelector('svg').appendChild(p);
            }
        }
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
