<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPreferencesRequest;
use App\Http\Resources\PersonalizedNewsResource;
use App\Http\Resources\UserPreferenceResource;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function getPreferences(Request $request)
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        return new UserPreferenceResource($preferences);
    }

    public function setPreferences(SetPreferencesRequest $request)
    {
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'sources' => $request->input('sources', []),
                'categories' => $request->input('categories', []),
                'authors' => $request->input('authors', []),
            ]
        );

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $preferences,
        ]);
    }

    public function getPersonalizedNews(Request $request)
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        $query = Article::query();

        // Filter by preferred sources
        if (!empty($preferences->sources)) {
            $query->whereIn('source', $preferences->sources);
        }

        // Filter by preferred categories
        if (!empty($preferences->categories)) {
            $query->whereIn('category', $preferences->categories);
        }

        // Filter by preferred authors
        if (!empty($preferences->authors)) {
            $query->whereIn('author', $preferences->authors);
        }

        // Paginate results
        $articles = $query->paginate(10);

        return PersonalizedNewsResource::collection($articles);
    }

}
