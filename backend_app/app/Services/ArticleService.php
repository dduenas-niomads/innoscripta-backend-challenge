<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use Illuminate\Support\Facades\Concurrency;

class ArticleService
{
    public function getAuthorCategorySourceAndSlugInConcurrencyMode($authorName, $categoryName, $sourceName, $articleTitle) {
        /** Using Concurrency to handle different functions at same time */        
        return Concurrency::run([
            fn() => AuthorService::getAuthor($authorName),
            fn() => CategoryService::getCategory($categoryName),
            fn() => SourceService::getSource($sourceName),
            fn() => self::getSlugFromArticleTitle($articleTitle),
        ]);
    }
    
    public static function getSlugFromArticleTitle($title, $divider = '_') {
        // replace non letter or digits by divider
        $slug = preg_replace('~[^\pL\d]+~u', $divider, $title);
        // transliterate
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        // remove unwanted characters
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        // trim - remove all spaces
        $slug = trim($slug, $divider);
        // remove duplicate divider
        $slug = preg_replace('~-+~', $divider, $slug);
        // lowercase
        $slug = strtolower($slug);
        // return title as slug
        return empty($slug) ? 'no_slug' : substr($slug, 0, 250);
    }

    public function createNewArticle($articleData = []) {
        return Article::create($articleData);
    }

    public function findArticleBySlugAndSourceId($slug, $sourceId) {
        return Article::withoutTrashed()
            ->whereSlug($slug)
            ->first();
    }

}