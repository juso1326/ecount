<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>系統管理員登入 — ECount</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .input-field {
            width: 100%; padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db; border-radius: 0.5rem;
            font-size: 0.875rem; color: #111827;
            transition: border-color .15s, box-shadow .15s;
            outline: none; background: #fff;
        }
        .input-field:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
        .input-field.error { border-color: #ef4444; }
        .input-field::placeholder { color: #9ca3af; }
        .btn-primary {
            width: 100%; padding: .7rem 1rem;
            background: #4f46e5; color: #fff;
            font-weight: 600; font-size: .9rem;
            border-radius: .5rem; border: none; cursor: pointer;
            transition: background .15s, transform .1s;
            letter-spacing: .01em;
        }
        .btn-primary:hover:not(:disabled) { background: #4338ca; }
        .btn-primary:active:not(:disabled) { transform: scale(.98); }
        .btn-primary:disabled { opacity: .55; cursor: not-allowed; }
    </style>
</head>
<body style="background:#f1f5f9; min-height:100vh; display:flex; align-items:center; justify-content:center;">

<div style="width:100%; max-width:400px; padding:1rem;">

    {{-- Brand --}}
    <div style="text-align:center; margin-bottom:2rem;">
        <div style="display:inline-flex; align-items:center; justify-content:center;
                    width:52px; height:52px; border-radius:14px;
                    background:linear-gradient(135deg,#4f46e5,#7c3aed);
                    box-shadow:0 4px 14px rgba(79,70,229,.35); margin-bottom:.875rem;">
            <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <h1 style="font-size:1.25rem; font-weight:700; color:#0f172a; margin:0 0 .25rem;">ECount 後台管理</h1>
        <p style="font-size:.8125rem; color:#64748b; margin:0;">僅限授權人員登入</p>
    </div>

    {{-- Card --}}
    <div style="background:#fff; border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.08), 0 8px 32px rgba(0,0,0,.07); padding:2rem;">

        {{-- Alerts --}}
        @if($errors->any())
        <div style="margin-bottom:1rem; padding:.75rem 1rem; background:#fef2f2; border:1px solid #fecaca; border-radius:.5rem; font-size:.8125rem; color:#b91c1c;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        @if(session('error'))
        <div style="margin-bottom:1rem; padding:.75rem 1rem; background:#fef2f2; border:1px solid #fecaca; border-radius:.5rem; font-size:.8125rem; color:#b91c1c;">
            {{ session('error') }}
        </div>
        @endif

        @if($isLocked)
        <div style="margin-bottom:1rem; padding:.75rem 1rem; background:#fef2f2; border:1px solid #fca5a5; border-radius:.5rem; font-size:.8125rem; color:#dc2626; display:flex; gap:.625rem; align-items:flex-start;">
            <svg style="flex-shrink:0;margin-top:1px" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <div>
                <div style="font-weight:600;">帳號暫時鎖定</div>
                <div style="margin-top:.125rem;">請 <span id="lockCountdown" style="font-family:monospace; font-weight:700;">{{ $lockSeconds }}</span> 秒後再試</div>
            </div>
        </div>
        @elseif($attempts > 0)
        <div style="margin-bottom:1rem; padding:.625rem .875rem; background:#fffbeb; border:1px solid #fcd34d; border-radius:.5rem; font-size:.8125rem; color:#92400e; display:flex; gap:.5rem; align-items:center;">
            <svg style="flex-shrink:0" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            還可嘗試 <strong>{{ $remaining }}</strong> 次，超過將鎖定 15 分鐘
        </div>
        @endif

        <form method="POST" action="{{ route('superadmin.login') }}" {{ $isLocked ? 'onsubmit="return false"' : '' }}>
            @csrf

            {{-- Email --}}
            <div style="margin-bottom:1rem;">
                <label for="email" style="display:block; font-size:.8125rem; font-weight:500; color:#374151; margin-bottom:.375rem;">電子郵件</label>
                <input type="email" name="email" id="email"
                    value="{{ old('email') }}"
                    class="input-field {{ $errors->has('email') ? 'error' : '' }}"
                    placeholder="admin@example.com"
                    autofocus required>
                @error('email')
                <p style="margin:.375rem 0 0; font-size:.75rem; color:#dc2626;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div style="margin-bottom:1rem;">
                <label for="password" style="display:block; font-size:.8125rem; font-weight:500; color:#374151; margin-bottom:.375rem;">密碼</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="password"
                        class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                        style="padding-right:2.75rem;"
                        placeholder="••••••••"
                        required>
                    <button type="button" onclick="togglePassword('password', this)"
                        style="position:absolute; right:.625rem; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9ca3af; padding:.25rem; display:flex;" title="顯示/隱藏密碼">
                        <svg class="eye-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                <p style="margin:.375rem 0 0; font-size:.75rem; color:#dc2626;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Captcha --}}
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:.8125rem; font-weight:500; color:#374151; margin-bottom:.375rem;">驗證碼</label>
                <div style="display:flex; gap:.625rem; align-items:center;">
                    {{-- 左：驗證碼圖 + 換一張 --}}
                    <div style="display:flex; flex-direction:column; align-items:center; gap:.25rem; flex-shrink:0;">
                        <div id="captcha-img" style="border:1px solid #e5e7eb; border-radius:.5rem; overflow:hidden; line-height:0; background:#f9fafb;">
                            {!! $captchaSvg !!}
                        </div>
                        <button type="button" onclick="refreshCaptcha()"
                            style="display:inline-flex; align-items:center; gap:.25rem; background:none; border:none; cursor:pointer; font-size:.75rem; color:#4f46e5; font-weight:500; padding:.125rem .25rem; border-radius:.375rem;"
                            onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='none'">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            換一張
                        </button>
                    </div>
                    {{-- 右：輸入框 --}}
                    <div style="flex:1;">
                        <input type="text" name="captcha" id="captcha"
                            autocomplete="off" maxlength="3" required
                            class="input-field {{ $errors->has('captcha') ? 'error' : '' }}"
                            style="letter-spacing:.25em; font-family:monospace; font-size:1.125rem; text-align:center;"
                            placeholder="輸入驗證碼">
                        @error('captcha')
                        <p style="margin:.375rem 0 0; font-size:.75rem; color:#dc2626;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Remember --}}
            <div style="margin-bottom:1.5rem; display:flex; align-items:center; gap:.5rem;">
                <input type="checkbox" name="remember" id="remember"
                    style="width:1rem; height:1rem; accent-color:#4f46e5; cursor:pointer;">
                <label for="remember" style="font-size:.8125rem; color:#6b7280; cursor:pointer;">記住此裝置</label>
            </div>

            <button type="submit" class="btn-primary" {{ $isLocked ? 'disabled' : '' }}>
                登入後台
            </button>
        </form>
    </div>

    {{-- Security badges --}}
    <div style="display:flex; justify-content:center; gap:1.25rem; margin-top:1.25rem; flex-wrap:wrap;">
        @foreach([['驗證碼保護','M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'], ['暴力破解防護','M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'], ['CSRF 保護','M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z']] as [$label,$path])
        <div style="display:flex; align-items:center; gap:.3rem; font-size:.6875rem; color:#94a3b8;">
            <svg width="11" height="11" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
            {{ $label }}
        </div>
        @endforeach
    </div>

    <p style="text-align:center; margin-top:1rem; font-size:.6875rem; color:#94a3b8;">© {{ date('Y') }} ECount. All rights reserved.</p>
</div>

<script>
function refreshCaptcha() {
    const btn = event.currentTarget;
    btn.style.opacity = '.5';
    fetch('{{ route('superadmin.captcha.refresh') }}')
        .then(r => r.text())
        .then(svg => {
            document.getElementById('captcha-img').innerHTML = svg;
            document.getElementById('captcha').value = '';
            document.getElementById('captcha').focus();
        })
        .finally(() => { btn.style.opacity = '1'; });
}
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.style.color = isHidden ? '#4f46e5' : '#9ca3af';
    const svg = btn.querySelector('svg');
    svg.innerHTML = isHidden
        ? '<path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'
        : '<path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
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
