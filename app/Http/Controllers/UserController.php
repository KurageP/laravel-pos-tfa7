<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AvatarStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('id')->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', ['user' => new User]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create($request->safe()->only(['username', 'full_name']));

        return to_route('users.index')->with('success', 'User account created successfully.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user, AvatarStorage $avatars): RedirectResponse
    {
        $data = $request->safe()->only(['username', 'full_name']);
        $previousAvatar = $user->avatar;
        $newAvatar = null;

        if ($request->hasFile('avatar')) {
            $newAvatar = $avatars->store($request->file('avatar'));
            $data['avatar'] = $newAvatar;
        }

        try {
            $user->update($data);
        } catch (\Throwable $exception) {
            // Keep the previous avatar if saving the database record fails.
            if ($newAvatar !== null) {
                $avatars->delete($newAvatar);
            }

            throw $exception;
        }

        if ($newAvatar !== null && $previousAvatar !== null) {
            $avatars->delete($previousAvatar);
        }

        return to_route('users.index')->with('success', 'User account updated successfully.');
    }
}
