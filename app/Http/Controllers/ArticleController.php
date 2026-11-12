<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

/**
 * Artikel untuk pengunjung (hanya yang sudah terbit). Penanggung jawab: Fais.
 * CRUD artikel untuk staff dibuat terpisah di dashboard.
 */
class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::published()
            ->latest('published_at')
            ->paginate(9);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        abort_unless($article->is_published && $article->published_at?->isPast(), 404);

        $article->load('author');

        $others = Article::published()
            ->whereKeyNot($article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('articles.show', compact('article', 'others'));
    }
}
