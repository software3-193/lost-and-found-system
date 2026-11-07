<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    public const TYPE_LOST = 'lost';
    public const TYPE_FOUND = 'found';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_RETURNED = 'returned';

    public const CATEGORIES = [
        'إلكترونيات', 'كتب ودفاتر', 'مفاتيح', 'محافظ وبطاقات',
        'ملابس', 'حقائب', 'مجوهرات', 'أخرى',
    ];

    protected $fillable = [
        'user_id', 'type', 'title', 'category', 'description',
        'location', 'item_date', 'status', 'admin_note',
    ];

    protected function casts(): array
    {
        return ['item_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(ClaimRequest::class);
    }
}
