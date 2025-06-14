<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MaterialUser extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'material_user';

    protected $fillable = [
        'useres_id',
        'material_id',
        'selesai',
    ];

    public $timestamps = true;

}
