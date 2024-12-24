<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommandTest extends TestCase
{
    /** Test command to sync articles from New York Times source.*/
    public function test_sync_articles_from_new_york_times()
    {
        $this->artisan('app:article-sync-new-york-times')->assertSuccessful();
    }
    /** Test command to sync articles from News.org source.*/
    public function test_sync_articles_from_news_dot_org()
    {
        $this->artisan('app:article-sync-news-api-dot-org')->assertSuccessful();
    }
    /** Test command to sync articles from News-api.ai source.*/
    public function test_sync_articles_from_new_api_ai()
    {
        $this->artisan('app:article-sync-news-api-ai')->assertSuccessful();
    }
    /** Test command to sync articles from source with invalid ApiKey.*/
    public function test_sync_articles_from_source_with_wrong_api_key()
    {
        // In this case, I'm using NYT. But work with every source.
        $this->artisan('app:article-sync-new-york-times fail')->assertFailed();
    }
}
