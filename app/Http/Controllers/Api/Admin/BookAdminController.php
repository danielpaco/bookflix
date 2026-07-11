<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Support\ApiResponse;

use App\Http\Resources\BookResource;
use App\Http\Resources\BookListResource;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Jobs\ProcessPdfJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;

use App\Services\BookService;

class BookAdminController extends Controller
{
    protected BookService $bookService;

    public function __construct(
        BookService $bookService
    ){
        $this->bookService = $bookService;
    }

    public function index()
    {
        return ApiResponse::success(
            BookListResource::collection(
                $this->bookService
                    ->list()
            )
        );
    }

    public function show(Book $book)
    {
        return ApiResponse::success(
            new BookResource(
                $this->bookService
                    ->show($book)
            )
        );
    }

    public function store(StoreBookRequest $request)
    {
        $book = $this->bookService
                ->create(
                    $request->validated()
                );

        return ApiResponse::success(
            new BookResource($book),
            'Book processing started',
            201
        );
    }
   

    public function update(UpdateBookRequest $request, Book $book)
    {
        return ApiResponse::success(
            new BookResource(
                $this->bookService
                    ->update(
                        $book,
                        $request->validated()
                    )
            ),
            'Book updated'
        );
    }

    public function destroy(Book $book)
    {
        $this->bookService
            ->delete($book);

        return ApiResponse::success(
            null,
            'Book deleted'
        );
    }
    private function clearBookCache()
    {
        Cache::forget('admin.books');
        Cache::forget('reader.home');
        Cache::forget('reader.new');
        Cache::forget('reader.popular');
        Cache::forget('reader.premium');
        Cache::forget('reader.categories');
        Cache::forget('reader.authors');
    }
}