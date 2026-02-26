@extends('layouts.superadmin')

@section('title', '租戶詳情')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">租戶詳情</h1>
    <div class="space-x-2">
        <form method="POST" action="{{ route('superadmin.tenants.reset-password', $tenant) }}" class="inline"
              onsubmit="return confirm('確定要重設管理員密碼嗎？')">
            @csrf
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                重設密碼
            </button>
        </form>
        <form method="POST" action="{{ route('superadmin.tenants.clear-login-lock', $tenant) }}" class="inline"
              onsubmit="return confirm('確定要解除登入鎖定嗎？')">
            @csrf
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                解除登入鎖定
            </button>
        </form>
        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            編輯
        </a>
        <a href="{{ route('superadmin.tenants.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            返回列表
        </a>
    </div>
</div>

@if($dbBroken)
<div class="bg-red-50 border border-red-400 rounded-lg p-4 mb-6 flex items-start justify-between">
    <div>
        <p class="text-red-800 font-semibold">⚠️ 資料庫不完整</p>
        <p class="text-red-700 text-sm mt-1">租戶資料庫存在但缺少資料表，可能是建立時 Migration 未完成。請點擊「重建資料庫」修復。</p>
        <p class="text-red-600 text-xs mt-1">⚠️ 重建會清除該租戶所有資料並重置管理員密碼。</p>
    </div>
    <form method="POST" action="{{ route('superadmin.tenants.rebuild', $tenant) }}"
          onsubmit="return confirm('確定要重建資料庫嗎？此操作將清除該租戶所有資料！')">
        @csrf
        <button type="submit" class="ml-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
            重建資料庫
        </button>
    </form>
</div>
@endif

@if(session('init_password'))
<div class="bg-green-50 border border-green-400 rounded-lg p-4 mb-6">
    <p class="text-green-800 font-semibold text-lg">✅ 租戶建立成功</p>
    <p class="text-green-700 mt-2">管理員帳號資訊（請立即記錄，離開後無法再查看）：</p>
    <div class="mt-3 bg-white rounded border border-green-300 p-3 font-mono text-sm space-y-1">
        <div><span class="text-gray-500">Email：</span><span class="font-semibold">{{ $tenant->email }}</span></div>
        <div class="flex items-center gap-2">
            <span class="text-gray-500">密碼：</span>
            <span class="font-semibold text-green-700" id="init-pwd">{{ session('init_password') }}</span>
            <button onclick="navigator.clipboard.writeText('{{ session('init_password') }}'); this.textContent='已複製✓'; setTimeout(()=>this.textContent='複製',1500)"
                    class="text-xs border border-green-400 text-green-700 rounded px-2 py-0.5 hover:bg-green-100">複製</button>
        </div>
    </div>
</div>
@endif

