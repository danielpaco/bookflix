<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadPdf(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'file' => 'required|mimes:pdf'
        ]);

        $path = $request->file('file')->store('pdfs');

        return response()->json([
            'path' => $path
        ]);
    }
}