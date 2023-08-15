<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode' => 'EDM01',
            ],
            [
                'kode' => 'EDM02',
            ],
            [
                'kode' => 'EDM03',
            ],
            [
                'kode' => 'EDM04',
            ],
            [
                'kode' => 'EDM05',
            ],
        ];

        foreach ($data as $item) {
            Domain::create([
                'kode' => $item['kode'],
            ]);
        }
    }
}
