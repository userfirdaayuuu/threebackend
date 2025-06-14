<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class Kursus extends Model
{
    protected $collection = 'kursuses';
    protected $connection = 'mongodb';
    protected $fillable = [
        'namaKursus'
    ];

    public function detail()
    {
        return $this->hasOne(KursusDetail::class, 'kursus_id', '_id');
    }

    public function materials()
    {
    return $this->hasMany(Material::class, 'kursus_id', '_id');
    }

}