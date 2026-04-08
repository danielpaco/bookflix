<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getPages($bookId)
    {
        return Page::where('book_id', $bookId)
            ->orderBy('page_number')
            ->get()
            ->map(function ($page) {

                return [
                    'page' => $page->page_number,
                    'url' => URL::temporarySignedRoute(
                        'page.view',
                        now()->addMinutes(5),
                        ['path' => $page->file_path]
                    )
                ];
            });
    }
}
