<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PageController;

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

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    Route::get('/books/{id}/pages', [PageController::class, 'getPages']);
});

Route::middleware('throttle:60,1');