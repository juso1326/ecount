<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = SuperAdmin::withTrashed()->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'active')   $query->whereNull('deleted_at')->where('is_active', true);
            if ($status === 'inactive') $query->whereNull('deleted_at')->where('is_active', false);
            if ($status === 'suspended') $query->onlyTrashed();
        }

        $admins = $query->paginate(20)->withQueryString();
        $currentAdmin = auth('superadmin')->user();

        return view('superadmin.admins.index', compact('admins', 'currentAdmin'));
    }

    public function create()
    {
        return view('superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:central.super_admins,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        SuperAdmin::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.admins.index')
            ->with('success', '帳號已建立');
    }

    public function edit(SuperAdmin $admin)
    {
        return view('superadmin.admins.edit', compact('admin'));
    }

    public function update(Request $request, SuperAdmin $admin)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:central.super_admins,email,' . $admin->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('superadmin.admins.index')
            ->with('success', '帳號已更新');
    }

    public function suspend(SuperAdmin $admin)
    {
        $current = auth('superadmin')->user();
        if ($admin->id === $current->id) {
            return back()->with('error', '無法停權自己的帳號');
        }

        $admin->delete(); // soft delete

        return back()->with('success', "帳號「{$admin->name}」已停權");
    }

    public function restore(int $id)
    {
        $admin = SuperAdmin::onlyTrashed()->findOrFail($id);
        $admin->restore();

        return back()->with('success', "帳號「{$admin->name}」已恢復");
    }
}
