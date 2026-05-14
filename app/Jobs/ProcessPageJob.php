<?php

namespace App\Jobs;

use Exception;
use App\Models\Page;
use App\Services\CryptoService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class ProcessPageJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 1;

    protected $bookId;
    protected $index;
    protected $file;

    public function __construct($bookId, $index, $file)
    {
        $this->bookId = $bookId;
        $this->index = $index;
        $this->file = $file;
    }

    public function handle()
    {
        try {
            Log::info("START PAGE JOB");

            if (!file_exists($this->file)) {
                throw new Exception("PNG file not found: {$this->file}");
            }

            $content = file_get_contents($this->file);

            if (!$content) {
                throw new Exception("Could not read PNG");
            }

            Log::info("PNG LOADED");

            $key = CryptoService::getPageKey(
                $this->bookId,
                $this->index
            );

            $iv = random_bytes(12);

            $encrypted = openssl_encrypt(
                $content,
                'aes-256-gcm',
                $key,
                0,
                $iv,
                $tag
            );

            if (!$encrypted) {
                throw new Exception("Encryption failed");
            }

            Log::info("ENCRYPTION OK");

            $path = "books/{$this->bookId}/page_{$this->index}.enc";

            $result = Storage::disk('minio')->put(
                $path,
                $iv . $tag . $encrypted
            );

            if (!$result) {
                throw new Exception("MinIO upload failed");
            }

            Log::info("UPLOAD OK");

            Page::create([
                'book_id' => $this->bookId,
                'page_number' => $this->index,
                'file_path' => $path
            ]);

            Log::info("DB SAVE OK");
        } catch (\Exception $e) {

            \Log::error($e->getMessage());

            throw $e;
        }

        // unlink($this->file);
    }
}