@extends('layouts.dashboard')

@section('title', 'Tambah akun')

@section('content')
    <form method="POST" action="{{ route('dashboard.users.store') }}" class="panel max-w-3xl" novalidate>
        @csrf
        @include('dashboard.users.form', ['user' => $user])
    </form>
@endsection
