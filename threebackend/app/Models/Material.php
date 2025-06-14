<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Material extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'materials';

    protected $fillable = [
        'kursus_id', 
        'judul_materi',
        'deskripsi_materi',
        'video_url',
        'document', 
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id', '_id'); // jika pakai MongoDB
    }

}
