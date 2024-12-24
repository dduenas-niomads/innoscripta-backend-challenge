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
        /**
         * I use withoutTrashed function from SoftDeletes trait and
         * ofType is a local scope to filter by articles by Type.
        */
        $articles = Article::withoutTrashed()
            ->ofType()
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
        // Return a collection resource of Articles.
        return ArticleResource::setMode('articles')::collection($articles);
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
