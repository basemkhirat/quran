<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ayah extends Model
{
    protected $table = 'ayah';
    protected $primaryKey = 'ayah_id';
    protected $guarded = ['ayah_id'];
    public $timestamps = false;
}
