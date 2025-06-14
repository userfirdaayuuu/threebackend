<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class modelCloudinary extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'dokumen';

    protected $fillable = [
        'nama_file',
        'url',
        'public_id',
        'uploaded_by',
    ];
}
