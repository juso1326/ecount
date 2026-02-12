@extends('layouts.tenant')

@section('title', '編輯應收帳款')

@section('page-title', '編輯應收帳款')

@section('content')
<!-- 頁面標題 -->
<div class="mb-3">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">編輯應收帳款 #{{ $receivable->receipt_no }}</h1>
</div>

@include('tenant.receivables.form')
@endsection
