<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
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
        $query = User::query();

        // 搜尋
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 狀態篩選
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

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

        return view('tenant.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $supervisors = User::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.users.create', compact('departments', 'supervisors'));
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
            'department_id' => 'nullable|exists:departments,id',
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

        $userData = $request->except(['role', 'password']);
        $userData['password'] = Hash::make($request->password);
        $userData['is_active'] = $request->boolean('is_active', true);

        $user = User::create($userData);

        // 分配角色
        if ($request->role) {
            $user->assignRole($request->role);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '使用者建立成功',
                'data' => $user
            ], 201);
        }

        return redirect()->route('tenant.users.index')
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

        return view('tenant.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('tenant.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ], [
            'name.required' => '姓名為必填',
            'email.required' => 'Email 為必填',
            'email.email' => 'Email 格式不正確',
            'email.unique' => 'Email 已被使用',
            'password.min' => '密碼至少需要 8 個字元',
            'password.confirmed' => '密碼確認不一致',
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

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->boolean('is_active', true),
        ];

        // 只在有提供新密碼時才更新
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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

        $user->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => '使用者刪除成功'
            ]);
        }

        return redirect()->route('tenant.users.index')
            ->with('success', '使用者刪除成功');
    }
}
