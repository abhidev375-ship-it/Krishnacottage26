<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_album_id',
        'image_url',
        'title',
        'alt_text',
        'description',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
    }
}
