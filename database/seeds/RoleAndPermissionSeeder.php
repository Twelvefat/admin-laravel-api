<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'setting index']);
        Permission::create(['name' => 'setting update']);
        Permission::create(['name' => 'user index']);
        Permission::create(['name' => 'user create']);
        Permission::create(['name' => 'user edit']);
        Permission::create(['name' => 'user delete']);
        Permission::create(['name' => 'role index']);
        Permission::create(['name' => 'role create']);
        Permission::create(['name' => 'role edit']);
        Permission::create(['name' => 'role delete']);
        Permission::create(['name' => 'permission index']);
        Permission::create(['name' => 'permission create']);
        Permission::create(['name' => 'permission edit']);
        Permission::create(['name' => 'permission delete']);
        Permission::create(['name' => 'activity index']);

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
