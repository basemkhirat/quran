<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timing extends Model
{
    protected $table ='audio_timing';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function tracks()
    {
      return $this->belongsTo(Timing::class,'audio_tracks_id','id');
    }
}
