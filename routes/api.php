<?php

use Illuminate\Support\Facades\Route;

Route::get('/page', function (Request $request) {

    if (!$request->hasValidSignature()) {
        abort(403);
    }

    if ($request->user != auth()->id()) {
        abort(403);
    }

    return Storage::disk('minio')->get($request->path);

})->middleware('auth:sanctum')->name('page.view');