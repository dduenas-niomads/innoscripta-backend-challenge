<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected $connection = 'mysql';
    const TABLE_NAME = 'articles';
    const MODULE_NAME = 'articles';
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE = 0;
    
    protected $fillable = [
        //Table Rows
        'id',
        'category_id',
        'author_id',
        'source_id',
        'title',
        'slug',
        'description',
        'url',
        'keywords',
        'section',
        'type',
        'media',
        'published_at',
        //Audit 
        'flag_active','created_at','updated_at','deleted_at',
    ];

    /** Scopes - Avoid same eloquent where conditions */
    public function scopeDeletedAt($query) {
        return $query->where(self::TABLE_NAME . '.deleted_at');
    }
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Casting of attributes
     *
     * @var array
     */
    protected $no_upper = [];
    protected $casts = [
        'media' => 'array'
    ];
    protected $table = self::TABLE_NAME;
}
