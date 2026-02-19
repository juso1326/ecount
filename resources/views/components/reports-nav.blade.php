@props(['current'])

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 mb-4 overflow-x-auto">
    <nav class="flex min-w-max">
        <a href="{{ route('tenant.reports.financial-overview') }}" 
           class="px-4 py-1.5 text-sm font-medium border-b-2 whitespace-nowrap
                  {{ $current === 'financial-overview' 
                     ? 'text-primary border-primary dark:text-primary-light' 
                     : 'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
            財務綜合分析
        </a>
        <a href="{{ route('tenant.reports.ar-ap-analysis') }}" 
           class="px-4 py-1.5 text-sm font-medium border-b-2 whitespace-nowrap
                  {{ $current === 'ar-ap-analysis' 
                     ? 'text-primary border-primary dark:text-primary-light' 
                     : 'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
            應收應付分析
        </a>
        <a href="{{ route('tenant.reports.project-profit-loss') }}" 
           class="px-4 py-1.5 text-sm font-medium border-b-2 whitespace-nowrap
                  {{ $current === 'project-profit-loss' 
                     ? 'text-primary border-primary dark:text-primary-light' 
                     : 'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
            專案損益分析
        </a>
        <a href="{{ route('tenant.reports.payroll-labor') }}" 
           class="px-4 py-1.5 text-sm font-medium border-b-2 whitespace-nowrap
                  {{ $current === 'payroll-labor' 
                     ? 'text-primary border-primary dark:text-primary-light' 
                     : 'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
            薪資人力分析
        </a>
    </nav>
</div>
