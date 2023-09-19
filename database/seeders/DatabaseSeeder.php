<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AssesmentSeeder::class,
            OrganisasiSeeder::class,
            DesainFaktorSeeder::class,
            DesainFaktorRefSeeder::class,
            QuisionerSeeder::class,
            // QuisionerPilganSeeder::class,
            QuisionerGrupPilganSeeder::class,
            RoleUsersSeeder::class,
            DomainSeeder::class,
            QuesionerPertanyaan::class,
            Df1Seeder::class,
            Df3Seeder::class,
            Df4Seeder::class,
            Df5Seeder::class,
            Df6Seeder::class,
            Df7Seeder::class,
            Df8Seeder::class,
            Df9Seeder::class,
            Df10Seeder::class,
            DfAdditionalSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
