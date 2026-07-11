<?php

namespace App\Services;

use App\Jobs\ProcessPdfJob;

class BookProcessingService
{
    public function process(
        int $bookId,
        string $pdfPath
    ): void
    {
        $this->processingService->process(
            $bookId,
            $pdfPath
        );
    }
}