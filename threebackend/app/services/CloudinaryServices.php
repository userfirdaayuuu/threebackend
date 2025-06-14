<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Dokumens; // Pastikan model Image sudah dibuat

class CloudinaryServices
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true],
        ]);
    }

    public function upload($file)
    {
        return $this->cloudinary->uploadApi()->upload($file->getRealPath());
    }

    public function uploadDocument($file, $folder = 'dokumen', $userId = null)
    {
        $originalName = $file->getClientOriginalName();
        $slug = Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '_');
        $extension = $file->getClientOriginalExtension();
        $timestamp = Carbon::now()->format('Ymd_His');

        $publicId = "{$folder}/{$slug}_{$timestamp}";

        $upload = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'resource_type' => 'auto',
                'public_id' => $publicId,
                'format' => $extension,
                'overwrite' => true,
            ]
        );

        // Simpan metadata ke MongoDB
        $dokumen = Dokumens::create([
            'nama_asli' => $originalName,
            'nama_file' => "{$slug}_{$timestamp}.{$extension}",
            'url' => $upload['secure_url'],
            'public_id' => $upload['public_id'],
            'ukuran' => $file->getSize(),
            'tipe' => $file->getMimeType(),
            'uploaded_at' => now(),
            'user_id' => $userId,
        ]);

        return $dokumen;
    }


    public function delete(string $publicId)
    {
        return $this->cloudinary->uploadApi()->destroy($publicId);
    }

}