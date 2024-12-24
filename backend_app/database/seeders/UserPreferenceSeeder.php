<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserPreference;

class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        UserPreference::create([
            'user_id' => 1,
            'type' => UserPreference::TYPE_ARTICLES,
            'preference_master_id'  => 1
        ]);
        UserPreference::create([
            'user_id' => 1,
            'type' => UserPreference::TYPE_AUTHORS,
            'preference_master_id'  => 1
        ]);
        UserPreference::create([
            'user_id' => 1,
            'type' => UserPreference::TYPE_CATEGORIES,
            'preference_master_id'  => 1
        ]);
        UserPreference::create([
            'user_id' => 1,
            'type' => UserPreference::TYPE_SOURCES,
            'preference_master_id'  => 1
        ]);
    }
}
