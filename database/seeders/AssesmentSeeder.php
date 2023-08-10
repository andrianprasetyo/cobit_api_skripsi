<?php

namespace Database\Seeders;

use App\Models\Assesment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssesmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => date('Y'),
            ]
        ];

        foreach ($data as $item) {
            Assesment::create([
                'nama' => $item['nama']
            ]);
        }
    }
}
