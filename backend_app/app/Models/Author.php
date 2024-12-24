<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory, SoftDeletes;

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

    /**
     * Casting of attributes
     *
     * @var array
     */
    protected $no_upper = [];
    protected $casts = [];
    protected $table = self::TABLE_NAME;
}
