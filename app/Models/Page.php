<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'title',
        'slug',
        'short_description',
        'hero_image_url',
        'content_blocks',
        'meta_title',
        'meta_description',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content_blocks' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
