<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory;

    protected $connection = 'mysql';
    const TABLE_NAME = 'authors';
    const MODULE_NAME = 'authors';
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE = 0;
    
    protected $fillable = [
        //Table Rows
        'id',
        'name',
        //Audit 
        'created_at','updated_at','deleted_at',
    ];

    /** Scopes - Avoid same eloquent where conditions */
    public function scopeDeletedAt($query) {
        return $query->where(self::TABLE_NAME . '.deleted_at');
    }

    /**
     * Casting of attributes
     *
     * @var array
     */
    protected $no_upper = [];
    protected $casts = [];
    protected $table = self::TABLE_NAME;
}
