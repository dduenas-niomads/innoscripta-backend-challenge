<?php

namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\ArticleService;

class NewsApiDotOrgService
{
    protected $apiKey;
    protected $apiUrl;
    protected $cacheTime;
    protected ArticleService $articleService;

    public function __construct()
    {
        $this->apiKey = config('services.news_api_dot_org.api_key');
        $this->apiUrl = config('services.news_api_dot_org.api_url');
        $this->cacheTime = config('cache.time_in_seconds');
        $this->articleService = new ArticleService();
    }

    public function getArticles($keyword = 'news_api_dot_org')
    {
        // Use keyword if cache is needed.
        // This example is without cache.
        $response = Http::get($this->apiUrl, [
            'apiKey'   => $this->apiKey,
            'qInTitle' => 'peru',
            'sortBy'   => 'popularity'
        ]);
        $articles = $this->handleExternalSourceResponse($response);

        return $this->syncArticlesToDb($articles);
    }

    private function syncArticlesToDb($articles = [])
    {
        $syncArticlesToDb  = [
            'status'         => (count($articles) > 0) ? 'ok' : 'error',
            'message'        => 'Articles synced from NewApi.org.',
            'new_articles'   => 0,
            'total_articles' => 0,
        ];
        $syncArticlesToDb['total_articles'] = count($articles);
        foreach ($articles as $article) {
            # Find Author, Category, Source and convert article title into slug text
            $getAuthorCategoryAndSourceInConcurrencyMode = 
                $this->articleService->getAuthorCategorySourceAndSlugInConcurrencyMode(
                    $article['author'], 'popular-news', isset($article['source']) ? $article['source']['name'] : null, $article['title']
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
                        'description'  => substr($article['content'], 0, 250),
                        'url'          => substr($article['url'], 0, 250),
                        'keywords'     => substr($article['description'], 0, 250),
                        'section'      => 'popular-news',
                        'type'         => 'news',
                        'media'        => [
                            'url_to_image' => ($article['urlToImage']) ? $article['urlToImage'] : null
                        ],
                        'published_at' => substr($article['publishedAt'], 0, 10),
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
            if (isset($jsonResponse['status']) && $jsonResponse['status'] === 'ok') {
                $parsedResponse = isset($jsonResponse['articles']) ? $jsonResponse['articles'] : [];
            }
        }

        return $parsedResponse;
    }
}
