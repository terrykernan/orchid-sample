<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Platform\Dashboard;

class Post extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $fillable = [
        'featured_image',
        'title',
        'body',
        'user_id',
        'category_id'
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function featuredImage() {
        return $this->hasOne(Attachment::class, 'id', 'featured_image');
    }
}
