<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin', 'vendor', 'customer', 'deliveryBoy'];

        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }
    }
}
