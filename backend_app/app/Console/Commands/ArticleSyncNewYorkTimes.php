<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\NewYorkTimesApiService;
use Illuminate\Support\Facades\Concurrency;

class ArticleSyncNewYorkTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:article-sync-new-york-times {scenario?}';

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
        $this->info("================");
        /** 
         * I use scenario argument for testing. $scenario is used as a text 
         * to be concatenated in the apiKey and make a wrong fetch to the source.
         * I can decide to use a correct command execution or a failed execution.
        */
        $scenario = $this->argument('scenario');
        $this->info($this->description . ' - scenario:' . (isset($scenario) ? $scenario : "correct"));
        try {
            $newYorkTimesApiService = new NewYorkTimesApiService();
            $result = $newYorkTimesApiService->getArticles($scenario);
            if (isset($result['status']) && $result['status'] === 'ok') {
                $this->info('new_articles: ' . (isset($result['new_articles']) ? $result['new_articles'] : '0'));
                $this->info('total_articles: ' . (isset($result['total_articles']) ? $result['total_articles'] : '0'));
            } else {
                $this->info((isset($result['message']) ? $result['message'] : json_encode($result)));
                $this->info('Process ended with failure.');
                return Command::FAILURE;
            }
        } catch (\Throwable $th) {
            $this->error("Error: " . $th->getMessage());
            return Command::FAILURE;
        }
        $this->info('Process ended correctly.');

        return Command::SUCCESS;
    }
}
