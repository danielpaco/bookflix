<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class BookObserver
{
    public function saved()
    {
        Cache::forget('books:list');
    }

    public function deleted()
    {
        Cache::forget('books:list');
    }
}