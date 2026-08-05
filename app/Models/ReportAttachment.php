<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class ReportAttachment extends Model
{
    protected $fillable = [
        'category',
        'slot_key',
        'caption',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'sort_order',
        'uploaded_by_user_id',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function url(): string
    {
        if (str_starts_with($this->file_path, 'media/')) {
            return '/'.ltrim($this->file_path, '/');
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public static function slotLabel(string $slotKey): string
    {
        if (str_starts_with($slotKey, 'photo_')) {
            return 'Foto Kerusakan '.str_replace('photo_', '', $slotKey);
        }

        return match ($slotKey) {
            'indoor_cleaning' => 'Pencucian AC Indoor',
            'outdoor_cleaning' => 'Pencucian AC Outdoor',
            'maintenance_card' => 'Kartu Perawatan',
            default => $slotKey,
        };
    }
}
