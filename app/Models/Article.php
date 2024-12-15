<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'author', 'source', 'category', 'url', 'published_at'
    ];
}
