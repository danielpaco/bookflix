<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class MediaService
{
    public static function cover(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::disk('minio')
            ->temporaryUrl(
                $path,
                Carbon::now()->addMinutes(10)
            );
    }
}