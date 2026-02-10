@extends('layouts.tenant')

@section('title', '新增客戶/廠商')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">新增客戶/廠商</h1>
</div>

<!-- <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">    -->
    <form method="POST" action="{{ route('tenant.companies.store') }}">
        @csrf
        
        @include('tenant.companies._form')
    </form>
<!-- </div> -->
@endsection
