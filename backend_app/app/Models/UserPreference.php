<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Auth;

class UserPreference extends Model
{
    /** @use HasFactory<\Database\Factories\UserPreferenceFactory> */
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';

    // I'm using uui so I need to change some properties.
    protected $keyType = 'string';
    public $incrementing = false;
    // Also, need to assign an UUI everytime I create a new preference.
    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    const TABLE_NAME = 'user_preferences';
    const MODULE_NAME = 'user_preferences';
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE = 0;
    
    // preference types
    const TYPE_ARTICLES   = 'articles';
    const TYPE_AUTHORS    = 'authors';
    const TYPE_CATEGORIES = 'categories';
    const TYPE_SOURCES    = 'sources';
    
    protected $fillable = [
        //Table Rows
        'id',
        'user_id',
        'preference_master_id',
        'type',
        'name',
        //Audit 
        'created_at','updated_at','deleted_at',
    ];

    /** 
     * Scopes - Avoid same eloquent where conditions 
     * In this example, I create a scope to filter records by User Id.
    */
    public function scopeOfUser($query) {
        return $query->where(self::TABLE_NAME . '.user_id', Auth::user()->id);
    }

    public function getMasterAttribute() {
        $master_ = null;
        switch ($this->type) {
            case self::TYPE_ARTICLES:
                $master_ = Article::find($this->preference_master_id);
                if ($master_) {
                    $master_->url = '/api/' . self::TYPE_ARTICLES . '/' . $master_->slug;
                }
                break;
            case self::TYPE_AUTHORS:
                $master_ = Author::find($this->preference_master_id);
                if ($master_) {
                    $master_->url = '/api/' . self::TYPE_AUTHORS . '/' . $master_->id;
                }
                break;
            case self::TYPE_CATEGORIES:
                $master_ = Category::find($this->preference_master_id);
                if ($master_) {
                    $master_->url = '/api/' . self::TYPE_CATEGORIES . '/' . $master_->id;
                }
                break;
            case self::TYPE_SOURCES:
                $master_ = Source::find($this->preference_master_id);
                if ($master_) {
                    $master_->url = '/api/' . self::TYPE_SOURCES . '/' . $master_->id;
                }
                break;
            default:
                break;
        }
        return $master_;
    }

    /**
     * Casting of attributes
     *
     * @var array
     */
    protected $appends = ['master'];
    protected $no_upper = [];
    protected $casts = [];
    protected $table = self::TABLE_NAME;
}
