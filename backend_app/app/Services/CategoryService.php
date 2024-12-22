<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;

class CategoryService
{
    public function getCategory($name) {
        $category = null;
        if (!empty($name)) {
            // Search category in DB.
            $category = Category::deletedAt()
                ->where('name', $name)
                ->first();
            // If category not exists, create new one.
            if (is_null($category)) {
                $category = new category();
                $category->name = $name;
                $category->save();
            }
        }
        // return category
        return $category;
    }

}