<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\Admin\BookAdminController;
use App\Http\Controllers\Api\Admin\UploadController;
use App\Http\Controllers\Api\ProcessController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/page', function (Request $request) {

    if (!$request->hasValidSignature()) {
        abort(403);
    }

    $path = $request->get('path');

    return Storage::disk('minio')->get($path);
})->name('page.view');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*Route::middleware('auth:sanctum')->group(function () {

    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    Route::get('/books/{id}/pages', [PageController::class, 'getPages']);
});*/

Route::prefix('admin')->group(function () {

    Route::get('/books', [BookAdminController::class, 'index']);
    Route::post('/books', [BookAdminController::class, 'store']);
    Route::get('/books/{id}', [BookAdminController::class, 'show']);
    Route::put('/books/{id}', [BookAdminController::class, 'update']);
    Route::delete('/books/{id}', [BookAdminController::class, 'destroy']);

    Route::post('/upload', [UploadController::class, 'uploadPdf']);
    Route::post('/process', [ProcessController::class, 'process']);
});

Route::middleware('throttle:60,1');