<?php

namespace App\Http\Controllers\Api\Admin;

use App\Support\ApiResponse;
use App\Services\UploadService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UploadPdfRequest;

class UploadController extends Controller
{
    public function __construct(
        private UploadService $uploadService
    ) {}

    public function uploadPdf(
        UploadPdfRequest $request
    )
    {
        $path = $this
            ->uploadService
            ->uploadPdf(
                $request->file('file')
            );

        return ApiResponse::success([
            'path'=>$path
        ]);
    }
}