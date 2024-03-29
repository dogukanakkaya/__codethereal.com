<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'developer'
        ]);

        /* Settings */
        Permission::create([
            'name' => 'see_settings',
            'group' => 'settings',
            'title' => 'See Settings'
        ]);
        Permission::create([
            'name' => 'update_settings',
            'group' => 'settings',
            'title' => 'Update Settings'
        ]);
        /* /Settings */

        /* Users */
        Permission::create([
            'name' => 'see_users',
            'group' => 'users',
            'title' => 'See Users'
        ]);
        Permission::create([
            'name' => 'create_users',
            'group' => 'users',
            'title' => 'Create Users'
        ]);
        Permission::create([
            'name' => 'update_users',
            'group' => 'users',
            'title' => 'Update Users'
        ]);
        Permission::create([
            'name' => 'delete_users',
            'group' => 'users',
            'title' => 'Delete Users'
        ]);
        /* /Users */

        /* Menus */
        Permission::create([
            'name' => 'see_menus',
            'group' => 'menus',
            'title' => 'See Menus'
        ]);
        Permission::create([
            'name' => 'create_menus',
            'group' => 'menus',
            'title' => 'Create Menus'
        ]);
        Permission::create([
            'name' => 'update_menus',
            'group' => 'menus',
            'title' => 'Update Menus'
        ]);
        Permission::create([
            'name' => 'delete_menus',
            'group' => 'menus',
            'title' => 'Delete Menus'
        ]);
        /* /Menus */

        /* Contents */
        Permission::create([
            'name' => 'see_posts',
            'group' => 'posts',
            'title' => 'See Posts'
        ]);
        Permission::create([
            'name' => 'create_posts',
            'group' => 'posts',
            'title' => 'Create Posts'
        ]);
        Permission::create([
            'name' => 'update_posts',
            'group' => 'posts',
            'title' => 'Update Posts'
        ]);
        Permission::create([
            'name' => 'delete_posts',
            'group' => 'posts',
            'title' => 'Delete Posts'
        ]);
        Permission::create([
            'name' => 'sort_posts',
            'group' => 'posts',
            'title' => 'Sort Posts'
        ]);
        /* /Contents */
    }
}
