<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/articles",
     *     summary="Get a list of articles",
     *     description="Retrieve a list of articles with optional filters for keyword, date, category, and source. Results are paginated.",
     *     operationId="getArticles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Search for articles by keyword in the title",
     *         required=false,
     *         @OA\Schema(type="string", example="euro")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter articles by publication date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-12-10")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter articles by category",
     *         required=false,
     *         @OA\Schema(type="string", example="sports")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter articles by source",
     *         required=false,
     *         @OA\Schema(type="string", example="New York Times")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Breaking News: Euro Hits All-Time High"),
     *                     @OA\Property(property="content", type="string", example="The Euro reached a record high today."),
     *                     @OA\Property(property="published_at", type="string", format="datetime", example="2024-12-10T10:00:00Z"),
     *                     @OA\Property(property="category", type="string", example="Finance"),
     *                     @OA\Property(property="source", type="string", example="New York Times")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://127.0.0.1:8000/api/articles?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://127.0.0.1:8000/api/articles?page=5"),
     *                 @OA\Property(property="prev", type="string", example=null),
     *                 @OA\Property(property="next", type="string", example="http://127.0.0.1:8000/api/articles?page=2")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/articles"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid parameters")
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        $query = Article::query();

        // Filtering by keyword
        if ($request->has('keyword')) {
            $query->where('title', 'LIKE', '%' . $request->keyword . '%');
        }

        // Filtering by date
        if ($request->has('date')) {
            $query->whereDate('published_at', $request->date);
        }

        // Filtering by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filtering by source
        if ($request->has('source')) {
            $query->where('source', $request->source);
        }


        $articles = $query->paginate(10); // Pagination

        return ArticleResource::collection($articles);
    }

    /**
     * @OA\Get(
     *     path="/articles/{id}",
     *     summary="Get a specific article by ID",
     *     description="Retrieve the details of a specific article by its ID.",
     *     operationId="getArticleById",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the article to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=100),
     *             @OA\Property(property="title", type="string", example="Breaking News: Euro Hits All-Time High"),
     *             @OA\Property(property="content", type="string", example="The Euro reached a record high today."),
     *             @OA\Property(property="published_at", type="string", format="datetime", example="2024-12-10T10:00:00Z"),
     *             @OA\Property(property="category", type="string", example="Finance"),
     *             @OA\Property(property="source", type="string", example="New York Times")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article not found")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return new ArticleResource($article);
    }
}
