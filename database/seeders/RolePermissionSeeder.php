<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get roles
        $adminRole = Role::findByName('admin');
        $storeAdminRole = Role::findByName('vendor');
        $deliveryBoyRole = Role::findByName('deliveryBoy');
        $customerRole = Role::findByName('customer');

        // get permission
        $createProductPermission = Permission::findByName('add-product');
        $updateProductPermission = Permission::findByName('edit-product');
        $deleteProductPermission = Permission::findByName('delete-product');
        // give permission for roles
        
        $adminRole->givePermissionTo([
            $createProductPermission,
            $updateProductPermission,
            $deleteProductPermission]);
        $storeAdminRole->givePermissionTo([
            $createProductPermission,
            $updateProductPermission,
            $deleteProductPermission]);
    }
}
