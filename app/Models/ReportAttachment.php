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

    /**
     * Nomor bagian (section) pada kartu pelaporan perawatan.
     * Bagian pertama memakai slot_key tanpa akhiran; bagian kedua memakai akhiran "_2".
     */
    public function getBagianAttribute(): int
    {
        return str_ends_with((string) $this->slot_key, '_2') ? 2 : 1;
    }

    /**
     * Get section number without using Eloquent accessor pattern.
     */
    public function sectionNumber(): int
    {
        return $this->bagian;
    }

    public static function slotLabel(string $slotKey): string
    {
        $key = $slotKey;

        if (str_ends_with($key, '_2')) {
            $key = substr($key, 0, -2);
        }

        if (str_starts_with($key, 'photo_')) {
            return 'Foto Kerusakan '.str_replace('photo_', '', $key);
        }

        return match ($key) {
            'indoor_cleaning' => 'Pencucian AC Indoor',
            'outdoor_cleaning' => 'Pencucian AC Outdoor',
            'maintenance_card' => 'Kartu Perawatan',
            'lampiran_tambahan' => 'Lampiran Tambahan',
            default => $slotKey,
        };
    }
}
