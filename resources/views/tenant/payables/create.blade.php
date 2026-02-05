@extends('layouts.tenant')

@section('title', '新增應付帳款')

@section('page-title', '新增應付帳款')

@section('content')
<!-- 麵包屑 -->
<div class="mb-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('tenant.payables.index') }}" class="hover:text-primary">財務管理</a> &gt; 
        <a href="{{ route('tenant.payables.index') }}" class="hover:text-primary">應付帳款管理</a> &gt; 
        新增
    </p>
</div>

<!-- 頁面標題 -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">新增應付帳款</h1>
</div>

@include('tenant.payables.form')
@endsection
