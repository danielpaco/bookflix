<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\User;
use ZipArchive;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

use App\Notifications\NewBookNotification;

class GenerateOfflinePackageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $bookId
    ) {
    }

    public function handle(): void
    {
        $book = Book::with([
            'pages' => fn ($q) =>
                $q->orderBy('page_number')
        ])->findOrFail($this->bookId);

        /*
        |--------------------------------------------------------------------------
        | Working folder
        |--------------------------------------------------------------------------
        */

        $folder = storage_path(
            'app/offline/book-' . $book->id
        );

        if (! file_exists($folder)) {
            mkdir(
                $folder,
                0777,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Zip destination
        |--------------------------------------------------------------------------
        */

        $zipPath = storage_path(
            'app/offline/book-' .
            $book->id .
            '.zip'
        );

        if (! file_exists(dirname($zipPath))) {

            mkdir(
                dirname($zipPath),
                0777,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Manifest
        |--------------------------------------------------------------------------
        */

        file_put_contents(
            $folder . '/manifest.json',
            json_encode([
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'pages_count' => $book->pages_count,
            ], JSON_PRETTY_PRINT)
        );

        /*
        |--------------------------------------------------------------------------
        | Encrypted pages
        |--------------------------------------------------------------------------
        */

        foreach ($book->pages as $page) {

            $content = Storage::disk('minio')
                ->get($page->file_path);

            file_put_contents(
                $folder .
                '/page-' .
                str_pad(
                    $page->page_number,
                    3,
                    '0',
                    STR_PAD_LEFT
                ) .
                '.enc',
                $content
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create ZIP
        |--------------------------------------------------------------------------
        */

        $zip = new ZipArchive();

        $result = $zip->open(
            $zipPath,
            ZipArchive::CREATE |
            ZipArchive::OVERWRITE
        );

        if ($result !== true) {

            throw new \Exception(
                'Cannot create zip package'
            );
        }

        foreach (glob($folder . '/*') as $file) {

            $zip->addFile(
                $file,
                basename($file)
            );
        }

        if ($zip->numFiles === 0) {

            $zip->close();

            throw new \Exception(
                'Offline package is empty'
            );
        }

        $zip->close();

        $zipStoragePath =
            "books/{$book->id}/offline.zip";

        $result = Storage::disk('minio')->put(
            $zipStoragePath,
            fopen($zipPath, 'r')
        );

        if (! $result) {
            throw new \Exception('Could not upload offline ZIP');
        }

        /*
        |--------------------------------------------------------------------------
        | Cleanup temporary files
        |--------------------------------------------------------------------------
        */

        foreach (glob($folder . '/*') as $file) {
            unlink($file);
        }

        rmdir($folder);

        $finishedAt = now();

        $seconds = (int) max(
            0,
            round(
                $book->processing_started_at
                    ->diffInSeconds($finishedAt)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Mark ready
        |--------------------------------------------------------------------------
        */
        $book->update([
            'status' => 'ready',
            'offline_package_path' => $zipStoragePath,
            'processing_finished_at' => $finishedAt,
            'processing_seconds' => $seconds,
        ]);

        $pagesDir = storage_path(
            "app/pages/{$book->id}"
        );

        if (is_dir($pagesDir)) {

            foreach (glob($pagesDir.'/*.png') as $file) {
                unlink($file);
            }

            rmdir($pagesDir);
        }

        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        /*
        |--------------------------------------------------------------------------
        | Notify users
        |--------------------------------------------------------------------------
        */

        try {

            User::query()
                ->chunk(100, function ($users) use ($book) {

                    Notification::send(
                        $users,
                        new NewBookNotification($book)
                    );
                });

        } catch (\Throwable $e) {

            \Log::error(
                'Notification failed',
                [
                    'book_id' => $book->id,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    public function failed(\Throwable $e)
    {
        Book::where('id', $this->bookId)
            ->update([
                'status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);
    }
}
