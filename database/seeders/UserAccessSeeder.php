<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserAccessSeeder extends Seeder
{
    private const PERMISSIONS = [
        'DASHBOARD' => [
            'view_dashboard',
        ],
        'BPL' => [
            'bpl_view',
            'bpl_create',
            'bpl_update',
            'bpl_delete',
            'bpl_import',
            'bpl_export',
        ],
        'PARTNER' => [
            'partner_view',
            'partner_create',
            'partner_update',
            'partner_delete',
            'partner_import',
            'partner_export',
        ],
        'ORDER' => [
            'order_view',
            'order_create',
            'order_update',
            'order_delete',
            'order_repor_document',
            'order_export',
        ],
        'USER_CONTROL' => [
            'user_control_view',
            'user_control_create',
            'user_control_update',
            'user_control_delete',
            'user_control_acesss_view',
            'user_control_acesss_create',
            'user_control_acesss_update',
            'user_control_acesss_delete',
        ],
        'PROJECT' => [
            'project_view',
            'project_create',
            'project_update',
            'project_delete',
        ],
        'BILL' => [
            'bill_view',
            'bill_create',
            'bill_update',
            'bill_delete'
        ],
        'PAYMENT' => [
            'payment_view',
            'payment_create',
            'payment_update',
            'payment_delete'
        ]
    ];

    private const ROLES = [
        'SUPER_ADMIN' => 'SUPER_ADMIN',
        'EDITOR' => 'EDITOR',
        'USER' => 'USER',
    ];

    public function run(): void
    {
        $this->createPermissions();
        $this->createRolesWithPermissions();
        $this->assignRoleToUser();
    }

    private function assignRoleToUser()
    {
        $user = User::where('username', 'adminproc')->first();
        $user->assignRole('SUPER_ADMIN');
    }

    private function createPermissions(): void
    {
        $allPermissions = $this->getAllPermissions();

        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
    private function createRolesWithPermissions(): void
    {
        $roles = $this->createRoles();
        $this->assignSuperAdminPermissions($roles['SUPER_ADMIN']);
        $this->assignEditorPermissions($roles['EDITOR']);
        $this->assignUserPermissions($roles['USER']);
    }

    private function createRoles(): array
    {
        $roles = [];
        foreach (self::ROLES as $role) {
            $roles[$role] = Role::create(['name' => $role]);
        }
        return $roles;
    }

    private function getAllPermissions(): array
    {
        return array_merge(...array_values(self::PERMISSIONS));
    }

    private function getSpecialPermissions(): array
    {
        return array_merge(
            self::PERMISSIONS['USER_CONTROL'],
            self::PERMISSIONS['PROJECT']
        );
    }

    private function assignSuperAdminPermissions(Role $superAdmin): void
    {
        $superAdmin->givePermissionTo($this->getAllPermissions());
    }

    private function assignEditorPermissions(Role $editor): void
    {
        $allPermissions = $this->getAllPermissions();
        $specialPermissions = $this->getSpecialPermissions();

        $editorPermissions = array_diff($allPermissions, $specialPermissions);
        $editor->givePermissionTo($editorPermissions);
    }

    private function assignUserPermissions(Role $user): void
    {
        $basicPermissions = [
            ...self::PERMISSIONS['DASHBOARD'],
            'bpl_view',
            'partner_view',
            'order_view',
        ];

        $user->givePermissionTo($basicPermissions);
    }
}
