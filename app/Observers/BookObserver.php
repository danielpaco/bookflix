<?php

namespace App\Observers;

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