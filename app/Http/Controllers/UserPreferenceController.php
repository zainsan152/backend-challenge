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
    /**
     * @OA\Get(
     *     path="/preferences",
     *     summary="Get user preferences",
     *     description="Retrieve the preferences of the authenticated user, including preferred sources, categories, and authors.",
     *     operationId="getUserPreferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="sources", type="array",
     *                     @OA\Items(type="string", example="NewsAPI")
     *                 ),
     *                 @OA\Property(property="categories", type="array",
     *                     @OA\Items(type="string", example="Technology")
     *                 ),
     *                 @OA\Property(property="authors", type="array",
     *                     @OA\Items(type="string", example="John Doe")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2024-12-15T15:07:55.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2024-12-15T15:09:13.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No preferences found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No preferences found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */

    public function getPreferences(Request $request)
    {
        $preferences = UserPreference::where('user_id', $request->user()->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        return new UserPreferenceResource($preferences);
    }

    /**
     * @OA\Post(
     *     path="/preferences",
     *     summary="Set user preferences",
     *     description="Update or create the preferences of the authenticated user, including sources, categories, and authors.",
     *     operationId="setUserPreferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="sources", type="array",
     *                 @OA\Items(type="string", example="NewsAPI")
     *             ),
     *             @OA\Property(property="categories", type="array",
     *                 @OA\Items(type="string", example="Technology")
     *             ),
     *             @OA\Property(property="authors", type="array",
     *                 @OA\Items(type="string", example="Tom Ambrose")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully"),
     *             @OA\Property(property="preferences", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="sources", type="array",
     *                     @OA\Items(type="string", example="NewsAPI")
     *                 ),
     *                 @OA\Property(property="categories", type="array",
     *                     @OA\Items(type="string", example="Technology")
     *                 ),
     *                 @OA\Property(property="authors", type="array",
     *                     @OA\Items(type="string", example="Tom Ambrose")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2024-12-15T15:07:55.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2024-12-15T15:09:13.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */


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

    /**
     * @OA\Get(
     *     path="/personalized-news",
     *     summary="Get personalized news",
     *     description="Retrieve a list of personalized news articles based on the authenticated user's preferences (sources, categories, and authors).",
     *     operationId="getPersonalizedNews",
     *     tags={"Personalized News"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Personalized news articles retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=31),
     *                     @OA\Property(property="title", type="string", example="Marcus Fakana: Briton, 18, sentenced over Dubai sex with girl, 17"),
     *                     @OA\Property(property="description", type="string", example="Marcus Fakana has been jailed for a year over the relationship, a campaign group in Dubai says."),
     *                     @OA\Property(property="author", type="string", example="BBC News"),
     *                     @OA\Property(property="source", type="string", example="BBC News"),
     *                     @OA\Property(property="category", type="string", example="General"),
     *                     @OA\Property(property="url", type="string", format="url", example="https://www.bbc.co.uk/news/articles/cly2zq1yl0ko"),
     *                     @OA\Property(property="published_at", type="string", format="datetime", example="2024-12-11T12:22:21.5477043Z")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://127.0.0.1:8000/api/personalized-news?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://127.0.0.1:8000/api/personalized-news?page=5"),
     *                 @OA\Property(property="prev", type="string", example=null),
     *                 @OA\Property(property="next", type="string", example="http://127.0.0.1:8000/api/personalized-news?page=2")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/personalized-news"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No preferences found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No preferences found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */

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
