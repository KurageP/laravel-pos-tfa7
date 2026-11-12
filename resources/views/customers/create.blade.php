@extends('layouts.app')
@section('title', 'Create Customer')
@section('content')
    <h1>Create Customer</h1>
    <p class="description">Add a customer to your directory. Fields marked * are required.</p>
    @include('partials.errors')
    <form class="card form-card" method="POST" action="{{ route('customers.store') }}">
        @csrf
        @include('customers._form', ['submitLabel' => 'Create customer'])
    </form>
@endsection
