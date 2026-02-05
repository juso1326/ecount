@extends('layouts.tenant')

@section('title', '編輯應收帳款')

@section('page-title', '編輯應收帳款')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.receivables.index') }}" class="hover:text-primary">財務管理</a> &gt; 
        <a href="{{ route('tenant.receivables.index') }}" class="hover:text-primary">應收帳款管理</a> &gt; 
        編輯
    </p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">編輯應收帳款 #{{ $receivable->receipt_no }}</h1>
</div>

@include('tenant.receivables.form')
@endsection
