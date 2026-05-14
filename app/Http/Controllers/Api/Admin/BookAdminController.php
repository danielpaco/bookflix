<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Jobs\ProcessPdfJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBookRequest;

class BookAdminController extends Controller
{
    public function index()
    {
        return Cache::remember('books:list', 60, function () {
            return Book::latest()->get();
        });
    }

    public function show(Book $book)
    {
        return $book->load('pages');
    }

    public function store(StoreBookRequest $request)
    {
        $pdfPath = $request
            ->file('pdf')
            ->store('pdfs', 'local');

        $coverPath = null;

        if ($request->hasFile('cover')) {

            $coverPath = $request
                ->file('cover')
                ->store('covers');
        }

        $book = Book::create([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'cover' => $coverPath,
            'pdf_path' => $pdfPath,
            'is_premium' => $request->boolean('is_premium'),
            'status' => 'processing'
        ]);

        ProcessPdfJob::dispatch(
            $book->id,
            $pdfPath
        )->onQueue('pdf');

        return response()->json([
            'message' => 'Book processing started',
            'book' => $book
        ]);
    }
   

    public function update(Request $request, Book $book)
    {
        $book->update($request->only([
            'title',
            'description',
            'author',
            'is_premium'
        ]));

        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        Storage::delete($book->pdf_path);

        if ($book->cover) {
            Storage::delete($book->cover);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted'
        ]);
    }
}