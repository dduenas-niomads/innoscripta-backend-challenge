<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    public $name;
    public $email;
    public $password;

    public function setUp(): void
    {
        parent::setUp();
        $this->name  = 'Daniel Dueñas';
        $this->email = 'dduenas@niomads.com';
        $this->password = 'Niomads2024.';

        \Artisan::call('migrate:fresh', ['-vvv' => true]);
    }

    /** Test a correct login case */
    public function test_login_success()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'email' => $this->email,
            'password' => $this->password
        ];

        $this->json('POST', route('auth.login'), $body)->assertOk();
    }

    /** Test an incorrect login case when email is correct but password is incorrect */
    public function test_login_error_email_exists_but_incorrect_password()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'email' => 'dduenas@niomads.com',
            'password' => 'password'
        ];

        $this->json('POST', route('auth.login'), $body)->assertStatus(400);
    }

    /** Test an incorrect login case when email is incorrect (user not exists) */
    public function test_login_error_email_is_invalid()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'email' => 'fake@email.com',
            'password' => 'password'
        ];

        $this->json('POST', route('auth.login'), $body)->assertStatus(422);
    }

    /** Test a correct user registration case */
    public function test_registration_success()
    {
        $body = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password
        ];

        $this->json('POST', route('auth.register'), $body)->assertOk()
            ->assertJson([
                // Validate if status atribute exists in Json response body
                "status" => "ok"
            ]);
    }

    /** Test an incorrect user registration case (All scenarios) */
    public function test_registration_error()
    {
        $body = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => '12345678', //simple password scenario
            'password_confirmation' => '12345678'
        ];

        // You can change the body to have another wrong scenario
        // For example: password_confirmation missing, or password and p_confirmation not match or email already exists
        // All wrong cases throw a 422 status

        $this->json('POST', route('auth.register'), $body)->assertStatus(422);
    }

    /** Test if logged user can see profile information */
    public function test_authenticated_user_can_see_profile_information()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['user.profileInfo']
        );

        $this->json('get', route('user.profileInfo'))->assertOk();
    }

    /** Test if guest can see profile information */
    public function test_guest_cannot_see_profile_information()
    {
        $this->json('get', route('user.profileInfo'))->assertStatus(401);
    }

    /** Test if logged user is allowed to refresh API tokens */
    public function test_logged_user_can_refresh_api_tokens()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['auth.refreshToken']
        );
        
        $this->json('post', route('auth.refreshToken'))->assertOk();
    }

    /** Test if guest is not allowed refresh API tokens */
    public function test_guest_cannot_refresh_api_tokens()
    {
        $this->json('post', route('auth.refreshToken'))->assertStatus(401);
    }

    /** Test if logged user is allowed to logout from current session */
    public function test_logged_user_can_logout_from_current_session()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['auth.logout']
        );
        
        $this->json('post', route('auth.logout'))->assertOk();
    }

    /** Test if guest is not allowed to logout from single session */
    public function test_guest_cannot_logout_from_single_session()
    {
        $this->json('post', route('auth.logout'))->assertStatus(401);
    }

    /** Test if logged user is allowed to logout from all sessions */
    public function test_logged_user_can_logout_from_all_sessions()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['auth.logout']
        );
     
        $this->json('post', route('auth.logoutAll'))->assertOk();
    }

    /** Test if guest is not allowed to logout from all sessions */
    public function test_guest_cannot_logout_from_all_sessions()
    {
        $this->json('post', route('auth.logoutAll'))->assertStatus(401);
    }

}
