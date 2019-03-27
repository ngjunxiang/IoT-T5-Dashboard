<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_tenant = Role::where('name', 'tenant')->first();
        $role_manager = Role::where('name', 'manager')->first();

        $tenant = new User();
        $tenant->name = 'Tenant';
        $tenant->email = 'tenant@hubquarters.com';
        $tenant->password = Hash::make('tenant');
        $tenant->role_id = $role_tenant->id;
        $tenant->save();

        $manager = new User();
        $manager->name = 'Manager';
        $manager->email = 'manager@hubquarters.com';
        $manager->password = Hash::make('manager');
        $manager->role_id = $role_manager->id;
        $manager->save();
    }
}
