<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // Change data attribute name for body
    public static $wrap = 'body';
    public static $mode = 'article';
    // Set current mode for this resource
    public static function setMode($mode) : string {
        self::$mode = $mode;
        return __CLASS__;
    }
    // Data resource structure.
    public function toArray(Request $request): array
    {   
        return [
            'slug' => $this->slug,
            // Category, Author and Source are display as an objects...
            // But, depends on the requirement, they could be display as a Strings too.
            'author'   => AuthorResource::make($this->author),
            'category' => CategoryResource::make($this->category),
            'source'   => SourceResource::make($this->source),
            // Source as text convert the Source object into text
            // 'source_as_text' => $this->source->implode('name', ', '),
            'title' => $this->title,
            // Description attribute only appears when the request is retriving a single article (show method).
            'description' => $this->when(self::$mode === 'article', $this->description),
            'url' => $this->url,
            'keywords' => $this->keywords,
            'section' => $this->section,
            'type' => $this->type,
            'media' => $this->media,
            'published_at' => $this->published_at
        ];
    }
    // Add status attribute as Ok when the request is ok.
    public function with($request)
    {
        return [ 'status' => 'ok' ];
    }
}
