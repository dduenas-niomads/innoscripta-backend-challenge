<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\NewYorkTimesApiService;

class ArticleSyncNewYorkTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:article-sync-new-york-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync articles from New York Times api.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // NEW YORK TIMES API
        $newYorkTimesApiService = new NewYorkTimesApiService();
        $newYorkTimesApiService->getArticles();
        return Command::SUCCESS;
    }
}
