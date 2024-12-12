<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
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

    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return new ArticleResource($article);
    }
}
