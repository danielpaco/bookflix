<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Bookmark;
use App\Models\Favorite;
use App\Models\BookReaction;
use App\Models\ReadingSession;
use App\Models\UserBookProgress;

class ReadingService
{
    public function updateProgress(
        User $user,
        Book $book,
        array $data
    ): UserBookProgress
    {
        $totalPages = max($book->pages_count, 1);

        $progressPercent = min(
            100,
            round(
                ($data['last_page'] / $totalPages) * 100,
                2
            )
        );

        $progress = UserBookProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ],
            [
                'last_page' => 0,
                'progress_percent' => 0,
                'reading_time_seconds' => 0,
            ]
        );

        $progress->update([
            'last_page' => $data['last_page'],
            'progress_percent' => $progressPercent,
            'reading_time_seconds' =>
                $progress->reading_time_seconds +
                $data['reading_time_seconds'],
        ]);

        return $progress->fresh();
    }

    public function bookmark(
        User $user,
        Book $book,
        int $page
    ): Bookmark
    {
        return Bookmark::firstOrCreate([

            'user_id'=>$user->id,

            'book_id'=>$book->id,

            'page_number'=>$page

        ]);
    }

    public function removeBookmark(
        User $user,
        Book $book,
        int $page
    ): void
    {
        Bookmark::where([

            'user_id'=>$user->id,

            'book_id'=>$book->id,

            'page_number'=>$page

        ])->delete();
    }

    public function react(
        User $user,
        Book $book,
        string $reaction
    ): BookReaction
    {
        return BookReaction::updateOrCreate(
            [
                'user_id'=>$user->id,
                'book_id'=>$book->id
            ],
            [
                'reaction'=>$reaction
            ]
        );
    }

    public function favorite(
        User $user,
        Book $book
    ): Favorite
    {
        return Favorite::firstOrCreate([
            'user_id'=>$user->id,
            'book_id'=>$book->id
        ]);
    }

    public function removeFavorite(
        User $user,
        Book $book
    ): void
    {
        Favorite::where([
            'user_id'=>$user->id,
            'book_id'=>$book->id
        ])->delete();
    }

    public function storeSession(
        User $user,
        array $data
    ): ReadingSession
    {
        return ReadingSession::create([
            'user_id'=>$user->id,
            'book_id'=>$data['book_id'],
            'start_page'=>$data['start_page'],
            'end_page'=>$data['end_page'],
            'duration_seconds'=>$data['duration_seconds']
        ]);
    }
}