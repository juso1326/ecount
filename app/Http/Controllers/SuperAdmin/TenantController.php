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
        $query = Tenant::with('domains');

        // 聰明搜尋：id / 名稱 / email / domain
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id',    'like', "%{$search}%")
                  ->orWhere('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('domains', fn($d) => $d->where('domain', 'like', "%{$search}%"));
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

        // 到期狀態篩選
        if ($request->filled('expiry')) {
            $now = now();
            if ($request->expiry === 'expired') {
                $query->whereNotNull('plan_ends_at')->where('plan_ends_at', '<', $now);
            } elseif ($request->expiry === 'expiring') {
                $query->whereNotNull('plan_ends_at')
                      ->where('plan_ends_at', '>=', $now)
                      ->where('plan_ends_at', '<=', $now->copy()->addDays(7));
            }
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

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
        $plans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('superadmin.tenants.create', compact('plans'));
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'              => 'required|string|max:50|regex:/^[a-z0-9]+$/|unique:tenants,id',
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:tenants,email',
            'plan'            => 'required|string|exists:plans,slug',
            'billing_cycle'   => 'required|in:monthly,annual,unlimited',
            'plan_started_at' => 'nullable|date',
            'auto_renew'      => 'nullable|boolean',
            'domain'          => 'nullable|string|max:255',
        ], [
            'id.required'   => '租戶 ID 為必填',
            'id.regex'      => '租戶 ID 只能包含小寫字母和數字',
            'id.unique'     => '租戶 ID 已存在',
            'name.required' => '租戶名稱為必填',
            'email.required'=> 'Email 為必填',
            'email.email'   => 'Email 格式不正確',
            'email.unique'  => 'Email 已被使用',
            'plan.required' => '方案為必填',
            'plan.exists'   => '所選方案不存在',
            'billing_cycle.required' => '請選擇計費週期',
            'billing_cycle.in'       => '計費週期選擇不正確',
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
                $request->domain,
                $request->billing_cycle,
                $request->plan_started_at,
                (bool) $request->input('auto_renew', true),
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => '租戶建立成功',
                    'data' => $tenant
                ], 201);
            }

            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('success', "租戶 {$tenant->name} 建立成功！")
                ->with('init_password', $tenant->_plainPassword);
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

        $dbBroken = false;
        try {
            $stats = $tenant->run(function () {
                return [
                    'companies' => \App\Models\Company::count(),
                    'projects'  => \App\Models\Project::count(),
                    'users'     => \App\Models\User::count(),
                ];
            });
        } catch (\Throwable $e) {
            $dbBroken = true;
            $stats = ['companies' => 0, 'projects' => 0, 'users' => 0];
        }

        $plans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'data' => array_merge($tenant->toArray(), [
                    'stats' => $stats,
                    'subscriptions' => $tenant->subscriptions
                ])
            ]);
        }

        return view('superadmin.tenants.show', compact('tenant', 'stats', 'plans', 'dbBroken'));
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
     * Renew or change the tenant's plan.
     */
    public function renew(Request $request, Tenant $tenant)
    {
        $request->validate([
            'plan'            => 'required|string|exists:plans,slug',
            'billing_cycle'   => 'required|in:monthly,annual,unlimited',
            'plan_started_at' => 'nullable|date',
            'auto_renew'      => 'nullable|boolean',
        ]);

        $startedAt = $request->plan_started_at
            ? \Carbon\Carbon::parse($request->plan_started_at)
            : now();

        $endsAt = match($request->billing_cycle) {
            'monthly'   => $startedAt->copy()->addMonth(),
            'annual'    => $startedAt->copy()->addYear(),
            default     => null, // unlimited
        };

        $tenant->update([
            'plan'            => $request->plan,
            'plan_started_at' => $startedAt,
            'plan_ends_at'    => $endsAt,
            'auto_renew'      => (bool) $request->input('auto_renew', true),
            'status'          => 'active',
        ]);

        // 記錄訂閱歷程
        $tenant->subscriptions()->create([
            'plan'       => $request->plan,
            'started_at' => $startedAt,
            'ends_at'    => $endsAt,
            'status'     => 'active',
            'auto_renew' => (bool) $request->input('auto_renew', true),
            'notes'      => '手動更換方案（' . match($request->billing_cycle) {
                'monthly'   => '月繳',
                'annual'    => '年繳',
                default     => '無限期',
            } . '）',
        ]);

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', '方案已更新：'. $request->plan .' / '. match($request->billing_cycle) {
                'monthly'  => '月繳，到期 '.$endsAt->format('Y-m-d'),
                'annual'   => '年繳，到期 '.$endsAt->format('Y-m-d'),
                default    => '無限期',
            });
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
     * 更新域名為目前設定的完整 subdomain
     */
    public function fixDomain(Tenant $tenant)
    {
        $baseDomain = config('app.domain', 'localhost');
        $fullDomain = $tenant->id . '.' . $baseDomain;

        $tenant->domains()->delete();
        $tenant->domains()->create(['domain' => $fullDomain]);

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', "域名已更新為：{$fullDomain}");
    }

    /**
     * 重建租戶資料庫（重新執行 migration + 建立管理員帳號）
     */
    public function rebuild(Tenant $tenant)
    {
        try {
            $dbName = 'tenant_' . $tenant->id . '_db';

            // Drop 並重建資料庫
            \Illuminate\Support\Facades\DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
            \Illuminate\Support\Facades\DB::statement("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 重新執行 tenant migrations
            \Illuminate\Support\Facades\Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->id],
                '--force'   => true,
            ]);

            // 在租戶 DB 重新建立 admin 帳號 + 角色
            $adminEmail    = $tenant->email;
            $adminPassword = \Illuminate\Support\Str::random(12);

            $tenant->run(function () use ($adminEmail, $adminPassword, $tenant) {
                $userId = \Illuminate\Support\Facades\DB::table('users')->insertGetId([
                    'name'              => 'Admin',
                    'email'             => $adminEmail,
                    'password'          => \Illuminate\Support\Facades\Hash::make($adminPassword),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $job = new \App\Jobs\CreateTenantJob(
                    $tenant->id, $tenant->name, '', $adminEmail, $adminPassword
                );
                $job->runSeedOnly();

                $adminUser = \App\Models\User::find($userId);
                if ($adminUser) {
                    $adminUser->assignRole('admin');
                }
            });

            return redirect()->route('superadmin.tenants.show', $tenant)
                ->with('success', "資料庫已重建，管理員密碼已重設：{$adminPassword}（請立即記錄）");

        } catch (\Throwable $e) {
            return back()->with('error', '重建失敗：' . $e->getMessage());
        }
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
