<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Page;
use App\Models\Bookmark;
use App\Models\BookReaction;
use App\Models\UserBookProgress;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author',
        'cover',
        'pdf_path',
        'pages_count',
        'status',
        'processing_started_at',
        'processing_finished_at',
        'processing_seconds',
        'last_error',
        'offline_package_path',
        'is_premium'
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'processing_started_at' => 'datetime',
        'processing_finished_at' => 'datetime',
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function progress()
    {
        return $this->hasMany(UserBookProgress::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reactions()
    {
        return $this->hasMany(BookReaction::class);
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function favorites()
    {
        return $this->hasMany(
            Favorite::class
        );
    }
}