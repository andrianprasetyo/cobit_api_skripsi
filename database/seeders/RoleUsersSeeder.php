<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'users_id' => User::where('username','admin')->first()->id,
                'roles_id'=>Roles::where('code','administrator')->first()->id,
            ],
            [
                'users_id' => User::where('username', 'assesor')->first()->id,
                'roles_id' => Roles::where('code', 'assesor')->first()->id,
            ]
        ];

        foreach ($data as $item) {
            RoleUsers::create([
                'users_id' => $item['users_id'],
                'roles_id' => $item['roles_id'],
            ]);
        }
    }
}
