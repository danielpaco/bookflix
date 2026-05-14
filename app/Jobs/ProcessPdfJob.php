<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\CryptoService;
use App\Jobs\ProcessPageJob;
use App\Models\Book;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, Queueable;
    
    protected $bookId;
    protected $pdfPath;

    public function __construct($bookId, $pdfPath)
    {
        $this->bookId = $bookId;

        $this->pdfPath = storage_path(
            'app/private/' . $pdfPath
        );
    }

    public function handle()
    {
        $outputDir = storage_path('app/pages/' . $this->bookId);

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        if (!file_exists($this->pdfPath)) {
            throw new \Exception("PDF not found: {$this->pdfPath}");
        }

        $command = sprintf(
            'gs -sDEVICE=pngalpha -o %s/page-%%03d.png -r144 %s',
            escapeshellarg($outputDir),
            escapeshellarg($this->pdfPath)
        );

        \Log::info("RUNNING GS COMMAND");
        \Log::info($command);

        exec($command . ' 2>&1', $output, $resultCode);

        \Log::info(print_r($output, true));
        \Log::info("RESULT CODE: " . $resultCode);

        if ($resultCode !== 0) {
            throw new \Exception(
                "Ghostscript failed: " . implode("\n", $output)
            );
        }

        $pages = glob("$outputDir/*.png");

        if (empty($pages)) {
            throw new \Exception("No PNG pages generated");
        }

        foreach ($pages as $index => $file) {

            \Log::info("Dispatching page: " . $file);

            ProcessPageJob::dispatch(
                $this->bookId,
                $index,
                $file
            )->onQueue('pdf');
        }

        Book::where('id', $this->bookId)
            ->update([
                'status' => 'ready',
                'pages_count' => count($pages)
            ]);

        if (file_exists($this->pdfPath)) {
            unlink($this->pdfPath);
        }
    }
}