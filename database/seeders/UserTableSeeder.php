<?php

namespace Database\Seeders;

use App\Models\AclPermission;
use App\Models\AclRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    private $created_role_id = '';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system defined roles
        $this->createRole(1, 'super_admin', 'Super Admin');
        $this->createRole(2, 'Stylist', 'Stylist');

        // Create super admin
        $this->createSuperAdmin(1,'Super Admin', 'superadmin', 'pbms@mail.com', 'pass@123');
    }

    private function createRole($id, $name, $description)
    {
        $role = AclRole::updateOrCreate([
            'id' => $id
        ], [
            'name' => $name,
            'description' => $description,
            'system_defined' => 1,
        ]);

        $permissions = AclPermission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $this->created_role_id = $role->id;
    }

    private function createSuperAdmin($id, $name, $user_name, $email, $password)
    {

        $user = User::updateOrCreate([
            'id' => $id
        ], [
            'name'=>$name,
            'username'=>$user_name,
            'email'=>$email,
            'password'=>Hash::make($password)
        ]);

        $user->assignRole('super_admin');
    }
}
