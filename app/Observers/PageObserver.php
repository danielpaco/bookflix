<?php

namespace App\Observers;

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