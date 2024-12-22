<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ResetPasswordTest extends TestCase
{
    public $code;
    public $email;
    public $password;
    public $notification;

    public function setUp(): void
    {
        parent::setUp();
        $this->code  = '123456';
        $this->email = 'dduenas@niomads.com';
        $this->password = 'Niomads2024.';
        $this->notification = false;

        \Artisan::call('migrate:fresh', ['-vvv' => true]);
    }

    /** Test if guest can get a reset code using correct attributes */
    public function test_guest_can_get_reset_password_code()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'email' => $this->email,
            'notification' => $this->notification
        ];

        $this->json('post', route('auth.forgotPassword'), $body)->assertOk();
    }

    /** Test if guest can get a reset code using invalid attributes */
    public function test_guest_cannot_get_reset_password_code()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'email' => 'fake@email.com',
            'notification' => false
        ];

        // You can change the body to have another wrong scenario
        // For example: email missing, notification missing, notification not a boolean or even no attributes sended
        // All wrong cases throw a 422 status

        $this->json('post', route('auth.forgotPassword'), $body)->assertStatus(422);
    }

    /** Test if guest can check a reset password code using correct attributes */
    public function test_guest_can_check_password_reset_code()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'code'  => $this->code,
            'email' => $this->email
        ];

        $this->json('post', route('auth.passwordCodeCheck'), $body)->assertOk();
    }

    /** Test if guest can check a reset password code using invalid attributes */
    public function test_guest_cannot_check_password_reset_code()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'code'  => '100000',
            'email' => $this->email
        ];

        // You can change the body to have another wrong scenario
        // For example: code missing, wrong code, email missing or even no attributes sended
        // All wrong cases throw a 422 status

        $this->json('post', route('auth.passwordCodeCheck'), $body)->assertStatus(422);
    }

    /** Test if guest can reset a password using correct attributes */
    public function test_guest_can_reset_password()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'code'  => $this->code,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password
        ];

        $this->json('post', route('auth.resetPassword'), $body)->assertOk();
    }

    /** Test if guest can reset a password using invalid attributes */
    public function test_guest_cannot_reset_password()
    {
        \Artisan::call('db:seed', ['-vvv' => true]);

        $body = [
            'code'  => '100000',
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password
        ];

        // You can change the body to have another wrong scenario
        // For example: code missing, wrong code, email missing, password missing, confirmation_password missing
        // password and p_confirmation not match or even no attributes sended
        // All wrong cases throw a 422 status

        $this->json('post', route('auth.resetPassword'), $body)->assertStatus(422);
    }

}
