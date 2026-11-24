<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * CRUD artikel (staff & admin). Penanggung jawab: Fais.
 *
 * Isi artikel boleh teks biasa (baris kosong = paragraf baru) atau HTML sederhana.
 * Hanya tag aman yang disimpan, dan semua atribut dibuang, supaya tidak ada skrip.
 */
class ArticleController extends Controller
{
    private const ALLOWED_TAGS = '<p><h2><h3><ul><ol><li><strong><em><b><i><br><blockquote>';

    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), ['terbit', 'draf'], true) ? $request->query('status') : null;

        $articles = Article::with('author')
            ->when($status === 'terbit', fn ($q) => $q->where('is_published', true))
            ->when($status === 'draf', fn ($q) => $q->where('is_published', false))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.articles.index', compact('articles', 'status'));
    }

    public function create(): View
    {
        return view('dashboard.articles.form', ['article' => new Article(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article = Article::create($data);
        ActivityLog::record('article.created', "Menulis artikel \"{$article->title}\"", $article);

        return redirect()->route('dashboard.articles.index')->with('status', 'Artikel disimpan.');
    }

    public function edit(Article $article): View
    {
        return view('dashboard.articles.form', compact('article'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $this->validated($request, $article);

        if ($data['title'] !== $article->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
        }
        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article->update($data);
        ActivityLog::record('article.updated', "Mengubah artikel \"{$article->title}\"", $article);

        return redirect()->route('dashboard.articles.index')->with('status', 'Artikel diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $title = $article->title;
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        ActivityLog::record('article.deleted', "Menghapus artikel \"{$title}\"", null, ['title' => $title]);
        $article->delete();

        return back()->with('status', 'Artikel dihapus.');
    }

    private function validated(Request $request, ?Article $article = null): array
    {
        $data = $request->validate([
            'title'     => ['required', 'string', 'max:200'],
            'excerpt'   => ['nullable', 'string', 'max:300'],
            'content'   => ['required', 'string', 'min:50'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'content.min'   => 'Isi artikel minimal 50 karakter.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2 MB.',
        ], [
            'title'     => 'judul',
            'excerpt'   => 'ringkasan',
            'content'   => 'isi artikel',
            'thumbnail' => 'gambar',
        ]);

        unset($data['thumbnail']);
        $data['content'] = $this->cleanContent($data['content']);
        $data['is_published'] = $request->boolean('is_published');

        if ($data['is_published'] && ! $article?->published_at) {
            $data['published_at'] = now();
        }

        return $data;
    }

    private function cleanContent(string $content): string
    {
        $content = trim($content);

        // Teks biasa: ubah tiap paragraf menjadi <p>
        if (! preg_match('/<\w+/', $content)) {
            return collect(preg_split('/(\r\n|\n){2,}/', $content))
                ->map(fn ($p) => '<p>' . nl2br(e(trim($p)), false) . '</p>')
                ->implode("\n");
        }

        // HTML: sisakan tag aman saja, buang semua atribut (onclick, style, dll.)
        $content = strip_tags($content, self::ALLOWED_TAGS);

        return preg_replace('/<(\w+)\b[^>]*>/', '<$1>', $content);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug(Str::limit($title, 80, '')) ?: 'artikel';
        $slug = $base;
        for ($i = 2; Article::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
