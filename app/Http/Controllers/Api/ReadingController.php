<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Book;
use App\Models\Bookmark;
use App\Models\BookReaction;
use App\Models\ReadingSession;
use App\Models\UserBookProgress;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReadingController extends Controller
{
    /*public function updateProgress(
        Request $request,
        Book $book
    ) {

        $request->validate([
            'last_page' => 'required|integer|min:1',
            'reading_time_seconds' => 'required|integer|min:1'
        ]);

        $progress = UserBookProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $book->id
            ],
            [
                'last_page' => $request->last_page,
                'pages_read' => $request->last_page,
                'progress_percent' => (
                    $request->last_page / max($book->pages_count, 1)
                ) * 100,
                'reading_time_seconds' => \DB::raw(
                    'reading_time_seconds + ' .
                    (int) $request->reading_time_seconds
                ),
                'last_read_at' => now()
            ]
        );

        return response()->json($progress);
    }*/

    public function updateProgress(Request $request, Book $book)
    {
        $request->validate([
            'last_page' => 'required|integer|min:1',
            'reading_time_seconds' => 'required|integer|min:0',
        ]);

        $totalPages = max($book->pages_count, 1);

        $progressPercent = min(
            100,
            round(($request->last_page / $totalPages) * 100, 2)
        );

        $progress = UserBookProgress::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $book->id,
            ],
            [
                'last_page' => 0,
                'progress_percent' => 0,
                'reading_time_seconds' => 0,
            ]
        );

        $progress->update([
            'last_page' => $request->last_page,
            'progress_percent' => $progressPercent,
            'reading_time_seconds' =>
                $progress->reading_time_seconds +
                $request->reading_time_seconds,
        ]);

        return response()->json([
            'message' => 'Progress updated',
            'data' => $progress->fresh(),
        ]);
    }

    public function bookmark(
        Request $request,
        Book $book
    ) {

        $request->validate([
            'page_number' => 'required|integer|min:1'
        ]);

        $bookmark = Bookmark::firstOrCreate([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'page_number' => $request->page_number
        ]);

        return response()->json($bookmark);
    }

    public function removeBookmark(
        Book $book,
        int $page
    ) {

        Bookmark::where([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'page_number' => $page
        ])->delete();

        return response()->json([
            'message' => 'Bookmark removed'
        ]);
    }

    public function react(
        Request $request,
        Book $book
    ) {

        $request->validate([
            'reaction' => 'required|in:like,dislike'
        ]);

        $reaction = BookReaction::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $book->id
            ],
            [
                'reaction' => $request->reaction
            ]
        );

        return response()->json($reaction);
    }

    public function analytics()
    {
        $userId = auth()->id();

        return response()->json([

            'books_started' =>
                UserBookProgress::where(
                    'user_id',
                    $userId
                )->count(),

            'books_completed' =>
                UserBookProgress::where(
                    'user_id',
                    $userId
                )
                ->where('progress_percent', '>=', 100)
                ->count(),

            'total_reading_time_seconds' =>
                UserBookProgress::where(
                    'user_id',
                    $userId
                )->sum('reading_time_seconds'),

            'total_pages_read' =>
                UserBookProgress::where(
                    'user_id',
                    $userId
                )->sum('pages_read'),
        ]);
    }
}