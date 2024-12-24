<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResetCodePassword;

class ResetCodePasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ResetCodePassword::create([
            'code'  => '123456',
            'email' => 'dduenas@niomads.com'
        ]);
    }
}
