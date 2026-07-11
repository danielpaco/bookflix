<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse;
use App\Services\BookProcessingService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessPdfJob;
use App\Http\Requests\Reading\ProcessBookRequest;

class ProcessController extends Controller
{
    public function __construct(
        private BookProcessingService $processing
    ) {}

    public function process(
        ProcessBookRequest $request
    )
    {
        $this->processing->process(
            $request->book_id,
            $request->pdf_path
        );

        return ApiResponse::success(
            [],
            'Book processing started'
        );
    }
}
