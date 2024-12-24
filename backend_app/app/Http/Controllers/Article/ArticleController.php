<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * Fetch articles.
     */
    public function index(ArticleRequest $request)
    {
        if (isset($request->keywords) || isset($request->author) || 
            isset($request->category) || isset($request->source) || 
            isset($request->published_at)) {
            // Search functionality to filter Articles without cache.
            $articles = self::filterArticlesByParameters($request);
        } else {
            // Cache Eloquent Query Results to Load Articles Instantly.
            $articles = self::fetchAllArticles($request);
        }
        
        // Return a collection resource of Articles.
        return ArticleResource::setMode('articles')::collection($articles);
    }

    private static function fetchAllArticles(ArticleRequest $request) 
    {
        // Paginate the results - 10 items per page
        return cache()->remember('article_list_page_' . (isset($request->page) ? $request->page : 0), config('cache.time_in_seconds'), 
            function() {
                return Article::withoutTrashed()
                    ->paginate(env('ITEMS_PAGINATOR'));
            });
    }

    private static function filterArticlesByParameters(ArticleRequest $request) 
    {
        /** I use withoutTrashed function from SoftDeletes trait. */
            $articles = Article::withoutTrashed()
        // Filter by keywords - text can be Like %%
                ->where(Article::TABLE_NAME . ".keywords", "LIKE", "%{$request->keywords}%");
        // Filter by published_at - date needs to be equal.
            if (isset($request->published_at)) {
                $articles = $articles->where(Article::TABLE_NAME . ".published_at", $request->published_at);
            }
        // Filter by author name
            $articles = $articles->whereHas("author", function ($query) use ($request) {
                return $query->where("name", "like", "%{$request->author}%");
            })
        // Filter by category name
            ->whereHas("category", function ($query) use ($request) {
                return $query->where("name", "like", "%{$request->category}%");
            })
        // Filter by source name
            ->whereHas("source", function ($query) use ($request) {
                return $query->where("name", "like", "%{$request->source}%");
            })
        // Paginate the results - 10 items per page
            ->paginate(env('ITEMS_PAGINATOR'));

        return $articles;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        //
    }

    /**
     * Retrieving a single article by slug.
     */
    public function show(Article $article)
    {
        //
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        //
    }
}
