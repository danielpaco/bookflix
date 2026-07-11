<?php

namespace App\Services;

use App\Models\Book;

class OfflineService
{
    public function package(Book $book): string
    {
        $zip = storage_path(
            "app/offline/book-{$book->id}.zip"
        );

        if (! file_exists($zip)) {

            abort(
                409,
                'Offline package not ready'
            );

        }

        return $zip;
    }
}