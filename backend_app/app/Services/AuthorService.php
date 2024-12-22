<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Author;

class AuthorService
{
    public function getAuthor($name) {
        $author = null;
        if (!empty($name)) {
            // Search author in DB.
            $author = Author::deletedAt()
                ->where('name', $name)
                ->first();
            // If author not exists, create new one.
            if (is_null($author)) {
                $author = new Author();
                $author->name = $name;
                $author->save();
            }
        }
        // return author
        return $author;
    }
}