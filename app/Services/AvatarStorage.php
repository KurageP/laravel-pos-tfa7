<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\ImageManager;

class AvatarStorage
{
    public function store(UploadedFile $upload): string
    {
        $manager = new ImageManager(new Driver(), decodeAnimation: false);

        try {
            // Decode actual image contents, center-crop, and re-encode a thumbnail.
            // A generated name and PNG output avoid trusting the original filename.
            $thumbnail = $manager->read($upload->getRealPath())
                ->cover(256, 256)
                ->toPng();
        } catch (DecoderException $exception) {
            throw ValidationException::withMessages([
                'avatar' => 'This image could not be processed. Please choose another image.',
            ]);
        }

        $path = 'avatars/'.Str::uuid().'.png';

        if (! Storage::disk('public')->put($path, (string) $thumbnail)) {
            throw ValidationException::withMessages([
                'avatar' => 'The profile picture could not be saved. Please try again.',
            ]);
        }

        return $path;
    }

    public function delete(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
