{{--
    Detail artikel - Fais
    Variabel dari ArticleController@show:
        $article : Article dengan relasi author
        $others  : artikel lain (maks 3)
    Isi artikel ($article->content) berupa HTML dari staff, ditampilkan dengan {!! !!}.
    Pastikan form CRUD artikel hanya bisa diakses staff/admin.
--}}
@extends('layouts.app')

@section('title', $article->title)
@section('description', $article->excerpt)

@section('content')
    <article class="mx-auto max-w-2xl px-4 pt-14 sm:px-6">
        <a href="{{ route('articles.index') }}" class="text-sm font-semibold text-laut hover:underline">Semua artikel</a>

        <h1 class="mt-6 text-4xl leading-tight sm:text-5xl">{{ $article->title }}</h1>
        <p class="mt-5 text-sm text-palung/60">
            Terbit <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('d F Y') }}</time>@if ($article->author) oleh {{ $article->author->name }}@endif
        </p>

        @if ($article->excerpt)
            <p class="mt-8 border-l-2 border-ombak pl-5 text-lg leading-8 text-palung/80">{{ $article->excerpt }}</p>
        @endif

        <div class="prose-artikel mt-8">
            {!! $article->content !!}
        </div>

        <div class="mt-14 rounded-2xl bg-buih p-6">
            <p class="font-semibold">Siap menginap di tepi laut?</p>
            <a href="{{ route('rooms.index') }}" class="btn btn-primary mt-4">Lihat kamar</a>
        </div>
    </article>

    @if ($others->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 pt-24 sm:px-6" aria-labelledby="judul-lain">
            <h2 id="judul-lain" class="text-3xl">Artikel lainnya</h2>
            <div class="mt-10 grid gap-10 md:grid-cols-3">
                @foreach ($others as $other)
                    @include('partials.article-card', ['article' => $other])
                @endforeach
            </div>
        </section>
    @endif
@endsection
