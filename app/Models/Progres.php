<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Progres extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'book_id',
        'last_page',
        'pages_read',
        'progress_percent',
        'reading_time_seconds',
        'last_read_at',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
