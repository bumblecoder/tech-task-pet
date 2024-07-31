<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 5);
        $articles = Article::paginate($perPage);

        return response()->json($articles->items())
            ->header('X-Total-Count', $articles->total());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $article = Article::create($validated);

        return response()->json($article, 201);
    }

    public function show($id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return response()->json($article, 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $article->update($validated);

        return response()->json($article, 200);
    }

    public function destroy($id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully'], 200);
    }
}
