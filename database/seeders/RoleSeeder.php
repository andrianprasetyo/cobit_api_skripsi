<?php

namespace Database\Seeders;

use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Administrator',
            ],
            [
                'nama' => 'Assesor',
            ],
            [
                'nama' => 'Respondent',
            ]
        ];

        foreach ($data as $item) {
            Roles::create([
                'code' => Str::slug($item['nama'], '.'),
                'nama' => $item['nama']
            ]);
        }
    }
}
