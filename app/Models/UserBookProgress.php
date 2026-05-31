<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBookProgress extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'last_page',
        'reading_time_seconds',
        'progress_percent',
        'completed_at',
    ];
}