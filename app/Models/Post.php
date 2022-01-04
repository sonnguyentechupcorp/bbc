<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "posts";

    protected $fillable = [
        'title',
        'body',
        'feature_img',
        'author_id',
        'deleted_at',
    ];

    protected $appends = ['is_deleted'];

    protected $casts = [
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function getIsDeletedAttribute()
    {
        return !empty($this->deleted_at);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,  'category_post', 'post_id', 'category_id');
    }
}
