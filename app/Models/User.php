<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'full_name',
        'avatar',
    ];

    public function avatarUrl(): string
    {
        return $this->avatar
            ? Storage::disk('public')->url($this->avatar)
            : asset('images/avatar-placeholder.svg');
    }
}
