<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::create([
            'name' => 'Thae Ei',
            'email' => 'thaeeithantun@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'thaeeithantun.dev@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $manager = User::create([
            'name' => 'Manager',
            'email' => 'thaeeithantunapple@gmail.com',
            'password' => Hash::make('password'),
        ]);
        
        $admin_role = Role::create([
            'name' => 'admin',
            'guard_name' => 'api'
        ]);
        $manager_role = Role::create([
            'name' => 'manager',
            'guard_name' => 'api'
        ]);
        $user_role = Role::create([
            'name' => 'user',
            'guard_name' => 'api'
        ]);
        
        $blog_permissions = [
            'create_blog',
            'read_blogs',
            'update_blog',
            'delete_blog',
        ];
        foreach($blog_permissions as $blog_permission)
        {
            $permission = Permission::create([
                'name' => $blog_permission,
                'guard_name' => 'api'
            ]);

            $admin_role->givePermissionTo($permission);
            $admin->assignRole($admin_role);

            $manager_role->givePermissionTo($permission);
            $manager->assignRole($manager_role);

            $user_role->givePermissionTo($permission);
            $user->assignRole($user_role);
        }

        $user_permissions = [
            'create_user',
            'read_users',
            'update_user',
            'delete_user'
        ];
        foreach($user_permissions as $user_permission)
        {
            $permission = Permission::create([
                'name' => $user_permission,
                'guard_name' => 'api'
            ]);

            $admin_role->givePermissionTo($permission);
            $admin->assignRole($admin_role);
        }

        $titles = [
            'Blog one',
            'Blog two',
            'Blog three'
        ];

        foreach($titles as $title)
        {
            Blog::create([
                'title' => $title,
                'description' => 'Description......',
                'user_id' => $user->id
            ]);
        }

        $titles = [
            'Blog four',
            'Blog five',
            'Blog six'
        ];

        foreach($titles as $title)
        {
            Blog::create([
                'title' => $title,
                'description' => 'Description......',
                'user_id' => $admin->id
            ]);
        }
        
    }
}
