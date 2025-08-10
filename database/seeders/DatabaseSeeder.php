<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->withPersonalTeam()->create();

        $user = User::factory()->withPersonalTeam()->create([
            'name' => 'Test Admin',
            'email' => 'admin@sypspace.com',
        ]);

        Artisan::call('shield:super-admin');

        $this->call([
            CountrySeeder::class,
        ]);
    }
}
