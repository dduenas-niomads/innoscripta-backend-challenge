<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory, SoftDeletes;

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

    /** 
     * Scopes - Avoid same eloquent where conditions 
     * In this example, I create a scope to query all records where deleted at is null
     * and a second scope to filter records by type.
     * Other option to handle deletedAt field is use SoftDeletes trait to handle soft_delete fields. 
    */
    public function scopeDeletedAt($query) {
        return $query->whereNull(self::TABLE_NAME . '.deleted_at');
    }

    public function scopeOfType($query, $type = null)
    {
        if (!is_null($type)) {
            return $query->where(self::TABLE_NAME . '.type', $type);
        }

        return $query;
    }
    
    /** 
     * By experience, sometimes you may wish to resolve Eloquent models using a column other than id. 
     * To do so, you may specify the column in the route parameter definition:
     * Route::get('/articles/{article:slug}'...
     * But, in this case, I preffer to override the getRouteKeyName method on the Eloquent model. 
     * Avoiding changes in the apiResource on routes/*
    */
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
