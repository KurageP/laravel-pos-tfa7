@extends('layouts.app')
@section('title', 'Edit Customer')
@section('content')
    <h1>Edit Customer</h1>
    <p class="description">Update the details for {{ $customer->full_name }}. Fields marked * are required.</p>
    @include('partials.errors')
    <form class="card form-card" method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')
        @include('customers._form', ['submitLabel' => 'Save changes'])
    </form>
@endsection
