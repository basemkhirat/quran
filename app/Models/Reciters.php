<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reciters extends Model
{
    protected $table ='audio_reciters';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * get tracks
     */
    public function tracks()
    {
       return $this->hasMany(Tracks::class,'audio_reciters_id','id');
    }
}
