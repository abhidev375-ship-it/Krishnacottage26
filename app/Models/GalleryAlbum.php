<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryAlbum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'title',
        'slug',
        'category',
        'description',
        'cover_image_url',
        'is_featured',
        'is_published',
        'images_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'images_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('sort_order');
    }

    public function getNameAttribute($value): string
    {
        return $value ?: ($this->attributes['title'] ?? 'Resort Album');
    }

    public function getTitleAttribute($value): string
    {
        return $value ?: ($this->attributes['name'] ?? 'Resort Album');
    }
}
