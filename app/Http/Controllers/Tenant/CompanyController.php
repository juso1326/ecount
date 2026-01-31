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
                  ->orWhere('short_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%");
            });
        }

        // 類型篩選 - 客戶
        if ($request->filled('is_client')) {
            $query->where('is_client', true);
        }

        // 類型篩選 - 外製
        if ($request->filled('is_outsource')) {
            $query->where('is_outsource', true);
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
        // 產生下一個公司代碼
        $nextCode = $this->generateNextCode();
        
        return view('tenant.companies.create', compact('nextCode'));
    }

    /**
     * Generate next company code
     */
    private function generateNextCode(): string
    {
        $prefix = \App\Models\TenantSetting::get('company_code_prefix', 'C');
        $length = \App\Models\TenantSetting::get('company_code_length', 4);
        
        // 找出最後一個公司代碼
        $lastCompany = Company::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();
        
        if ($lastCompany) {
            // 取得數字部分
            $lastNumber = (int) str_replace($prefix, '', $lastCompany->code);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // 格式化代碼
        return $prefix . str_pad((string)$nextNumber, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:companies,code',
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:100',
            'type' => 'required|in:company,individual',
            'tax_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'is_client' => 'boolean',
            'is_outsource' => 'boolean',
        ], [
            'code.required' => '公司代碼為必填',
            'code.unique' => '公司代碼已存在',
            'name.required' => '名稱為必填',
            'short_name.required' => '簡稱為必填',
            'type.required' => '類型為必填',
            'email.email' => 'Email 格式不正確',
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

        $companyData = $request->only([
            'name', 'short_name', 'type', 'tax_id', 
            'phone', 'fax', 'email', 'address'
        ]);
        
        $companyData['is_client'] = $request->boolean('is_client');
        $companyData['is_outsource'] = $request->boolean('is_outsource');
        $companyData['is_active'] = true;

        $company = Company::create($companyData);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '公司建立成功',
                'data' => $company
            ], 201);
        }

        return redirect()->route('tenant.companies.index')
            ->with('success', '客戶/廠商建立成功');
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
            'short_name' => 'required|string|max:100',
            'type' => 'required|in:company,individual',
            'tax_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'is_client' => 'boolean',
            'is_outsource' => 'boolean',
        ], [
            'code.required' => '公司代碼為必填',
            'code.unique' => '公司代碼已存在',
            'name.required' => '名稱為必填',
            'short_name.required' => '簡稱為必填',
            'type.required' => '類型為必填',
            'email.email' => 'Email 格式不正確',
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

        $companyData = $request->only([
            'name', 'short_name', 'type', 'tax_id', 
            'phone', 'fax', 'email', 'address'
        ]);
        
        $companyData['is_client'] = $request->boolean('is_client');
        $companyData['is_outsource'] = $request->boolean('is_outsource');

        $company->update($companyData);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '公司更新成功',
                'data' => $company
            ]);
        }

        return redirect()->route('tenant.companies.index')
            ->with('success', '客戶/廠商更新成功');
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
