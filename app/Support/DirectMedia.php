<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DirectMedia
{
    public static function attach(Model $model, UploadedFile $file, string $collection): string
    {
        $path = $file->store($collection, 'local');
        $media = $model->media()->create([
            'collection' => $collection,
            'disk' => 'local',
            'path' => $path,
            'filename' => basename($file->getClientOriginalName()),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        $url = route('content.media.show', $media);
        $alt = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->replace(['[', ']'], '')->limit(80, '')->toString();

        return str_starts_with((string) $file->getMimeType(), 'image/')
            ? "\n\n![{$alt}]({$url})"
            : "\n\n[video:{$url}]";
    }

    public static function remove(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
    }
}
