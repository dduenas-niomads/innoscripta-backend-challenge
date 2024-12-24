<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Source;

class SourceService
{
    public static function getSource($name) {
        $source = null;
        if (!empty($name)) {
            // Search source in DB.
            $source = Source::withoutTrashed()
                ->where('name', $name)
                ->first();
            // If source not exists, create new one.
            if (is_null($source)) {
                $source = new Source();
                $source->name = $name;
                $source->save();
            }
        }
        // return source
        return $source;
    }

}