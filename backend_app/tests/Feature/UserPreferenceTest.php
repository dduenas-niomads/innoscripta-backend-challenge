<?php

namespace Tests\Feature;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserPreference;

class UserPreferenceTest extends TestCase
{
    /** Test guest cannot fetch preferences. */
    public function test_guest_cannot_fetch_preferences()
    {
        $this->json('get', route('user-preference.index'))->assertStatus(401);
    }
    /** Test logged user can fetch preferences. */
    public function test_logged_user_can_fetch_preferences()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['user-preference.index']
        );
        
        $this->json('get', route('user-preference.index'))->assertOk();
    }
    /** Test guest cannot fetch news feed. */
    public function test_guest_cannot_fetch_news_feed()
    {
        $this->json('get', route('user-preference.showFeed'))->assertStatus(401);
    }
    /** Test logged user can fetch news feed. */
    public function test_logged_user_can_fetch_news_feed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['user-preference.showFeed']
        );
        
        $this->json('get', route('user-preference.showFeed'))->assertOk();
    }
    /** 
     * Test logged user cannot create a new preference with a different type.
     * Only use Articles, Authors, Categories and Sources. 
     */
    public function test_logged_user_cannot_create_a_new_preference_with_a_different_type()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['user-preference.store']
        );
        
        $body = [
            'type' => 'movies',
            'preference_master_id' => '1',
        ];

        $this->json('POST', route('user-preference.store'), $body)->assertStatus(422);
    }
    /** 
     * Test logged user cannot create a new preference with a missing value.
     * Type and preference_master_id are requireds. 
     */
    public function test_logged_user_cannot_create_a_new_preference_with_a_missing_value()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['user-preference.store']
        );
        
        $body = [
            'type' => 'movies',
        ];

        $this->json('POST', route('user-preference.store'), $body)->assertStatus(422);
    }
    /** Test logged user can delete a preference */
    public function test_guest_cannot_delete_a_preference()
    {
        $userPreference = UserPreference::withoutTrashed()->first();

        $this->json('DELETE', route('user-preference.destroy', ['user_preference' => $userPreference]))->assertStatus(401);
    }
}
