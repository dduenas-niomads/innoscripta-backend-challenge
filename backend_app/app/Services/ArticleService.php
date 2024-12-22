<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use Illuminate\Support\Facades\Concurrency;

class ArticleService
{
    protected AuthorService $authorService;
    protected CategoryService $categoryService;
    protected SourceService $sourceService;

    public function __construct() {
        $this->authorService = new AuthorService();
        $this->categoryService = new CategoryService();
        $this->sourceService = new SourceService();
    }

    public function getAuthorCategorySourceAndSlugInConcurrencyMode($authorName, $categoryName, $sourceName, $articleTitle) {
        /** Using Concurrency to handle different functions at same time */
        [$author, $category, $source, $slug] = Concurrency::driver('sync')->run([
            fn() => $this->authorService->getAuthor($authorName),
            fn() => $this->categoryService->getCategory($categoryName),
            fn() => $this->sourceService->getSource($sourceName),
            fn() => $this->getSlugFromArticleTitle($articleTitle),
        ]);

        return [
            isset($author->id) ? $author : null, 
            isset($category->id) ? $category : null, 
            isset($source->id) ? $source : null, 
            // Convert title of article to slug text
            $slug
        ];
    }
    
    public function getSlugFromArticleTitle($articleTitle, $delimiter = '_') {
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $articleTitle))))), $delimiter));
        
        return $slug;
    }

    public function createNewArticle($articleData = []) {
        $article = Article::create($articleData);
    }

    public function findArticleBySlugAndSourceId($slug, $sourceId) {
        return Article::deletedAt()
            ->whereSlug($slug)
            // ->whereSourceId($sourceId)
            ->first();
    }

}