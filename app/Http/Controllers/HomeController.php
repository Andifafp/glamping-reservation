<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\RoomType;
use Illuminate\View\View;

/**
 * Beranda / landing page. Penanggung jawab: Fais.
 */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->withCount(['roomTypes as rooms_count' => fn ($q) => $q->active()])
            ->withMin(['roomTypes as min_price' => fn ($q) => $q->active()], 'price_per_night')
            ->with(['roomTypes' => fn ($q) => $q->active()->orderBy('price_per_night')])
            ->orderBy('id')
            ->get()
            ->each(function (Category $category) {
                // Satu kamar contoh untuk gambar latar kartu kategori
                $sample = $category->roomTypes->first()?->setRelation('category', $category);
                $category->setRelation('sample_room', $sample);
            });

        $featuredRooms = RoomType::active()
            ->with('category')
            ->orderBy('id')
            ->take(4)
            ->get();

        $articles = Article::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('home', compact('categories', 'featuredRooms', 'articles'));
    }
}
