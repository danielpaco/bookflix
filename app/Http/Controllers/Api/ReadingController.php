<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Support\ApiResponse;

use App\Http\Resources\BookResource;
use App\Http\Resources\BookmarkResource;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\ProgressResource;
use App\Http\Resources\ReactionResource;
use App\Http\Resources\ReadingSessionResource;

use App\Models\Book;
use App\Models\Bookmark;
use App\Models\BookReaction;
use App\Models\Favorite;
use App\Models\ReadingSession;
use App\Models\UserBookProgress;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Reading\UpdateProgressRequest;
use App\Http\Requests\Reading\BookmarkRequest;
use App\Http\Requests\Reading\ReactionRequest;
use App\Http\Requests\Reading\StoreSessionRequest;

use App\Services\ReadingService;

class ReadingController extends Controller
{
    private ReadingService $readingService;

    public function __construct(
        ReadingService $readingService
    )
    {
        $this->readingService = $readingService;
    }

    public function updateProgress(
        UpdateProgressRequest $request, 
        Book $book
    )
    {
        $progress = $this->readingService
            ->updateProgress(
                auth()->user(),
                $book,
                $request->validated()
            );

        return ApiResponse::success(
            new ProgressResource($progress),
            'Progress updated'
        );
    }

    public function bookmark(
        BookmarkRequest $request,
        Book $book
    )
    {
        $bookmark = $this->readingService
            ->bookmark(
                auth()->user(),
                $book,
                $request->page_number
            );

        return ApiResponse::success(
            $bookmark,
            'Bookmark created'
        );
    }

    public function removeBookmark(
        Book $book,
        int $page
    )
    {
        $this->readingService
            ->removeBookmark(
                auth()->user(),
                $book,
                $page
            );

        return ApiResponse::success(
            null,
            'Bookmark removed'
        );
    }

    public function react(
        ReactionRequest $request,
        Book $book
    )
    {
        $reaction = $this->readingService
            ->react(
                auth()->user(),
                $book,
                $request->reaction
            );

        return ApiResponse::success(
            $reaction,
            'Reaction saved'
        );
    }

    public function favorite(Book $book)
    {
        return ApiResponse::success(
            $this->readingService->favorite(
                auth()->user(),
                $book
            ),
            'Book added to favorites'
        );
    }

    public function removeFavorite(Book $book)
    {
        $this->readingService
            ->removeFavorite(
                auth()->user(),
                $book
            );

        return ApiResponse::success(
            null,
            'Favorite removed'
        );
    }

    public function analytics()
    {
        $userId = auth()->id();

        return ApiResponse::success([

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
                )->sum('last_page'),
        ]);
    }

    public function storeSession(StoreSessionRequest $request)
    {
        $session = $this->readingService
            ->storeSession(
                auth()->user(),
                $request->validated()
            );

        return ApiResponse::success(
            new ReadingSessionResource($session),
            'Reading session stored',
            201
        );
    }

    public function continueReading()
    {
        $progress = UserBookProgress::query()
            ->with('book')
            ->where('user_id',auth()->id())
            ->where('progress_percent','<',100)
            ->latest('updated_at')
            ->take(10)
            ->get();

        return ApiResponse::success(
            ProgressResource::collection($progress)
        );
    }

    public function favorites()
    {
        $favorites = Favorite::query()
            ->with('book')
            ->where('user_id',auth()->id())
            ->latest()
            ->paginate(20);

        return ApiResponse::success(
            FavoriteResource::collection($favorites)
        );
    }

    public function history()
    {
        $history = ReadingSession::query()
            ->with('book')
            ->where('user_id',auth()->id())
            ->latest()
            ->paginate(20);

        return ApiResponse::success(
            ReadingSessionResource::collection($history)
        );
    }

    public function popularBooks()
    {
        $books = Book::query()
            ->withCount('progress')
            ->orderByDesc('progress_count')
            ->take(20)
            ->get();

        return ApiResponse::success(
            BookResource::collection($books)
        );
    }

}