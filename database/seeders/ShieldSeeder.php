<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"Super Admin","guard_name":"web","permissions":[]},{"name":"Admin","guard_name":"web","permissions":["view_achievement","view_any_achievement","create_achievement","update_achievement","restore_achievement","restore_any_achievement","replicate_achievement","reorder_achievement","delete_achievement","delete_any_achievement","force_delete_achievement","force_delete_any_achievement"]}]';
        $directPermissions = '{"12":{"name":"view_shield::role","guard_name":"web"},"13":{"name":"view_any_shield::role","guard_name":"web"},"14":{"name":"create_shield::role","guard_name":"web"},"15":{"name":"update_shield::role","guard_name":"web"},"16":{"name":"delete_shield::role","guard_name":"web"},"17":{"name":"delete_any_shield::role","guard_name":"web"},"18":{"name":"view_user","guard_name":"web"},"19":{"name":"view_any_user","guard_name":"web"},"20":{"name":"create_user","guard_name":"web"},"21":{"name":"update_user","guard_name":"web"},"22":{"name":"restore_user","guard_name":"web"},"23":{"name":"restore_any_user","guard_name":"web"},"24":{"name":"replicate_user","guard_name":"web"},"25":{"name":"reorder_user","guard_name":"web"},"26":{"name":"delete_user","guard_name":"web"},"27":{"name":"delete_any_user","guard_name":"web"},"28":{"name":"force_delete_user","guard_name":"web"},"29":{"name":"force_delete_any_user","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
