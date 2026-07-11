<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\ReaderController;

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ProcessController;

use App\Http\Controllers\Api\Admin\BookAdminController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ReadingController;
use App\Http\Controllers\Api\OfflineController;

Route::post('/register', [
    AuthController::class,
    'register'
]);

Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::post('/logout',[
    AuthController::class,
    'logout'
])->middleware('auth:sanctum');

Route::get('/me',[
    AuthController::class,
    'me'
])->middleware('auth:sanctum');

Route::get('/page', function (Request $request) {

    if (!$request->hasValidSignature()) {
        abort(403, 'Invalid signature');
    }

    if ((int) $request->user !== auth()->id()) {
        abort(403, 'Invalid user');
    }

    $path = $request->get('path');

    if (!Storage::disk('minio')->exists($path)) {
        abort(404);
    }

    $page = \App\Models\Page::where(
        'file_path',
        $path
    )->firstOrFail();

    $content = Storage::disk('minio')->get($path);

    $iv = substr($content, 0, 12);

    $tag = substr($content, 12, 16);

    $ciphertext = substr($content, 28);

    $key = \App\Services\CryptoService::getPageKey(
        $page->book_id,
        $page->page_number
    );

    $image = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        $key,
        0,
        $iv,
        $tag
    );

    if (!$image) {
        abort(500, 'Decrypt failed');
    }

    return response($image)
        ->header('Content-Type', 'image/png')
        ->header('Cache-Control', 'no-store');

})->middleware('auth:sanctum')
  ->name('page.view');

Route::middleware([
    'auth:sanctum',
    'throttle:120,1'
])->group(function () {

    Route::get(
        '/home',
        [ReaderController::class, 'home']
    );

    Route::get(
        '/books',
        [ReaderController::class, 'books']
    );

    Route::get(
        '/categories',
        [ReaderController::class, 'categories']
    );

    Route::get(
        '/tags',
        [ReaderController::class, 'tags']
    );

    Route::get(
        '/authors',
        [ReaderController::class, 'authors']
    );

    Route::get(
        '/books/{book}',
        [ReaderController::class, 'show']
    );

    Route::get(
        '/books/{book}/pages/{page}',
        [ReaderController::class, 'page']
    )->middleware('premium');

    Route::get(
        '/books/{book}/pages',
        [ReaderController::class,'pages']
    );

    Route::post(
        '/books/{book}/progress',
        [ReadingController::class, 'updateProgress']
    );

    Route::post(
        '/books/{book}/bookmark',
        [ReadingController::class, 'bookmark']
    );

    Route::delete(
        '/books/{book}/bookmark/{page}',
        [ReadingController::class, 'removeBookmark']
    );

    Route::post(
        '/books/{book}/reaction',
        [ReadingController::class, 'react']
    );

    Route::get(
        '/analytics',
        [ReadingController::class, 'analytics']
    );

    Route::post(
        '/reading-session',
        [ReadingController::class, 'storeSession']
    );

    Route::get(
        '/continue-reading',
        [ReadingController::class, 'continueReading']
    );

    Route::get(
        '/favorites',
        [ReadingController::class, 'favorites']
    );

    Route::post(
        '/books/{book}/favorite',
        [ReadingController::class, 'favorite']
    );

    Route::delete(
        '/books/{book}/favorite',
        [ReadingController::class, 'removeFavorite']
    );

    Route::get(
        '/popular-books',
        [ReadingController::class, 'popularBooks']
    );

    Route::get(
        '/history',
        [ReadingController::class, 'history']
    );

    Route::get(
        '/notifications',
        function () {
            return auth()
                ->user()
                ->notifications()
                ->latest()
                ->paginate(20);
        }
    );

    Route::get(
        '/books/{book}/offline-package',
        [OfflineController::class, 'package']
    );
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/plans',
        [SubscriptionController::class, 'plans']
    );

    Route::post(
        '/subscribe',
        [SubscriptionController::class, 'subscribe']
    );

    Route::get(
        '/subscription/current',
        [SubscriptionController::class, 'current']
    );
});

Route::prefix('admin')
    ->middleware('auth:sanctum')
    ->group(function () {

        Route::apiResource(
            'books',
            BookAdminController::class
        );
});

Route::post(
    '/process',
    [ProcessController::class, 'process']
)->middleware('auth:sanctum');