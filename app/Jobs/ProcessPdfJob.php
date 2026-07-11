<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Jobs\ProcessPageJob;
use App\Models\Book;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Throwable;

use App\Jobs\GenerateOfflinePackageJob;

class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $timeout = 7200;
    public $tries = 3;
    
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

        //\Log::info("RUNNING GS COMMAND");
        //\Log::info($command);

        exec($command . ' 2>&1', $output, $resultCode);

        //\Log::info(print_r($output, true));
        //\Log::info("RESULT CODE: " . $resultCode);

        if ($resultCode !== 0) {
            throw new \Exception(
                "Ghostscript failed: " . implode("\n", $output)
            );
        }

        $pages = glob("$outputDir/*.png");

        if (empty($pages)) {
            throw new \Exception(
                "No PNG pages generated"
            );
        }

        $jobs = [];

        foreach ($pages as $index => $file) {

            $jobs[] = new ProcessPageJob(
                $this->bookId,
                $index,
                $file
            );
        }

        Book::where('id', $this->bookId)
            ->update([
                'pages_count' => count($pages),
                'status' => 'processing',
                'processing_started_at' => now(),
                'last_error' => null,
            ]);

        $bookId = $this->bookId;

        /*\Log::info('CREATING BATCH', [
            'book_id' => $bookId,
            'jobs' => count($jobs),
        ]);*/

        $batch = Bus::batch($jobs)

            ->then(function (Batch $batch) use ($bookId) {

                GenerateOfflinePackageJob::dispatch(
                    $bookId
                )->onQueue('pdf');

            })

            ->catch(function (
                Batch $batch,
                Throwable $e
            ) use ($bookId) {

                Book::where('id', $bookId)
                    ->update([
                        'status' => 'failed'
                    ]);

                \Log::error(
                    'PDF batch failed',
                    [
                        'book_id' => $bookId,
                        'error' => $e->getMessage(),
                    ]
                );
            })

            ->name(
                'Book Processing #'.$bookId
            )

            ->onQueue('pdf')

            ->dispatch();
            
        /*\Log::info('BATCH CREATED', [
            'batch_id' => $batch->id,
        ]);*/

        if (file_exists($this->pdfPath)) {
            unlink($this->pdfPath);
        }
    }

    public function failed(\Throwable $e)
    {
        Book::where('id', $this->bookId)
            ->update([
                'status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);

        \Log::error('PDF PROCESS FAILED', [
            'book_id' => $this->bookId,
            'error' => $e->getMessage(),
        ]);
    }
}