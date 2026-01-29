<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '超級管理員系統')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-indigo-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-white">🛡️ ECount 超級管理員</span>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('superadmin.dashboard') }}" class="border-transparent text-white hover:border-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            儀表板
                        </a>
                        <a href="{{ route('superadmin.tenants.index') }}" class="border-transparent text-white hover:border-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            租戶管理
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-white mr-4">{{ auth('superadmin')->user()->name }}</span>
                    <form method="POST" action="{{ route('superadmin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
                            登出
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
