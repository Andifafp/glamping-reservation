@extends('layouts.dashboard')

@section('title', 'Ubah akun')

@section('content')
    <form method="POST" action="{{ route('dashboard.users.update', $user) }}" class="panel max-w-3xl" novalidate>
        @csrf
        @method('PUT')
        @include('dashboard.users.form', ['user' => $user])
    </form>
@endsection
