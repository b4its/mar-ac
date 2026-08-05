<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicReportMedia
{
    public static function store(UploadedFile $file, string $type, mixed $date, ?Asset $asset, string $caption, int $index): string
    {
        $datePath = rescue(fn () => $date?->format('Y-m-d'), null, report: false) ?: (string) $date ?: now()->toDateString();
        $assetPath = Str::slug($asset?->nama_alat ?: 'aset-tanpa-nama') ?: 'aset-tanpa-nama';
        $departmentPath = Str::slug($asset?->department?->nama_jurusan ?: 'tanpa-jurusan') ?: 'tanpa-jurusan';
        $captionPath = Str::slug($caption) ?: 'gambar';
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $directory = "media/{$type}/{$datePath}/{$assetPath}/{$departmentPath}";
        $filename = 'gambar_'.$captionPath.'_'.($index + 1).'.'.$extension;

        File::ensureDirectoryExists(public_path($directory), 0775);

        $file->move(public_path($directory), $filename);

        return "{$directory}/{$filename}";
    }

    public static function delete(string|array|null $paths): void
    {
        foreach ((array) $paths as $path) {
            if (! $path) {
                continue;
            }

            $publicPath = public_path($path);
            if (File::exists($publicPath)) {
                File::delete($publicPath);

                continue;
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
