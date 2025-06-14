<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use MongoDB\BSON\ObjectId;

class Useres extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable;
    
    protected $collection = 'useres';
    protected $connection = 'mongodb';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tutor',
        'siswa',
        // 'materi_selesai'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'siswa'   => 'array',
        'tutor'   => 'array',
        // 'kursus'   => 'array',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getKursusSiswaAttribute()
    {
        $kursusIds = [];

         if (isset($this->siswa['kursus']) && is_array($this->siswa['kursus'])) {
            foreach ($this->siswa['kursus'] as $item) {
                // Jika dari MongoDB $oid
                if (is_array($item) && isset($item['$oid'])) {
                    $kursusIds[] = $item['$oid'];
                }
                // Jika plain string
                elseif (is_string($item)) {
                    $kursusIds[] = $item;
                }
            }
        }

        if (!empty($kursusIds)) {
            $objectIds = array_map(fn($id) => new ObjectId($id), $kursusIds);
            return \App\Models\Kursus::whereIn('_id', $objectIds)->get();
        }

        return collect([]);
    }

    
    public function getKursusTutorAttribute()
    {
        $kursusIds = [];

        if (isset($this->tutor['kursus_ajar']) && is_array($this->tutor['kursus_ajar'])) {
            foreach ($this->tutor['kursus_ajar'] as $item) {
                if (is_array($item) && isset($item['$oid'])) {
                    $kursusIds[] = $item['$oid'];
                }
            }
        }

        if (!empty($kursusIds)) {
            $objectIds = array_map(fn($id) => new ObjectId($id), $kursusIds);
            return \App\Models\Kursus::whereIn('_id', $objectIds)->get();
        }

        return collect([]);
    }

    public function kursusDetails()
    {
        return $this->hasMany(KursusDetail::class, 'tutor_id', '_id');
    }

    public function kursus()
    {
        return $this->belongsToMany(Kursus::class, 'kursus');
    }
}
