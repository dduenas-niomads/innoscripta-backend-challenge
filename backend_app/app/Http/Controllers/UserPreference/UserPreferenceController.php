<?php

namespace App\Http\Controllers\UserPreference;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserPreferenceRequest;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Concurrency;
use Auth;

class UserPreferenceController extends Controller
{
    /**
     * Fetch user preferences
     */
    public function index()
    {
        /**
         * I use withoutTrashed function from SoftDeletes trait and
         * ofUser is a local scope to filter by preferences by User.
        */
        $preferences = UserPreference::withoutTrashed()
            ->ofUser()
        // Paginate the results - 10 items per page
            ->paginate(env('ITEMS_PAGINATOR'));

        // Return a collection resource of Preferences.
        return UserPreferenceResource::collection($preferences);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create a new preference
     */
    public function store(StoreUserPreferenceRequest $request)
    {
        //
        $fields = $request->all();
        $fields['user_id'] = Auth::user()->id;
        // Search if preference exists.
        $searchPreference = UserPreference::where(UserPreference::TABLE_NAME . '.user_id', $fields['user_id'])
            ->where(UserPreference::TABLE_NAME . '.preference_master_id', $fields['preference_master_id'])
            ->where(UserPreference::TABLE_NAME . '.type', $fields['type'])
            ->first();

        if ($searchPreference) {
            return response([
                'status'  => 'error',
                'message' => 'You already has this preference.'
            ], 400); 
        }

        // Create new preference. 
        $newPreference = UserPreference::create($fields);
        if (is_null($newPreference->master)) {
            $newPreference->forceDelete();
            return response([
                'status'  => 'error',
                'message' => 'The provided preference_master_id not correspond to any type (Article, Author, Category or Source).'
            ], 400); 
        }

        // Return new preference
        return new UserPreferenceResource($newPreference);
    }

    /**
     * Show news feed
     */
    public function showFeed()
    {
        // Capture User Id
        $userId = Auth::user()->id;

        // Run Concurrency or Caching
        [$articles, $authors, $categories, $sources] = Concurrency::run([
            fn() => self::feedQuery(UserPreference::TYPE_ARTICLES, $userId),
            fn() => self::feedQuery(UserPreference::TYPE_AUTHORS, $userId),
            fn() => self::feedQuery(UserPreference::TYPE_CATEGORIES, $userId),
            fn() => self::feedQuery(UserPreference::TYPE_SOURCES, $userId),
        ]);

        // Return feed
        return [
            'status' => 'ok',
            'data'   => [
                'news'    => $articles,
                'authors' => $authors,
                'categories' => $categories,
                'sources' => $sources,
            ]
        ];
    }

    private static function feedQuery($type, $userId) 
    {
        // Use caching strategy to load results instantly. Persist data for 1 hour.
        $preferences = cache()->remember('preference_feed_' . $type, config('cache.time_in_seconds'), 
            function() use($type, $userId) {
                return UserPreference::withoutTrashed()
                    ->whereUserId($userId)
                    ->whereType($type)
                    ->get();
            });

        return UserPreferenceResource::collection($preferences);
    }

    /**
     * Delete a preference
     */
    public function destroy(UserPreference $userPreference)
    {
        //
        $userPreference->ofUser()->delete();
        
        return response([
            'status'  => 'ok',
            'message' => 'The preference has been deleted.'
        ]); 
    }
}
