<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\NewsApiAIService;

class ArticleSyncNewsApiAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:article-sync-news-api-ai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync articles from NewsApi.ai (AKA eventregistry)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // NEWS API AI
        $newsApiAIService = new NewsApiAIService();
        $newsApiAIService->getArticles();
        return Command::SUCCESS;
    }
}
