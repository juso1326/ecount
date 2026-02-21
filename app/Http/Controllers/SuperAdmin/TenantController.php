<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of tenants.
     */
    public function index(Request $request)
    {
        $query = Tenant::query();

        // 搜尋
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 方案篩選
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $tenants->items(),
                'meta' => [
                    'total' => $tenants->total(),
                    'per_page' => $tenants->perPage(),
                    'current_page' => $tenants->currentPage(),
                    'last_page' => $tenants->lastPage(),
                ]
            ]);
        }

        return view('superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('superadmin.tenants.create');
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:50|regex:/^[a-z0-9]+$/|unique:tenants,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'plan' => 'required|in:basic,professional,enterprise',
            'domain' => 'nullable|string|max:255',
        ], [
            'id.required' => '租戶 ID 為必填',
            'id.regex' => '租戶 ID 只能包含小寫字母和數字',
            'id.unique' => '租戶 ID 已存在',
            'name.required' => '租戶名稱為必填',
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式不正確',
            'email.unique' => 'Email 已被使用',
            'plan.required' => '方案為必填',
            'plan.in' => '方案選擇不正確',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '驗證失敗',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // 使用同步方式建立（開發環境）
            $tenant = $this->tenantService->createTenantSync(
                $request->id,
                $request->name,
                $request->email,
                $request->plan,
                $request->domain
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '租戶建立成功',
                    'data' => $tenant
                ], 201);
            }

            return redirect()->route('superadmin.tenants.index')
                ->with('success', "租戶 {$tenant->name} 建立成功！");
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '租戶建立失敗',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()
                ->withErrors(['error' => '租戶建立失敗：' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['domains', 'subscriptions' => function($query) {
            $query->latest('created_at');
        }]);
        
        // 取得租戶資料庫統計
        $stats = $tenant->run(function () {
            return [
                'companies' => \App\Models\Company::count(),
                'projects' => \App\Models\Project::count(),
                'users' => \App\Models\User::count(),
            ];
        });

        if (request()->wantsJson()) {
            return response()->json([
                'data' => array_merge($tenant->toArray(), [
                    'stats' => $stats,
                    'subscriptions' => $tenant->subscriptions
                ])
            ]);
        }

        return view('superadmin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * Show the form for editing the tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'plan' => 'required|in:basic,professional,enterprise',
            'status' => 'required|in:active,suspended,inactive',
        ], [
            'name.required' => '租戶名稱為必填',
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式不正確',
            'email.unique' => 'Email 已被使用',
            'plan.required' => '方案為必填',
            'status.required' => '狀態為必填',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '驗證失敗',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $tenant->update($request->only(['name', 'email', 'plan', 'status']));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '租戶更新成功',
                'data' => $tenant
            ]);
        }

        return redirect()->route('superadmin.tenants.index')
            ->with('success', '租戶更新成功');
    }

    /**
     * Suspend the tenant.
     */
    public function suspend(Tenant $tenant)
    {
        $this->tenantService->suspendTenant($tenant->id);

        if (request()->wantsJson()) {
            return response()->json(['message' => '租戶已暫停']);
        }

        return redirect()->route('superadmin.tenants.index')
            ->with('success', "租戶 {$tenant->name} 已暫停");
    }

    /**
     * Activate the tenant.
     */
    public function activate(Tenant $tenant)
    {
        $this->tenantService->activateTenant($tenant->id);

        if (request()->wantsJson()) {
            return response()->json(['message' => '租戶已啟用']);
        }

        return redirect()->route('superadmin.tenants.index')
            ->with('success', "租戶 {$tenant->name} 已啟用");
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Tenant $tenant)
    {
        try {
            $name = $tenant->name;
            $this->tenantService->deleteTenant($tenant->id);

            if (request()->wantsJson()) {
                return response()->json(['message' => '租戶刪除成功']);
            }

            return redirect()->route('superadmin.tenants.index')
                ->with('success', "租戶 {$name} 已刪除");
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => '租戶刪除失敗',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', '租戶刪除失敗：' . $e->getMessage());
        }
    }
}
