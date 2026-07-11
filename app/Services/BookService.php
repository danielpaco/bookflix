<?php

namespace App\Services;

use App\Models\Book;
use App\Jobs\ProcessPdfJob;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function create(array $data): Book
    {
        $pdfPath = $data['pdf']->store(
            'pdfs',
            'local'
        );

        $coverPath = null;

        if (isset($data['cover'])) {
            $coverPath = $data['cover']
                ->store('covers');
        }

        $book = Book::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'author' => $data['author'],
            'cover' => $coverPath,
            'pdf_path' => $pdfPath,
            'is_premium' =>
                $data['is_premium'] ?? false,
            'status' => 'processing'

        ]);

        ProcessPdfJob::dispatch(
            $book->id,
            $pdfPath
        )->onQueue('pdf');

        return $book;
    }

    public function update(
        Book $book,
        array $data
    ): Book
    {
        $book->update([
            'title'=>$data['title'],
            'description'=>$data['description'],
            'author'=>$data['author'],
            'is_premium'=>$data['is_premium']
        ]);

        return $book->fresh();
    }

    public function delete(Book $book): void
    {
        Storage::delete($book->pdf_path);
        if($book->cover){
            Storage::delete($book->cover);
        }

        $book->delete();
    }

    public function list()
    {
        return Book::latest()
            ->with([
                'categories',
                'tags'
            ])
            ->paginate(20);
    }

    public function show(Book $book)
    {
        return $book->load([
            'pages',
            'categories',
            'tags'
        ]);
    }
}