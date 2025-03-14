<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::query()->delete();

        for ($i = 0; $i < 10; $i++) {
            User::factory()->create([
                'first_name'  => 'Test',
                'last_name'   => 'User ' . $i,
                'email'       => 'user' . $i . '@example.com',
                'super_admin' => 0,
            ]);
        }

        User::factory()->create([
            'first_name'  => 'Admin',
            'last_name'   => 'User',
            'email'       => 'admin@example.com',
            'super_admin' => 1,
        ]);

        $this->call(ProjectSeeder::class);
    }
}
