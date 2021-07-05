<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracks extends Model
{
    protected $table = 'audio_tracks';
    protected $primaryKey = 'id';


    public function Timing()
    {
        return $this->hasMany(Timing::class,'audio_tracks_id','id');
    }

    public function reciter()
    {
        return $this->belongsTo(Reciters::class,'audio_reciters_id','id');
    }
}
