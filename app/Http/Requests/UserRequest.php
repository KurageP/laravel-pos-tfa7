<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $uniqueUsername = Rule::unique('users', 'username');

        // Ignore only the route-bound model, never an ID supplied in form input.
        if ($this->route('user') instanceof User) {
            $uniqueUsername->ignore($this->route('user'));
        }

        return [
            'username' => ['required', 'string', 'max:255', $uniqueUsername],
            'full_name' => ['required', 'string', 'max:255'],
            'avatar' => $this->isMethod('POST')
                ? ['prohibited']
                : ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048',
                    'dimensions:max_width=4096,max_height=4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Please enter a username.',
            'username.unique' => 'That username is already taken. Please choose another.',
            'full_name.required' => 'Please enter the user full name.',
            'avatar.image' => 'Please upload a valid image file.',
            'avatar.mimes' => 'The profile picture must be a JPG, PNG, GIF, or WebP image.',
            'avatar.max' => 'The profile picture must be 2 MB or smaller.',
            'avatar.dimensions' => 'The profile picture must be no larger than 4096 by 4096 pixels.',
        ];
    }
}
