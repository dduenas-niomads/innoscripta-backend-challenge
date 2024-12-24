<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\NewsApiAIService;

class ArticleSyncNewsApiAi extends Command
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
        $this->info("================");
        $this->info($this->description);
        try {
            $newsApiAIService = new NewsApiAIService();
            $result = $newsApiAIService->getArticles();
            $this->info('new_articles: ' . (isset($result['new_articles']) ? $result['new_articles'] : '0'));
            $this->info('total_articles: ' . (isset($result['total_articles']) ? $result['total_articles'] : '0'));
        } catch (\Throwable $th) {
            $this->error("Error: " . $th->getMessage());
            return Command::FAILURE;
        }
        $this->info('Process ended correctly');

        return Command::SUCCESS;
    }
}
