<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\NewsApiDotOrgService;

class ArticleSyncNewsApiDotOrg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:article-sync-news-api-dot-org';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync articles from NewsApi.org';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // NEWS API DOT ORG
        $newsApiDotOrgService = new NewsApiDotOrgService();
        $newsApiDotOrgService->getArticles();
        return Command::SUCCESS;
    }
}
