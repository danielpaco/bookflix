<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'page_number',
        'file_path'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
