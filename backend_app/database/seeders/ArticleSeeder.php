<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Article::create([
            'title'        => 'This article about Perú is amazing',
            'author_id'    => Author::first()->id,
            'category_id'  => Category::first()->id,
            'source_id'    => Source::first()->id,
            'slug'         => 'this_article_about_peru_is_amazing',
            'description'  => 'Lorem ipsum ...',
            'url'          => 'https://twitter_not_x.com/articles/this_article_about_peru_is_amazing',
            'keywords'     => 'peru',
            'section'      => 'popular-news',
            'type'         => 'news',
            'media'        => [],
            'published_at' => date("Y-m-d"),
            ]
        );
        Article::create([
                'title'        => 'This article about Spain is amazing',
                'author_id'    => Author::first()->id,
                'category_id'  => Category::first()->id,
                'source_id'    => Source::first()->id,
                'slug'         => 'this_article_about_spain_is_amazing',
                'description'  => 'Lorem ipsum ...',
                'url'          => 'https://twitter_not_x.com/articles/this_article_about_spain_is_amazing',
                'keywords'     => 'spain',
                'section'      => 'popular-news',
                'type'         => 'news',
                'media'        => [],
                'published_at' => date("Y-m-d"),
            ]
        );
    }
}
