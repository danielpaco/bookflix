<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Book;

class EnsurePremiumAccess
{
    public function handle(
        $request,
        Closure $next
    ) {
        $book = $request->route('book');

        if (
            $book->is_premium &&
            ! $request->user()
                ->hasPremium()
        ) {
            return response()->json([
                'message' =>
                    'Premium subscription required'
            ], 403);
        }

        return $next($request);
    }
}