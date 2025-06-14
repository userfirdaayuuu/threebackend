<?php

namespace App\Models;

use Mongodb\Laravel\Eloquent\Model;

class Dokumens extends Model
{
    protected $collection = 'dokumens';

    protected $fillable = [
        'nama_asli', 'nama_file', 'url', 'public_id', 'ukuran', 'tipe', 'uploaded_at', 'tutor_id'
    ];
}