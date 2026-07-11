<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse;
use App\Services\OfflineService;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class OfflineController extends Controller
{
    public function __construct(
        private OfflineService $offlineService
    ) {}
    
    public function package(Book $book)
    {
        if (
            $book->is_premium &&
            ! auth()->user()->hasPremium()
        ) {

            return ApiResponse::error(
                'Premium subscription required',
                403
            );

        }

        $zip = $this
            ->offlineService
            ->package($book);

        return response()->download(
            $zip,
            "book-{$book->id}.zip"
        );
    }
}
