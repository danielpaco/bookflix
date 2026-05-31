<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReadingSession extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'book_id',
        'start_time',
        'end_time',
        'pages_read',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
