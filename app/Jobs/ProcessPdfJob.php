<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPdfJob implements ShouldQueue
{
    protected $bookId;
    protected $pdfPath;

    public function __construct($bookId, $pdfPath)
    {
        $this->bookId = $bookId;
        $this->pdfPath = storage_path('app/' . $pdfPath);
    }

    public function handle()
    {
        $outputDir = storage_path('app/pages/' . $this->bookId);

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        exec("gs -sDEVICE=pngalpha -o $outputDir/page-%03d.png -r144 {$this->pdfPath}");

        foreach (glob("$outputDir/*.png") as $index => $file) {

            $content = file_get_contents($file);

            $key = hash('sha256', config('app.key'), true);
            $iv = random_bytes(12);

            $encrypted = openssl_encrypt(
                $content,
                'aes-256-gcm',
                $key,
                0,
                $iv,
                $tag
            );

            $path = "books/{$this->bookId}/page_$index.enc";

            Storage::disk('minio')->put($path, $iv . $tag . $encrypted);

            Page::create([
                'book_id' => $this->bookId,
                'page_number' => $index,
                'file_path' => $path
            ]);
        }
    }
}