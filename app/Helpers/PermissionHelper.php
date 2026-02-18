<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * 權限模組中文名稱映射
     */
    public static function getModuleName(string $module): string
    {
        $modules = [
            'users' => '用戶管理',
            'companies' => '公司管理',
            'projects' => '專案管理',
            'receivables' => '應收帳款',
            'payables' => '應付帳款',
            'salaries' => '薪資管理',
            'reports' => '報表管理',
            'settings' => '系統設定',
            'roles' => '角色權限',
        ];

        return $modules[$module] ?? ucfirst($module);
    }

    /**
     * 權限操作中文名稱映射
     */
    public static function getActionName(string $action): string
    {
        $actions = [
            'view' => '查看',
            'create' => '新增',
            'edit' => '編輯',
            'delete' => '刪除',
            'pay' => '發放',
            'export' => '匯出',
        ];

        return $actions[$action] ?? ucfirst($action);
    }

    /**
     * 取得權限完整中文名稱
     */
    public static function getPermissionName(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        
        if (count($parts) !== 2) {
            return $permissionName;
        }

        [$module, $action] = $parts;
        
        return self::getModuleName($module) . ' - ' . self::getActionName($action);
    }
}
