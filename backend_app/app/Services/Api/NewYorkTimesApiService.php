<?php

namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\ArticleService;

class NewYorkTimesApiService
{
    protected $apiKey;
    protected $apiUrl;
    protected $cacheTime;
    protected ArticleService $articleService;

    public function __construct()
    {
        $this->apiKey = config('services.newyork_times_api.api_key');
        $this->apiUrl = config('services.newyork_times_api.api_url');
        $this->cacheTime = config('cache.time_in_seconds');
        $this->articleService = new ArticleService();
    }
    
    public function getArticles($scenario = null, $keyword = 'new_york_times')
    {
        /** 
         * Use keyword if cache is needed. This example is without cache.
         * Concat $scenario to apiKey to do a failed request to the api.
         * This is going to help us when we create a test case. 
        */        
        $response = Http::get($this->apiUrl, [
            'api-key' => $this->apiKey . $scenario
        ]);
        $articles = $this->handleExternalSourceResponse($response);

        return $this->syncArticlesToDb($articles);
    }

    private function syncArticlesToDb($articles = [])
    {
        $syncArticlesToDb  = [
            'status'         => (count($articles) > 0) ? 'ok' : 'error',
            'message'        => (count($articles) > 0) ? 'Articles synced from New york times.' : 'Problem with the API connection.',
            'new_articles'   => 0,
            'total_articles' => 0,
        ];
        $syncArticlesToDb['total_articles'] = count($articles);
        foreach ($articles as $article) {
            # Find Author, Category, Source and convert article title into slug text
            $getAuthorCategoryAndSourceInConcurrencyMode = 
                $this->articleService->getAuthorCategorySourceAndSlugInConcurrencyMode(
                    $article['byline'], $article['subsection'], $article['source'], $article['title']
                );
            if (!in_array(null, $getAuthorCategoryAndSourceInConcurrencyMode, true)) {
                # Find article in bd using the slug and sourceId.
                $dbArticle = $this->articleService->findArticleBySlugAndSourceId(
                    $getAuthorCategoryAndSourceInConcurrencyMode[3], 
                    $getAuthorCategoryAndSourceInConcurrencyMode[2]->id
                );
                if (is_null($dbArticle)) {
                    # When dbArticle is null means the article don't exists in DB.
                    # So needs to be created.
                    $this->articleService->createNewArticle([
                        'title'        => substr($article['title'], 0, 250),
                        'author_id'    => $getAuthorCategoryAndSourceInConcurrencyMode[0]->id,
                        'category_id'  => $getAuthorCategoryAndSourceInConcurrencyMode[1]->id,
                        'source_id'    => $getAuthorCategoryAndSourceInConcurrencyMode[2]->id,
                        'slug'         => $getAuthorCategoryAndSourceInConcurrencyMode[3],
                        'description'  => substr($article['abstract'], 0, 250),
                        'url'          => substr($article['url'], 0, 250),
                        'keywords'     => substr($article['adx_keywords'], 0, 250),
                        'section'      => $article['section'],
                        'type'         => $article['type'],
                        'media'        => $article['media'],
                        'published_at' => $article['published_date'],
                    ]);
                    $syncArticlesToDb['new_articles']++;
                }
            }
        }

        return $syncArticlesToDb;
    }

    private function handleExternalSourceResponse($jsonResponse = null) {
        $parsedResponse = [];
        if ($jsonResponse->ok()) {
            $jsonResponse = $jsonResponse->json();
            if (isset($jsonResponse['status']) && $jsonResponse['status'] === 'OK') {
                $parsedResponse = isset($jsonResponse['results']) ? $jsonResponse['results'] : [];
            }
        }

        return $parsedResponse;
    }
}
