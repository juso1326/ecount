@extends('layouts.tenant')
@section('title', '系統設定')
@section('page-title', '系統設定')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">系統設定</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">設定系統基本偏好與顯示格式</p>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg text-sm">
    {{ session('success') }}
</div>
@endif

<!-- Tab Nav -->
<div class="border-b border-gray-200 dark:border-gray-700 mb-6">
    <nav class="flex space-x-1 -mb-px" id="settingsTabs">
        @foreach([
            ['key'=>'setup',    'label'=>'設置'],
            ['key'=>'account',  'label'=>'帳戶'],
            ['key'=>'general',  'label'=>'一般'],
            ['key'=>'notify',   'label'=>'通知'],
            ['key'=>'payment',  'label'=>'付款'],
        ] as $tab)
        <button onclick="switchTab('{{ $tab['key'] }}')" id="tab-{{ $tab['key'] }}"
            class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
            {{ $tab['label'] }}
        </button>
        @endforeach
    </nav>
</div>

<!-- ===== 設置 ===== -->
<div id="panel-setup" class="tab-panel">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 max-w-lg">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">訂閱方案</h2>
        <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">方案</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-3 py-0.5 rounded-full">{{ $subPlan }}</span>
        </div>
        @if($subExpires)
        <div class="flex items-center justify-between py-3">
            <span class="text-sm text-gray-500 dark:text-gray-400">到期日</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subExpires }}</span>
        </div>
        @else
        <div class="flex items-center justify-between py-3">
            <span class="text-sm text-gray-500 dark:text-gray-400">到期日</span>
            <span class="text-sm text-gray-400">—</span>
        </div>
        @endif
    </div>
</div>

<!-- ===== 帳戶 ===== -->
<div id="panel-account" class="tab-panel hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 max-w-lg">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Account</h2>
        <form action="{{ route('tenant.settings.system.account.update') }}" method="POST" class="space-y-4">
            @csrf
            <!-- Avatar + Name -->
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Password</label>
                <input type="password" name="password" placeholder="留空則不變更"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="確認新密碼"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary-dark text-white text-sm rounded-lg font-medium">SAVE</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== 一般 ===== -->
