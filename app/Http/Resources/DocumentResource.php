<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FileSystemAdapter $disk */
        $disk = Storage::disk('supabase');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'original_hash' => $this->original_hash,
            'file_path' => $this->file_path,
            'file_url' => $disk->url($this->file_path),
            'uploader' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
