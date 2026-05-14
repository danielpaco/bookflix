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

Route::post('/register', [
    AuthController::class,
    'register'
]);

Route::post('/login', [
    AuthController::class,
    'login'
]);

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

    $content = Storage::disk('minio')->get($path);

    return response($content)
        ->header(
            'Content-Type',
            'application/octet-stream'
        )
        ->header(
            'Cache-Control',
            'no-store, no-cache'
        );

})->middleware('auth:sanctum')
  ->name('page.view');

Route::middleware([
    'auth:sanctum',
    'throttle:120,1'
])->group(function () {

    Route::get(
        '/books',
        [ReaderController::class, 'books']
    );

    Route::get(
        '/books/{book}',
        [ReaderController::class, 'show']
    );

    Route::get(
        '/books/{book}/pages/{page}',
        [ReaderController::class, 'page']
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