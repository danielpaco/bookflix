<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use App\Models\UserBookProgress;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    public function categories()
    {
        return Cache::remember(
            'reader.categories',
            now()->addHour(),
            fn() =>
            Category::query()
                ->orderBy('name')
                ->get()
        );
    }

    public function tags()
    {
        return Cache::remember(
            'reader.tags',
            now()->addHour(),
            fn() =>
            Tag::query()
                ->orderBy('name')
                ->get()
        );
    }

    public function popularBooks()
    {
        return Cache::remember(
            'reader.popular',
            now()->addMinutes(15),
            fn() =>
            Book::query()
                ->where('status','ready')
                ->withCount('progress')
                ->orderByDesc('progress_count')
                ->take(20)
                ->get()
        );
    }

    public function newBooks()
    {
        return Cache::remember(
            'reader.new',
            now()->addMinutes(15),
            fn() =>
            Book::query()
                ->where('status','ready')
                ->latest()
                ->take(20)
                ->get()
        );
    }

    public function premiumBooks()
    {
        return Cache::remember(
            'reader.premium',
            now()->addMinutes(15),
            fn() =>
            Book::query()
                ->where('status','ready')
                ->where('is_premium',true)
                ->latest()
                ->take(20)
                ->get()
        );
    }

    public function featuredCategories()
    {
        return Cache::remember(
            'reader.featured',
            now()->addMinutes(15),
            fn() =>
            Category::query()
                ->withCount('books')
                ->orderByDesc('books_count')
                ->take(10)
                ->get()
        );
    }

    public function recommended(int $userId)
    {
        $categoryIds = UserBookProgress::query()
            ->where('user_id',$userId)
            ->with('book.categories')
            ->get()
            ->pluck('book.categories')
            ->flatten()
            ->pluck('id')
            ->unique();

        return Book::query()
            ->where('status','ready')
            ->when(
                $categoryIds->isNotEmpty(),
                fn($q)=>$q->whereHas(
                    'categories',
                    fn($sub)=>$sub->whereIn(
                        'categories.id',
                        $categoryIds
                    )
                )
            )
            ->latest()
            ->take(20)
            ->get();
    }

    public function continueReading(int $userId)
    {
        return UserBookProgress::query()
            ->with([
                'book:id,title,author,cover,is_premium,pages_count'
            ])
            ->where('user_id',$userId)
            ->where('progress_percent','<',100)
            ->latest('updated_at')
            ->take(10)
            ->get();
    }

    public function home(int $userId)
    {
        return [
            'continue_reading' =>
                $this->continueReading($userId),

            'popular' =>
                $this->popularBooks(),

            'new' =>
                $this->newBooks(),

            'premium' =>
                $this->premiumBooks(),

            'categories' =>
                $this->featuredCategories(),

            'recommended' =>
                $this->recommended($userId),
        ];
    }
}