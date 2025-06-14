<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class KursusDetail extends Model
{
    protected $collection = 'kursuses_detail';
    protected $connection = 'mongodb';

    protected $fillable = [
        'kursus_id',
        'deskripsi',
        'durasi',
        'modul',
        'jadwal',
        'tutor_id',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function tutor()
    {
        return $this->belongsTo(Useres::class, 'tutor_id', '_id');
    }

}
