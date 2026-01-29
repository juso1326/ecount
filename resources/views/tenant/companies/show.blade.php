@extends('layouts.tenant')

@section('title', '公司詳情')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-900">公司詳情</h1>
    <div class="space-x-2">
        <a href="{{ route('tenant.companies.edit', $company) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            編輯
        </a>
        <a href="{{ route('tenant.companies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            返回列表
        </a>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500">公司代碼</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->code }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">公司名稱</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">統一編號</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->tax_id ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">聯絡電話</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->phone ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">傳真</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->fax ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">Email</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->email ?? '-' }}</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500">地址</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->address ?? '-' }}</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500">描述</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->description ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">狀態</label>
            <p class="mt-1">
                @if($company->is_active)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        啟用
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        停用
                    </span>
                @endif
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500">建立時間</label>
            <p class="mt-1 text-lg text-gray-900">{{ $company->created_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</div>

<!-- 相關專案 -->
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">相關專案</h2>
    
    @if($company->projects->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">專案代碼</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">專案名稱</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">狀態</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">預算</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($company->projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $project->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">{{ $project->status }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($project->budget) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('tenant.projects.show', $project) }}" class="text-blue-600 hover:text-blue-900">查看</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">尚無相關專案</p>
    @endif
</div>
@endsection
