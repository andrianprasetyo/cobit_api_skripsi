<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Administrator',
                'username' => 'admin',
                'password' => 'admin',
                'status' => 'active'
            ],
            [
                'nama' => 'Assesor Admin',
                'username' => 'assesor',
                'password' => 'admin',
                'status' => 'active'
            ]
        ];
        foreach ($data as $item) {
            User::create([
                'nama' => $item['nama'],
                'username' => $item['username'],
                'password' => $item['password'],
                'status' => $item['status']
            ]);
        }
    }
}
