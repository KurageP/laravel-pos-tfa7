@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="page-heading">
        <div>
            <h1>User Accounts</h1>
            <p class="description">Manage your POS users and their profile pictures.</p>
        </div>
        <a class="button" href="{{ route('users.create') }}">Create user</a>
    </div>

    <section class="card">
        <div class="card-header">
            User Directory · {{ $users->count() }} records
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Avatar</th>
                        <th scope="col">Username</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><img class="avatar" src="{{ $user->avatarUrl() }}"
                                     alt="Profile picture for {{ $user->full_name }}" width="48" height="48"></td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->full_name }}</td>
                            <td><a class="edit-link" href="{{ route('users.edit', $user) }}"
                                   aria-label="Edit {{ $user->full_name }}">Edit</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No users found. Create a user to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
