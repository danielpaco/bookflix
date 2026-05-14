<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessPdfJob;

class ProcessController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'book_id' => 'required',
            'pdf_path' => 'required'
        ]);

        ProcessPdfJob::dispatch(
            $request->book_id,
            $request->pdf_path
        )->onQueue('pdf');

        return ['status' => 'processing'];
    }
}
