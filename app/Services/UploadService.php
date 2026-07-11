<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class UploadService
{
    public function uploadPdf(
        UploadedFile $file
    ): string
    {
        return $file->store(
            'pdfs',
            'local'
        );
    }

    public function uploadCover(
        UploadedFile $file
    ): string
    {
        return $file->store(
            'covers',
            'public'
        );
    }
}