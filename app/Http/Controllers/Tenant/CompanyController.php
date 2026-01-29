<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::query();

        // 搜尋功能
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%");
            });
        }

        // 狀態篩選
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $companies = $query->orderBy('created_at', 'desc')->paginate(15);

        // API 回應
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $companies->items(),
                'meta' => [
                    'total' => $companies->total(),
                    'per_page' => $companies->perPage(),
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                ]
            ]);
        }

        return view('tenant.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:companies,code',
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:20',
            'representative' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => '公司代碼為必填',
            'code.unique' => '公司代碼已存在',
            'name.required' => '公司名稱為必填',
            'email.email' => 'Email 格式不正確',
            'website.url' => '網址格式不正確',
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

        $company = Company::create($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '公司建立成功',
                'data' => $company
            ], 201);
        }

        return redirect()->route('tenant.companies.index')
            ->with('success', '公司建立成功');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $company->load(['projects', 'receivables', 'payables'])
            ]);
        }

        return view('tenant.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('tenant.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:companies,code,' . $company->id,
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:20',
            'representative' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => '公司代碼為必填',
            'code.unique' => '公司代碼已存在',
            'name.required' => '公司名稱為必填',
            'email.email' => 'Email 格式不正確',
            'website.url' => '網址格式不正確',
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

        $company->update($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '公司更新成功',
                'data' => $company
            ]);
        }

        return redirect()->route('tenant.companies.index')
            ->with('success', '公司更新成功');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => '公司刪除成功'
            ]);
        }

        return redirect()->route('tenant.companies.index')
            ->with('success', '公司刪除成功');
    }
}