<!-- 基本資訊 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">基本資訊</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500">租戶 ID</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->id }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">租戶名稱</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">Email</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->email }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">方案</label>
            <p class="mt-1">
                <span class="px-2 py-1 text-sm rounded-full
                    @if($tenant->plan === 'basic') bg-blue-100 text-blue-800
                    @elseif($tenant->plan === 'professional') bg-indigo-100 text-indigo-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    {{ ucfirst($tenant->plan) }}
                </span>
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">狀態</label>
            <p class="mt-1">
                @if($tenant->status === 'active')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        啟用中
                    </span>
                @elseif($tenant->status === 'suspended')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        已暫停
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        未啟用
                    </span>
                @endif
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">資料庫名稱</label>
            <p class="mt-1 text-lg text-gray-900 font-mono">{{ $tenant->getDatabaseName() }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">建立時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->created_at->format('Y-m-d H:i:s') }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">更新時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $tenant->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</div>

<!-- 域名資訊 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">域名資訊</h2>
    @php $expectedDomain = $tenant->id . '.' . config('app.domain', 'localhost'); @endphp
    <div class="space-y-3">
        @forelse($tenant->domains as $domain)
            @php
                $raw           = $domain->domain;
                $fullUrl       = 'http://' . $raw;
                $domainMismatch = $raw !== $expectedDomain;
            @endphp
            <div class="bg-gray-50 rounded-lg px-4 py-3 border {{ $domainMismatch ? 'border-yellow-400' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">目前域名</p>
                        <span class="font-mono text-gray-900 text-sm">{{ $raw }}</span>
                        @if($domainMismatch)
                            <p class="text-xs text-yellow-600 mt-1">⚠️ 預期應為：<span class="font-mono">{{ $expectedDomain }}</span></p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($domainMismatch)
                            <form method="POST" action="{{ route('superadmin.tenants.fix-domain', $tenant) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white rounded px-3 py-1">
                                    套用預設
                                </button>
                            </form>
                        @endif
                        <button onclick="navigator.clipboard.writeText('{{ $fullUrl }}'); this.textContent='已複製✓'; setTimeout(()=>this.textContent='複製',1500)"
                                class="text-xs text-gray-500 hover:text-gray-700 border border-gray-300 rounded px-2 py-1">
                            複製
                        </button>
                        <button onclick="document.getElementById('edit-domain-form').classList.toggle('hidden')"
                                class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded px-2 py-1">
                            編輯
                        </button>
                        <a href="{{ $fullUrl }}" target="_blank"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded px-3 py-1">
                            訪問 →
                        </a>
                    </div>
                </div>
                {{-- 可展開的編輯表單 --}}
                <div id="edit-domain-form" class="hidden mt-3 pt-3 border-t border-gray-200">
                    <form method="POST" action="{{ route('superadmin.tenants.fix-domain', $tenant) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="domain" value="{{ $raw }}"
                               placeholder="例：abc123.ecount.duckdns.org"
                               class="flex-1 text-sm border border-gray-300 rounded px-3 py-1.5 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded px-3 py-1.5">
                            儲存
                        </button>
                        <button type="button" onclick="document.getElementById('edit-domain-form').classList.add('hidden')"
                                class="text-sm text-gray-500 hover:text-gray-700 px-2 py-1.5">
                            取消
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-yellow-50 border border-yellow-400 rounded-lg px-4 py-3">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-yellow-700 text-sm">尚無域名記錄</p>
                </div>
                <form method="POST" action="{{ route('superadmin.tenants.fix-domain', $tenant) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="domain" value="{{ $expectedDomain }}"
                           placeholder="例：abc123.ecount.duckdns.org"
                           class="flex-1 text-sm border border-yellow-300 rounded px-3 py-1.5 font-mono focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded px-3 py-1.5">
                        建立域名
                    </button>
                </form>
            </div>
        @endforelse
    </div>
</div>

<!-- 方案資訊 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">方案資訊</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-500">目前方案</label>
            <p class="mt-1">
                <span class="px-2 py-1 text-sm rounded-full
                    @if($tenant->plan === 'basic') bg-blue-100 text-blue-800
                    @elseif($tenant->plan === 'professional') bg-indigo-100 text-indigo-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    {{ $tenant->plan_name ?? ucfirst($tenant->plan) }}
                </span>
            </p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">開始日期</label>
            <p class="mt-1 text-gray-900">{{ $tenant->plan_started_at?->format('Y-m-d') ?? '未設定' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">到期日期</label>
            <p class="mt-1">
                @if($tenant->plan_ends_at)
                    <span class="{{ $tenant->isPlanExpired() ? 'text-red-600 font-semibold' : ($tenant->isPlanExpiringSoon() ? 'text-orange-600 font-semibold' : 'text-gray-900') }}">
                        {{ $tenant->plan_ends_at->format('Y-m-d') }}
                    </span>
                    @if($tenant->isPlanExpired())
                        <span class="ml-1 px-1.5 py-0.5 text-xs rounded bg-red-100 text-red-700">已到期</span>
                    @elseif($tenant->isPlanExpiringSoon())
                        <span class="ml-1 px-1.5 py-0.5 text-xs rounded bg-orange-100 text-orange-700">{{ $tenant->planDaysRemaining() }}天後到期</span>
                    @endif
                @else
                    <span class="text-gray-500">無限期</span>
                @endif
            </p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500">自動續約</label>
            <p class="mt-1">
                <span class="px-2 py-0.5 text-xs rounded {{ $tenant->auto_renew ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $tenant->auto_renew ? '是' : '否' }}
                </span>
            </p>
        </div>
    </div>
</div>

<!-- 更換/續約方案 -->
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900">更換 / 續約方案</h2>
        <button type="button" onclick="document.getElementById('renew-form').classList.toggle('hidden')"
            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">展開 ▾</button>
    </div>

    <form id="renew-form" class="hidden" method="POST"
          action="{{ route('superadmin.tenants.renew', $tenant) }}">
        @csrf

        {{-- 方案卡片 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
            @foreach($plans as $plan)
            <label class="relative cursor-pointer">
                <input type="radio" name="plan" value="{{ $plan->slug }}"
                    class="sr-only peer" {{ $tenant->plan === $plan->slug ? 'checked' : '' }} required>
                <div class="border-2 rounded-lg p-3 transition-all
                    peer-checked:border-indigo-600 peer-checked:bg-indigo-50
                    hover:border-gray-400
                    {{ $plan->is_featured ? 'border-indigo-200' : 'border-gray-200' }}">
                    @if($plan->is_featured)
                        <span class="absolute -top-2 left-3 bg-indigo-600 text-white text-xs px-2 py-0.5 rounded-full">推薦</span>
                    @endif
                    <div class="font-semibold text-sm text-gray-900">{{ $plan->name }}</div>
                    <div class="text-indigo-600 font-bold">NT${{ number_format($plan->price) }}<span class="text-xs font-normal text-gray-400">/月</span></div>
                    @if($plan->annual_price)
                    <div class="text-xs text-green-600">年繳 NT${{ number_format($plan->annual_price) }} 省{{ $plan->annual_discount_percentage }}%</div>
                    @endif
                    <div class="text-xs text-gray-500 mt-1">👥 {{ $plan->max_users ?: '不限' }} 人</div>
                </div>
            </label>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- 計費週期 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">計費週期 <span class="text-red-500">*</span></label>
                <div class="space-y-1.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="billing_cycle" value="monthly" checked class="text-indigo-600 renew-cycle">
                        <span class="text-sm">月繳</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="billing_cycle" value="annual" class="text-indigo-600 renew-cycle">
                        <span class="text-sm">年繳</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="billing_cycle" value="unlimited" class="text-indigo-600 renew-cycle">
                        <span class="text-sm">無限期</span>
                    </label>
                </div>
            </div>

            {{-- 開始日期 --}}
            <div>
                <label for="renew_started_at" class="block text-sm font-medium text-gray-700 mb-2">開通日期</label>
                <input type="date" name="plan_started_at" id="renew_started_at"
                    value="{{ date('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <p id="renew-expiry-preview" class="mt-1 text-xs text-indigo-600"></p>
            </div>

            {{-- 自動續費 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">自動續費</label>
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="auto_renew" value="1" {{ $tenant->auto_renew ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700">到期自動續約</span>
                </label>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">
                確認更新方案
            </button>
        </div>
    </form>
</div>

<!-- 租用記錄 -->
@if($tenant->subscriptions->count() > 0)
<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">租用記錄</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">方案</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">價格</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">開始時間</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">結束時間</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">狀態</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">自動續約</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tenant->subscriptions as $subscription)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($subscription->plan === 'basic') bg-blue-100 text-blue-800
                            @elseif($subscription->plan === 'professional') bg-indigo-100 text-indigo-800
                            @else bg-purple-100 text-purple-800
                            @endif">
                            {{ $subscription->plan_name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                        ${{ number_format($subscription->price, 2) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscription->started_at->format('Y-m-d') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscription->ends_at->format('Y-m-d') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($subscription->status === 'active') bg-green-100 text-green-800
                            @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $subscription->status_name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscription->auto_renew ? '是' : '否' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- 資料統計 -->
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">資料統計</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="text-center">
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['companies'] }}</p>
            <p class="text-sm text-gray-500 mt-1">公司數量</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['projects'] }}</p>
            <p class="text-sm text-gray-500 mt-1">專案數量</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-purple-600">{{ $stats['users'] }}</p>
            <p class="text-sm text-gray-500 mt-1">使用者數量</p>
        </div>
    </div>
</div>

<!-- 操作按鈕 -->
<div class="mt-6 flex justify-end space-x-3">
    @if($tenant->status === 'active')
        <form action="{{ route('superadmin.tenants.suspend', $tenant) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('確定要暫停此租戶嗎？')">
                暫停租戶
            </button>
        </form>
    @elseif($tenant->status === 'suspended')
        <form action="{{ route('superadmin.tenants.activate', $tenant) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                啟用租戶
            </button>
        </form>
    @endif
    
    <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ 警告：刪除租戶將永久刪除所有資料！\n\n確定要刪除租戶【{{ $tenant->name }}】嗎？');">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
            刪除租戶
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function renewExpiryPreview() {
    const cycle   = document.querySelector('input[name="billing_cycle"]:checked')?.value;
    const startEl = document.getElementById('renew_started_at');
    const preview = document.getElementById('renew-expiry-preview');
    if (!cycle || !startEl || !preview) return;
    const start = new Date(startEl.value);
    if (isNaN(start)) { preview.textContent = ''; return; }
    if (cycle === 'unlimited') { preview.textContent = '無限期'; return; }
    const end = new Date(start);
    if (cycle === 'monthly') end.setMonth(end.getMonth() + 1);
    if (cycle === 'annual')  end.setFullYear(end.getFullYear() + 1);
    preview.textContent = '到期：' + end.toISOString().slice(0, 10);
}
document.querySelectorAll('.renew-cycle').forEach(r => r.addEventListener('change', renewExpiryPreview));
document.getElementById('renew_started_at')?.addEventListener('change', renewExpiryPreview);
renewExpiryPreview();
</script>
@endpush
