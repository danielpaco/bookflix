<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved($page)
    {
        Cache::forget("book:{$page->book_id}:pages");
    }

    public function deleted($page)
    {
        Cache::forget("book:{$page->book_id}:pages");
    }
}