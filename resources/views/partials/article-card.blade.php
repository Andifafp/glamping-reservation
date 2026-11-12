{{-- Kartu artikel. Parameter: $article (App\Models\Article) --}}
<article class="flex flex-col border-t-2 border-laut pt-5">
    <p class="text-sm text-palung/55">
        <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('d F Y') }}</time>
    </p>
    <h3 class="mt-2 text-xl leading-snug text-palung">
        <a href="{{ route('articles.show', $article) }}" class="hover:text-laut">{{ $article->title }}</a>
    </h3>
    <p class="mt-3 text-sm leading-6 text-palung/70">{{ $article->excerpt }}</p>
    <a href="{{ route('articles.show', $article) }}" class="mt-4 text-sm font-semibold text-laut hover:underline">
        Baca artikel<span class="sr-only">: {{ $article->title }}</span>
    </a>
</article>
