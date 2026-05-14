<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'is_premium'
    ];

    protected $casts = [
        'is_premium' => 'boolean'
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}