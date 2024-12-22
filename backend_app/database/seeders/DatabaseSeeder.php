<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ResetCodePassword;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Daniel Dueñas',
            'email' => 'dduenas@niomads.com',
            'password' =>  Hash::make('Niomads2024.')
        ]);

        ResetCodePassword::create([
            'code'  => '123456',
            'email' => 'dduenas@niomads.com'
        ]);
    }
}
