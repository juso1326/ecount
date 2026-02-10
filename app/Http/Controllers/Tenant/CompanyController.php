<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyAttributeHistory;
use App\Models\CompanyBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // 標籤篩選
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'client':
                    $query->where('is_client', true);
                    break;
                case 'outsource':
                    $query->where('is_outsource', true);
                    break;
                case 'member':
                    $query->where('is_member', true);
                    break;
            }
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
            'short_name' => 'nullable|string|max:100',
            'type' => 'required|in:company,individual',
            'tax_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'is_client' => 'boolean',
            'is_outsource' => 'boolean',
            'is_member' => 'boolean',
            // 使用者帳號欄位
            'create_user_account' => 'boolean',
            'user_email' => 'required_if:create_user_account,1|nullable|email|unique:users,email',
            'user_password' => 'required_if:create_user_account,1|nullable|string|min:6',
            'user_role' => 'required_if:create_user_account,1|nullable|in:employee,accountant,manager',
            'user_is_active' => 'boolean',
        ], [
            'code.required' => '公司代碼為必填',
            'code.unique' => '公司代碼已存在',
            'name.required' => '名稱為必填',
            'type.required' => '類型為必填',
            'email.email' => 'Email 格式不正確',
            'user_email.required_if' => '使用者 Email 為必填',
            'user_email.email' => '使用者 Email 格式不正確',
            'user_email.unique' => '此 Email 已被使用',
            'user_password.required_if' => '使用者密碼為必填',
            'user_password.min' => '密碼至少需要 6 個字元',
            'user_role.required_if' => '角色權限為必填',
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
            'code', 'name', 'short_name', 'type', 'tax_id', 
            'phone', 'fax', 'email', 'address'
        ]);
        
        // 如果沒有簡稱，使用名稱
        if (empty($companyData['short_name'])) {
            $companyData['short_name'] = $companyData['name'];
        }
        
        $companyData['is_client'] = $request->boolean('is_client');
        $companyData['is_outsource'] = $request->boolean('is_outsource');
        $companyData['is_member'] = $request->boolean('is_member');
        $companyData['is_active'] = true;

        DB::beginTransaction();
        try {
            $company = Company::create($companyData);

            // 處理銀行帳號
            if ($request->has('bank_accounts')) {
                $this->saveBankAccounts($company, $request->bank_accounts);
            }

            // 如果勾選建立使用者帳號且是員工
            if ($request->boolean('create_user_account') && $request->boolean('is_member')) {
                $user = \App\Models\User::create([
                    'name' => $company->name,
                    'email' => $request->user_email,
                    'password' => bcrypt($request->user_password),
                    'company_id' => $company->id,
                    'is_active' => $request->boolean('user_is_active', true),
                ]);
                
                // 指派角色
                $user->assignRole($request->user_role);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '建立失敗：' . $e->getMessage()])->withInput();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '公司建立成功',
                'data' => $company
            ], 201);
        }

        $successMessage = '客戶/廠商建立成功';
        if ($request->boolean('create_user_account') && $request->boolean('is_member')) {
            $successMessage .= '，已同時建立使用者帳號';
        }

        return redirect()->route('tenant.companies.index')
            ->with('success', $successMessage);
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
        // 載入屬性變更歷史和銀行帳號
        $attributeHistories = $company->attributeHistories()
            ->with('changedBy')
            ->latest('changed_at')
            ->limit(20)
            ->get();
        
        $company->load('bankAccounts');
        
        return view('tenant.companies.edit', compact('company', 'attributeHistories'));
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
            'is_member' => 'boolean',
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
        
        // 檢查並記錄屬性變更
        $this->recordAttributeChanges($company, [
            'is_client' => $request->boolean('is_client'),
            'is_outsource' => $request->boolean('is_outsource'),
            'is_member' => $request->boolean('is_member'),
        ]);
        
        $companyData['is_client'] = $request->boolean('is_client');
        $companyData['is_outsource'] = $request->boolean('is_outsource');
        $companyData['is_member'] = $request->boolean('is_member');

        DB::beginTransaction();
        try {
            $company->update($companyData);

            // 處理銀行帳號
            if ($request->has('bank_accounts')) {
                $this->saveBankAccounts($company, $request->bank_accounts);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '更新失敗：' . $e->getMessage()])->withInput();
        }

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

    /**
     * 記錄屬性變更歷史
     */
    private function recordAttributeChanges(Company $company, array $newAttributes): void
    {
        foreach ($newAttributes as $attributeName => $newValue) {
            $oldValue = $company->$attributeName;
            
            // 只記錄有變更的屬性
            if ($oldValue !== $newValue) {
                CompanyAttributeHistory::create([
                    'company_id' => $company->id,
                    'attribute_name' => $attributeName,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                ]);
            }
        }
    }

    /**
     * 儲存銀行帳號資訊
     */
    private function saveBankAccounts(Company $company, array $bankAccounts): void
    {
        // 刪除舊的銀行帳號
        $company->bankAccounts()->delete();
        
        // 新增銀行帳號
        foreach ($bankAccounts as $accountData) {
            // 過濾空資料
            if (empty($accountData['bank_name']) && empty($accountData['account_number'])) {
                continue;
            }
            
            CompanyBankAccount::create([
                'company_id' => $company->id,
                'bank_name' => $accountData['bank_name'] ?? null,
                'branch_name' => $accountData['branch_name'] ?? null,
                'account_number' => $accountData['account_number'] ?? null,
                'is_default' => isset($accountData['is_default']) && $accountData['is_default'] == '1',
            ]);
        }
    }
}
