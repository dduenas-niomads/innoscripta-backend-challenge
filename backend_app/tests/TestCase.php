<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // All tests are running in a test database configured in phpunit.xml

    // Work on a clean database for every test with RefreshDatabase
    use RefreshDatabase;
    // $seed = true help us to run all seeders
    protected $seed = true;

    // Set up attributes
    public $code;
    public $name;
    public $email;
    public $password;
    public $notification;
    public $articleTitle;
    public $correctSlug;
    public $wrongSlug;

    public function setUp(): void {
        parent::setUp();
        $this->code         = '123456';
        $this->name         = 'Daniel Dueñas';
        $this->email        = 'dduenas@niomads.com';
        $this->password     = 'Niomads2024.';
        $this->notification = false;
        $this->articleTile  = 'This article about Peru is amazing';
        $this->correctSlug  = 'this_article_about_peru_is_amazing';
        $this->wrongSlug    = 'this_article_dont_exists';
    }
}
