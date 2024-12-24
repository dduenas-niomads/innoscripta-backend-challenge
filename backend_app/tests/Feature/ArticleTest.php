<?php

namespace Tests\Feature;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;

class ArticleTest extends TestCase
{
    /** Test if guest is not allowed to fetch articles */
    public function test_guest_cannot_fetch_articles()
    {
        $this->json('get', route('articles.index'))->assertStatus(401);
    }

    /** Test if logged user is allowed to fetch articles */
    public function test_logged_user_can_fetch_articles()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.index']
        );

        $this->json('get', route('articles.index'))->assertOk();
    }

    /** Test if fetch articles are paginated */
    public function test_fetch_articles_are_paginated()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.index']
        );

        $this->json('get', route('articles.index'))->assertOk()
            ->assertJsonStructure([
                // Validate if links and meta atributes exists in Json response body
                "links", "meta"
            ]);
    }

    /** Test if user can filter articles by keywords */
    public function test_user_can_filter_articles_by_keywords()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.index']
        );

        $this->json('get', route('articles.index', ['keywords' => 'peru']))->assertOk();
    }

    /** Test if user can filter articles by date */
    public function test_user_can_filter_articles_by_date()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.index']
        );

        $this->json('get', route('articles.index', ['date' => date("Y-m-d")]))->assertOk();
    }

    /** Test if user can filter articles by object (Can be Author, Category or Source) */
    public function test_user_can_filter_articles_by_object()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.index']
        );

        $this->json('get', route('articles.index', ['author' => 'Daniel']))->assertOk();
    }

    /** Test if guest is not allowed to logout from all sessions */
    public function test_user_can_change_to_other_page_result()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.index']
        );

        $this->json('get', route('articles.index', ['page' => 2]))->assertOk();
    }

    /** Test if guest is not allowed to logout from all sessions */
    public function test_page_field_must_be_a_number()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.index']
        );

        $this->json('get', route('articles.index', ['page' => 'dos']))->assertStatus(422);
    }

    /** Test if guest is not allowed to logout from all sessions */
    public function test_retrieve_a_single_article()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.show']
        );

        $this->json('get', route('articles.show', ['article' => $this->correctSlug]))->assertOk();
    }

    /** Test if guest is not allowed to logout from all sessions */
    public function test_article_not_found()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['articles.show']
        );

        $this->json('get', route('articles.show', ['article' => $this->wrongSlug]))->assertStatus(404);
    }
}
