@extends('layouts.app')
@section('title', 'Create User')
@section('content')
    <h1>Create User</h1>
    <p class="description">Add a user account with a unique username. Fields marked * are required.</p>
    @include('partials.errors')
    <form class="card form-card" method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('users._form', ['submitLabel' => 'Create user'])
    </form>
@endsection
