@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
    <h1>Edit User</h1>
    <p class="description">Update {{ $user->full_name }} or upload a profile picture. Fields marked * are required.</p>
    @include('partials.errors')
    <form class="card form-card" method="POST" enctype="multipart/form-data"
          action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('users._form', ['submitLabel' => 'Save changes'])
    </form>
@endsection
