<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class ReaderController extends Controller
{
    public function books()
    {
        return Book::where('status', 'ready')
            ->select([
                'id',
                'title',
                'author',
                'cover',
                'pages_count',
                'is_premium'
            ])
            ->latest()
            ->paginate(20);
    }

    public function show(Book $book)
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'description' => $book->description,
            'pages_count' => $book->pages_count,
            'cover' => $book->cover,
            'is_premium' => $book->is_premium
        ];
    }

    public function page(Book $book, $pageNumber)
    {
        $page = Page::where('book_id', $book->id)
            ->where('page_number', $pageNumber)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | FUTURO:
        |--------------------------------------------------------------------------
        |
        | Aquí validarás:
        |
        | - suscripción activa
        | - compra del libro
        | - límites offline
        | - bans
        |
        */
        if ($book->is_premium) {

            if (!auth()->user()->hasActiveSubscription()) {

                abort(403, 'Premium subscription required');
            }
        }

        $signedUrl = URL::temporarySignedRoute(
            'page.view',
            now()->addMinutes(5),
            [
                'path' => $page->file_path,
                'user' => auth()->id()
            ]
        );

        return [
            'page' => $pageNumber,
            'url' => $signedUrl
        ];
    }
}