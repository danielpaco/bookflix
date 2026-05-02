<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Book;
use App\Models\Page;
use App\Observers\BookObserver;
use App\Observers\PageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Book::observe(BookObserver::class);
        Page::observe(PageObserver::class);
    }
}
