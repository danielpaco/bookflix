<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookAdminController extends Controller
{
    public function index()
    {
        return Cache::remember('books:list', 60, function () {
            return Book::latest()->get();
        });
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'title' => 'required',
            'total_pages' => 'required|integer'
        ]);

        return Book::create($request->all());
    }

    public function show($id)
    {
        return Book::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->all());

        return $book;
    }

    public function destroy($id)
    {
        Book::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }
}