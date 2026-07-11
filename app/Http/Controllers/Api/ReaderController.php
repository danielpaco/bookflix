<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse;

use App\Http\Resources\BookResource;
use App\Http\Resources\BookListResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\ProgressResource;
use App\Http\Resources\HomeResource;

use App\Models\Book;
use App\Models\Page;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\UserBookProgress;

use App\Services\RecommendationService;
use App\Services\LibraryService;

class ReaderController extends Controller
{
    protected RecommendationService $recommendationService;
    protected LibraryService $libraryService;

    public function __construct(
        LibraryService $libraryService,
        RecommendationService $recommendationService
    )
    {
        $this->libraryService = $libraryService;
        $this->recommendationService = $recommendationService;
    }

    public function books(Request $request)
    {
        return ApiResponse::success(
            BookListResource::collection(
                $this->libraryService
                    ->books($request)
            )
        );
    }

    public function show(Book $book)
    {
        return ApiResponse::success(
            new BookResource(
                $this->libraryService
                    ->show($book)
            )
        );
    }

    public function pages(Book $book)
    {
        return ApiResponse::success(
            PageResource::collection(
                $this->libraryService
                    ->pages($book)
            )
        );
    }

    public function page(Book $book, $pageNumber)
    {
        return ApiResponse::success(
            $this->libraryService
                ->page(
                    $book,
                    $pageNumber,
                    auth()->id()
                )
        );
    }

    public function categories()
    {
        return ApiResponse::success(
            CategoryResource::collection(
                $this->libraryService
                    ->categories()
            )
        );
    }

    public function tags()
    {
        return ApiResponse::success(
            TagResource::collection(
                $this->libraryService
                    ->tags()
            )
        );
    }

    public function authors()
    {
        return ApiResponse::success(
            AuthorResource::collection(
                $this->libraryService
                    ->authors()
            )
        );
    }

    public function continueReading()
    {
        $progress = UserBookProgress::query()
            ->with([
                'book:id,title,author,cover,is_premium'
            ])
            ->where('user_id', auth()->id())
            ->where('progress_percent', '<', 100)
            ->orderByDesc('updated_at')
            ->take(10)
            ->get();

        return ApiResponse::success(
            ProgressResource::collection($progress)
        );
    }

    public function popularBooks()
    {
        return ApiResponse::success(
            BookListResource::collection(
                $this->recommendationService
                    ->popularBooks()
            )
        );
    }

    public function newBooks()
    {
        return ApiResponse::success(
            BookResource::collection(
                $this->recommendationService
                    ->newBooks()
            )
        );
    }

    public function premiumBooks()
    {
        return ApiResponse::success(
            BookResource::collection(
                $this->recommendationService
                    ->premiumBooks()
            )
        );
    }

    public function featuredCategories()
    {
        return ApiResponse::success(
            CategoryResource::collection(
                $this->recommendationService
                    ->featuredCategories()
            )
        );
    }

    public function recommended()
    {
        return ApiResponse::success(
            BookResource::collection(
                $this->recommendationService->recommended(auth()->id())
            )
        );
    }

    public function home()
    {
        return ApiResponse::success(
            new HomeResource(
                $this->recommendationService
                    ->home(auth()->id())
            )
        );
    }
}