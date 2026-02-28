<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with(['projects' => fn($q) => $q->whereNotIn('status', ['closed', 'archived'])]);

        // 搜尋
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_no', 'like', "%{$search}%");
            });
        }

        // 狀態篩選
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        // 預設隱藏已離職（resign_date 不為空），可透過 show_resigned=1 顯示
        if (!$request->boolean('show_resigned')) {
            $query->where(function($q) {
                $q->whereNull('resign_date')->orWhere('resign_date', '>', now());
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $showResigned = $request->boolean('show_resigned');
        $resignedCount = User::whereNotNull('resign_date')->where('resign_date', '<=', now())->count();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $users->items(),
                'meta' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ]
            ]);
        }

        return view('tenant.users.index', compact('users', 'showResigned', 'resignedCount'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $supervisors = User::where('is_active', true)->orderBy('name')->get();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        
        return view('tenant.users.create', compact('supervisors', 'roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'is_active' => 'boolean',
            // 員工資訊
            'employee_no' => 'nullable|string|max:50',
            'short_name' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'supervisor_id' => 'nullable|exists:users,id',
            // 個人資料
            'id_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'backup_email' => 'nullable|email',
            // 銀行資訊
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            // 緊急聯絡人
            'emergency_contact_name' => 'nullable|string|max:50',
            'emergency_contact_phone' => 'nullable|string|max:20',
            // 任職資訊
            'hire_date' => 'nullable|date',
            'resign_date' => 'nullable|date',
            'suspend_date' => 'nullable|date',
            'note' => 'nullable|string',
        ], [
            'name.required' => '姓名為必填',
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式不正確',
            'email.unique' => 'Email 已被使用',
            'password.required' => '密碼為必填',
            'password.min' => '密碼至少需要 6 個字元',
            'role.required' => '角色為必填',
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

        $userData = $request->except(['role', 'password', 'bank_accounts', 'bank_default', 'bank_name', 'bank_branch', 'bank_account']);
        $userData['password'] = Hash::make($request->password);
        $userData['is_active'] = $request->boolean('is_active', true);

        // 將空字串的日期欄位轉為 null
        $dateFields = ['birth_date', 'hire_date', 'resign_date', 'suspend_date'];
        foreach ($dateFields as $field) {
            if (isset($userData[$field]) && $userData[$field] === '') {
                $userData[$field] = null;
            }
        }

        $user = User::create($userData);

        // 分配角色
        if ($request->role) {
            $user->assignRole($request->role);
        }

        // 儲存多筆銀行帳戶
        $defaultIndex = $request->input('bank_default', 0);
        foreach ($request->input('bank_accounts', []) as $i => $bank) {
            if (!empty($bank['bank_name']) || !empty($bank['bank_account'])) {
                $user->bankAccounts()->create([
                    'bank_name'    => $bank['bank_name'] ?? null,
                    'bank_branch'  => $bank['bank_branch'] ?? null,
                    'bank_account' => $bank['bank_account'] ?? null,
                    'account_name' => $bank['account_name'] ?? null,
                    'note'         => $bank['note'] ?? null,
                    'is_default'   => ((string)$i === (string)$defaultIndex),
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '使用者建立成功',
                'data' => $user
            ], 201);
        }

        return redirect()->route('tenant.users.edit', $user)
            ->with('success', '使用者建立成功');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $user
            ]);
        }

        $user->load(['projects.company']);

        return view('tenant.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load('bankAccounts');
        $supervisors = User::where('is_active', true)->where('id', '!=', $user->id)->orderBy('name')->get();
        $currentRole = $user->roles->first()?->name ?? '';
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        
        return view('tenant.users.edit', compact('user', 'supervisors', 'currentRole', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
            // 員工資訊
            'employee_no' => 'nullable|string|max:50',
            'short_name' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'supervisor_id' => 'nullable|exists:users,id',
            // 個人資料
            'id_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'backup_email' => 'nullable|email',
            // 銀行資訊
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            // 緊急聯絡人
            'emergency_contact_name' => 'nullable|string|max:50',
            'emergency_contact_phone' => 'nullable|string|max:20',
            // 任職資訊
            'hire_date' => 'nullable|date',
            'resign_date' => 'nullable|date',
            'suspend_date' => 'nullable|date',
            'note' => 'nullable|string',
        ], [
            'name.required' => '姓名為必填',
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式不正確',
            'email.unique' => 'Email 已被使用',
            'password.min' => '密碼至少需要 6 個字元',
            'password.confirmed' => '密碼確認不一致',
            'role.required' => '角色層級為必填',
            'role.in' => '角色層級不正確',
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

        $data = $validator->validated();
        unset($data['password']); // 先移除密碼欄位
        unset($data['role']); // 角色需要另外處理

        // 將空字串的日期欄位轉為 null
        $dateFields = ['birth_date', 'hire_date', 'resign_date', 'suspend_date'];
        foreach ($dateFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // 只在有提供新密碼時才更新
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // 更新角色
        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        // 同步多筆銀行帳戶
        if ($request->has('bank_accounts')) {
            $user->bankAccounts()->delete();
            $defaultIndex = $request->input('bank_default', 0);
            foreach ($request->input('bank_accounts', []) as $i => $bank) {
                if (!empty($bank['bank_name']) || !empty($bank['bank_account'])) {
                    $user->bankAccounts()->create([
                        'bank_name'    => $bank['bank_name'] ?? null,
                        'bank_branch'  => $bank['bank_branch'] ?? null,
                        'bank_account' => $bank['bank_account'] ?? null,
                        'account_name' => $bank['account_name'] ?? null,
                        'note'         => $bank['note'] ?? null,
                        'is_default'   => ((string)$i === (string)$defaultIndex),
                    ]);
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '使用者更新成功',
                'data' => $user
            ]);
        }

        return redirect()->route('tenant.users.index')
            ->with('success', '使用者更新成功');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // 防止刪除自己
        if ($user->id === auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => '無法刪除自己的帳號'
                ], 422);
            }
            return back()->with('error', '無法刪除自己的帳號');
        }

        // 防止刪除最後一個管理員
        if ($user->hasRole('admin')) {
            $adminCount = User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->where('is_active', true)->count();
            
            if ($adminCount <= 1) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'message' => '無法刪除最後一個系統管理員'
                    ], 422);
                }
                return back()->with('error', '無法刪除最後一個系統管理員');
            }
        }

        $user->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => '使用者刪除成功'
            ]);
        }

        return redirect()->route('tenant.users.index')
            ->with('success', '使用者刪除成功');
    }

    /**
     * 新增銀行帳戶
     */
    public function storeBankAccount(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'required|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'note' => 'nullable|string',
        ], [
            'bank_name.required' => '銀行名稱為必填',
            'bank_account.required' => '帳號為必填',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '驗證失敗',
                'errors' => $validator->errors()
            ], 422);
        }

        $bankAccount = $user->bankAccounts()->create($request->all());

        return response()->json([
            'message' => '銀行帳戶新增成功',
            'data' => $bankAccount
        ], 201);
    }

    /**
     * 更新銀行帳戶
     */
    public function updateBankAccount(Request $request, User $user, UserBankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== $user->id) {
            return response()->json([
                'message' => '無權限操作此銀行帳戶'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_account' => 'required|string|max:50',
            'account_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '驗證失敗',
                'errors' => $validator->errors()
            ], 422);
        }

        $bankAccount->update($request->all());

        return response()->json([
            'message' => '銀行帳戶更新成功',
            'data' => $bankAccount
        ]);
    }

    /**
     * 刪除銀行帳戶
     */
    public function destroyBankAccount(User $user, UserBankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== $user->id) {
            return response()->json([
                'message' => '無權限操作此銀行帳戶'
            ], 403);
        }

        $bankAccount->delete();

        return response()->json([
            'message' => '銀行帳戶刪除成功'
        ]);
    }

    /**
     * 設為預設銀行帳戶
     */
    public function setDefaultBankAccount(User $user, UserBankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== $user->id) {
            return response()->json([
                'message' => '無權限操作此銀行帳戶'
            ], 403);
        }

        // 取消其他帳戶的預設狀態
        $user->bankAccounts()->update(['is_default' => false]);
        
        // 設為預設
        $bankAccount->update(['is_default' => true]);

        return response()->json([
            'message' => '已設為預設銀行帳戶',
            'data' => $bankAccount
        ]);
    }

    /**
     * 匯出使用者清單
     */
    public function export(Request $request)
    {
        $query = User::with(['projects']);

        // 套用與 index 相同的篩選條件
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $filename = '使用者清單_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // 標題列
            fputcsv($file, ['員工編號', '姓名', 'Email', '角色', '手機', '分機', '狀態', '建立日期']);

            // 資料列
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->employee_no,
                    $user->name,
                    $user->email,
                    $user->getRoleNames()->first(),
                    $user->phone,
                    $user->extension,
                    $user->is_active ? '啟用' : '停用',
                    $user->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
