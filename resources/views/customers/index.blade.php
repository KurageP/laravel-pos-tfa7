@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Customers</h1>
            <p class="description">Browse and manage your customer directory and contact details.</p>
        </div>
        <a class="button" href="{{ route('customers.create') }}">Create customer</a>
    </div>

    <section class="card">
        <div class="card-header">
            Customer Directory · {{ $customers->count() }} records
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->full_name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?: '—' }}</td>
                            <td><a class="edit-link" href="{{ route('customers.edit', $customer) }}"
                                   aria-label="Edit {{ $customer->full_name }}">Edit</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No customers found. Create a customer to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
