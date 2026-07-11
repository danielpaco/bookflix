<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\Page;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class LibraryService
{
    public function books(Request $request)
    {
        $query = Book::query()
            ->with([
                'categories:id,name,slug',
                'tags:id,name,slug'
            ])
            ->where('status','ready')
            ->select([
                'id',
                'title',
                'description',
                'author',
                'cover',
                'pages_count',
                'is_premium',
                'created_at'
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Author
        |--------------------------------------------------------------------------
        */
        if ($request->filled('author')) {
            $query->where(
                'author',
                'like',
                '%' . $request->author . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Premium
        |--------------------------------------------------------------------------
        */
        if ($request->has('premium')) {
            $query->where(
                'is_premium',
                $request->boolean('premium')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */
        if ($request->filled('category')) {
            $query->whereHas(
                'categories',
                function ($q) use ($request) {
                    $q->where(
                        'slug',
                        $request->category
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tag
        |--------------------------------------------------------------------------
        */
        if ($request->filled('tag')) {
            $query->whereHas(
                'tags',
                function ($q) use ($request) {
                    $q->where(
                        'slug',
                        $request->tag
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        switch ($request->get('sort')) {
            case 'popular':
                $query->withCount('progress')
                    ->orderByDesc('progress_count');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        return $query->paginate(
            $request->integer(
                'per_page',
                20
            )
        );
    }

    public function show(Book $book)
    {
        return $book->load(
            'categories',
            'tags'
        );
    }

    public function pages(Book $book)
    {
        return $book
            ->pages()
            ->orderBy('page_number')
            ->get();
    }

    public function page(
        Book $book,
        int $pageNumber,
        int $userId
    )
    {
        $page = Page::where('book_id', $book->id)
            ->where('page_number', $pageNumber)
            ->firstOrFail();

        if ($book->is_premium) {
            if (!auth()->user()->hasPremium()) {
                abort(403, 'Premium subscription required');
            }
        }

        $signedUrl = URL::temporarySignedRoute(
            'page.view',
            now()->addMinutes(5),
            [
                'path' => $page->file_path,
                'user' => auth()->id()
            ]
        );

        return [
            'page'=>$pageNumber,
            'url'=>$signedUrl,
        ];
    }

    public function categories()
    {
        return Cache::remember(
            'reader.categories',
            now()->addHour(),
            fn()=>Category::orderBy('name')->get()
        );
    }

    public function tags()
    {
        return Cache::remember(
            'reader.tags',
            now()->addHour(),
            fn()=>Tag::orderBy('name')->get()
        );
    }

    public function authors()
    {
        return Cache::remember(
            'reader.authors',
            now()->addHour(),
            fn()=>Book::query()
                ->select('author')
                ->distinct()
                ->orderBy('author')
                ->get()
        );
    }
}