<div id="panel-general" class="tab-panel hidden">
    <form action="{{ route('tenant.settings.system.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-6 max-w-2xl">

            <!-- Format 格式設定 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Format <span class="font-normal text-gray-500">格式設定</span></h2>
                <div class="mt-4 space-y-5">

                    <!-- Date Format -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Date Format</p>
                            <p class="text-xs text-gray-400">日期格式</p>
                        </div>
                        <div class="flex-1">
                            <select name="date_format"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                                @foreach(['Y-m-d'=>'YYYY-MM-DD','Y/m/d'=>'YYYY/MM/DD','Y.m.d'=>'YYYY.MM.DD','m/d/Y'=>'MM/DD/YYYY','d/m/Y'=>'DD/MM/YYYY'] as $fmt=>$label)
                                <option value="{{ $fmt }}" {{ $dateFormat==$fmt?'selected':'' }}>{{ $label }} ({{ date($fmt) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Main Currency -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Main Currency</p>
                            <p class="text-xs text-gray-400">計價幣值</p>
                        </div>
                        <div class="flex-1">
                            <select name="default_currency"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                                @foreach($currencies as $code=>$name)
                                <option value="{{ $code }}" {{ $mainCurrency==$code?'selected':'' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tax Rate -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <div class="flex items-center gap-1">
                                <span id="taxRateLabelDisplay" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $taxRateLabel }}</span>
                                <span class="text-xs text-gray-400">（%）</span>
                            </div>
                            <input type="hidden" name="tax_rate_label" id="taxRateLabelInput" value="{{ $taxRateLabel }}">
                            <button type="button" onclick="startRename('taxRate')" class="text-xs text-primary hover:underline mt-0.5">Rename</button>
                        </div>
                        <div class="flex-1">
                            <input type="number" name="tax_rate" value="{{ $taxRate }}" min="0" max="100" step="0.01"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <!-- Tax Number -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <span id="taxNumberLabelDisplay" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $taxNumberLabel }}</span>
                            <input type="hidden" name="tax_number_label" id="taxNumberLabelInput" value="{{ $taxNumberLabel }}">
                            <button type="button" onclick="startRename('taxNumber')" class="text-xs text-primary hover:underline mt-0.5 block">Rename</button>
                        </div>
                        <div class="flex-1">
                            <input type="text" name="tax_number" value="{{ $taxNumber }}"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <!-- Decimal places -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Decimal places</p>
                            <p class="text-xs text-gray-400">小數點位數</p>
                        </div>
                        <div class="flex-1">
                            <input type="number" name="decimal_places" value="{{ $decimalPlaces }}" min="0" max="6"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        </div>
                    </div>

                    <!-- Use 1000 separator -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Use 1000 separator (,)</p>
                            <p class="text-xs text-gray-400">使用千位(,)標示</p>
                        </div>
                        <div class="flex-1">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="use_thousand_separator" value="0">
                                <input type="checkbox" name="use_thousand_separator" value="1" class="sr-only peer"
                                    {{ $useThousandSeparator ? 'checked' : '' }}>
                                <div class="w-10 h-5 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400" id="separatorLabel">{{ $useThousandSeparator ? '1,234,567' : '1234567' }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quotation Number type -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="w-40 flex-shrink-0 pt-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Quotation Number type</p>
                            <p class="text-xs text-gray-400">單號規則</p>
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="text" name="quotation_number_pattern" id="qtnPattern"
                                value="{{ $quotationPattern }}"
                                placeholder="AAAYYYY0000"
                                oninput="updateQtnExample()"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm font-mono">
                            <p class="text-xs text-gray-400">
                                規則：<code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">AAA</code>=前綴字母
                                　<code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">YYYY</code>=年份
                                　<code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">0000</code>=流水號
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                example：<span id="qtnExample" class="font-medium text-gray-700 dark:text-gray-200">{{ $quotationExample }}</span>
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Theme Customization -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Theme Customization <span class="font-normal text-gray-500">樣式設計</span></h2>
                <div class="mt-4 space-y-5">

                    <!-- 顯示名稱 -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">顯示名稱</p>
                            <p class="text-xs text-gray-400">Display Name</p>
                        </div>
                        <div class="flex-1">
                            <input type="text" name="display_name" value="{{ $displayName }}" maxlength="30"
                                placeholder="系統顯示名稱"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>

                    <!-- Display Language -->
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Display Language</p>
                            <p class="text-xs text-gray-400">顯示語言</p>
                        </div>
                        <div class="flex-1">
                            <select name="display_language"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                                <option value="zh_TW" {{ $displayLang=='zh_TW'?'selected':'' }}>繁體中文</option>
                                <option value="zh_CN" {{ $displayLang=='zh_CN'?'selected':'' }}>简体中文</option>
                                <option value="en"    {{ $displayLang=='en'?'selected':'' }}>English</option>
                                <option value="ja"    {{ $displayLang=='ja'?'selected':'' }}>日本語</option>
                            </select>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 -mt-2 ml-44">儲存後，重新登入介面換語言</p>

                    <!-- Company Logo -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="w-40 flex-shrink-0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Company logo</p>
                        </div>
                        <div class="flex-1">
                            @if($logoPath)
                            <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo" class="h-12 mb-2 rounded">
                            @endif
                            <label class="flex items-center gap-2 cursor-pointer border border-dashed border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span id="logoFileName">upload image</span>
                                <input type="file" name="logo" accept="image/*" class="hidden" onchange="document.getElementById('logoFileName').textContent=this.files[0]?.name||'upload image'">
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Hidden system fields -->
            <input type="hidden" name="time_format" value="{{ $timeFormat }}">
            <input type="hidden" name="timezone" value="{{ $timezone }}">

            <div class="flex justify-end gap-3">
                <button type="button" onclick="location.reload()" class="px-5 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">CANCEL</button>
                <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary-dark text-white text-sm rounded-lg font-medium">SAVE</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== 通知 ===== -->
<div id="panel-notify" class="tab-panel hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 max-w-lg">
        <p class="text-sm text-gray-400">通知設定（開發中）</p>
    </div>
</div>

<!-- ===== 付款 ===== -->
<div id="panel-payment" class="tab-panel hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 max-w-lg">
        <p class="text-sm text-gray-400">付款設定（開發中）</p>
    </div>
</div>

<!-- Rename Modal -->
<div id="renameModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-5 w-72 shadow-lg">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3" id="renameTitle">Rename</h3>
        <input type="text" id="renameInput" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm mb-4">
        <div class="flex justify-end gap-2">
            <button onclick="closeRename()" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300">取消</button>
            <button onclick="applyRename()" class="px-3 py-1.5 text-sm bg-primary text-white rounded-lg">確認</button>
        </div>
    </div>
</div>

<script>
let activeRenameTarget = null;

const TAB_CLASSES = {
    active: 'border-primary text-primary dark:text-primary-light',
    inactive: 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300'
};

function switchTab(key) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.className = b.className.replace(TAB_CLASSES.active, '').replace(TAB_CLASSES.inactive, '').trim();
        b.classList.add(...TAB_CLASSES.inactive.split(' '));
    });
    document.getElementById('panel-' + key).classList.remove('hidden');
    const btn = document.getElementById('tab-' + key);
    btn.className = btn.className.replace(TAB_CLASSES.inactive, '').trim();
    btn.classList.add(...TAB_CLASSES.active.split(' '));

    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', key);
    history.replaceState(null, '', url);
}

function startRename(target) {
    activeRenameTarget = target;
    const display = document.getElementById(target + 'LabelDisplay');
    document.getElementById('renameTitle').textContent = 'Rename ' + (target === 'taxRate' ? 'Tax Rate' : 'Tax Number');
    document.getElementById('renameInput').value = display.textContent.trim();
    document.getElementById('renameModal').classList.remove('hidden');
}

function closeRename() {
    document.getElementById('renameModal').classList.add('hidden');
    activeRenameTarget = null;
}

function applyRename() {
    const val = document.getElementById('renameInput').value.trim();
    if (!val || !activeRenameTarget) return;
    document.getElementById(activeRenameTarget + 'LabelDisplay').textContent = val;
    document.getElementById(activeRenameTarget + 'LabelInput').value = val;
    closeRename();
}

// Init tab from URL or default
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    switchTab(params.get('tab') || 'setup');
});
</script>
@endsection
