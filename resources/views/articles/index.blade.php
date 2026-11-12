{{--
    Daftar artikel - Fais
    Variabel dari ArticleController@index:
        $articles : Article terbit, paginate(9)
--}}
@extends('layouts.app')

@section('title', 'Artikel')

@section('content')
    <section class="mx-auto max-w-6xl px-4 pt-14 sm:px-6">
        <h1 class="text-4xl sm:text-5xl">Artikel</h1>
        <p class="mt-4 max-w-xl text-palung/70">Tips memilih kamar, barang bawaan, dan cara menikmati glamping tanpa repot.</p>

        <div class="mt-12 grid gap-x-10 gap-y-14 md:grid-cols-3">
            @forelse ($articles as $article)
                @include('partials.article-card', ['article' => $article])
            @empty
                <p class="text-palung/70 md:col-span-3">Belum ada artikel yang terbit.</p>
            @endforelse
        </div>

        <div class="mt-14">
            {{ $articles->links() }}
        </div>
    </section>
@endsection
