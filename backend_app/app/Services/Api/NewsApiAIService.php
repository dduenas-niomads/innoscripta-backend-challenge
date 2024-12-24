<?php

namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\ArticleService;

class NewsApiAIService
{
    protected $apiKey;
    protected $apiUrl;
    protected $cacheTime;
    protected ArticleService $articleService;

    public function __construct()
    {
        $this->apiKey = config('services.news_api_ai.api_key');
        $this->apiUrl = config('services.news_api_ai.api_url');
        $this->cacheTime = config('cache.time_in_seconds');
        $this->articleService = new ArticleService();
    }

    public function getArticles($keyword = 'news_api_ai')
    {
        // Use keyword if cache is needed.
        // This example is without cache.
        $response = Http::get($this->apiUrl, [
            'action'  => 'getArticles',
            'keyword' => 'Spain',
            'sourceLocationUri' => [
                'http://en.wikipedia.org/wiki/United_States',
                'http://en.wikipedia.org/wiki/Canada',
                'http://en.wikipedia.org/wiki/United_Kingdom'
            ],
            'ignoreSourceGroupUri' => 'paywall/paywalled_sources',
            'articlesPage' => 1,
            'articlesCount' => 20,
            'articlesSortBy' => 'date',
            'articlesSortByAsc' => false,
            'dataType' => [
                'news'
            ],
            'forceMaxDataTimeWindow' => 10,
            'resultType' => 'articles',
            'apiKey' => $this->apiKey
        ]);
        $articles = $this->handleExternalSourceResponse($response);

        return $this->syncArticlesToDb($articles);
    }

    private function syncArticlesToDb($articles = [])
    {
        $syncArticlesToDb  = [
            'status'         => (count($articles) > 0) ? 'ok' : 'error',
            'message'        => 'Articles synced from NewApi.ai (AKA eventregistry)',
            'new_articles'   => 0,
            'total_articles' => 0,
        ];
        $syncArticlesToDb['total_articles'] = count($articles);
        foreach ($articles as $article) {
            # Find Author, Category, Source and convert article title into slug text
            $getAuthorCategoryAndSourceInConcurrencyMode = 
                $this->articleService->getAuthorCategorySourceAndSlugInConcurrencyMode(
                    isset($article['source']) ? $article['source']['uri'] : null, $article['dataType'], 
                    isset($article['source']) ? $article['source']['title'] : null, $article['title']
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
                        'description'  => substr($article['body'], 0, 250),
                        'url'          => substr($article['url'], 0, 250),
                        'keywords'     => substr($article['title'], 0, 250),
                        'section'      => 'wikipedia-news',
                        'type'         => 'news',
                        'media'        => [
                            'url_to_image' => ($article['image']) ? $article['image'] : null
                        ],
                        'published_at' => $article['date'],
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
            if (isset($jsonResponse['articles'])) {
                $parsedResponse = isset($jsonResponse['articles']['results']) ? $jsonResponse['articles']['results'] : [];
            }
        }

        return $parsedResponse;
    }
}
