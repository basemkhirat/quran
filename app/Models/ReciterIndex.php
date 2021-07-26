<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReciterIndex extends Model
{
    protected $table ='audio_reciters_indexes';
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

    public function translation() {
        return $this->hasOne(ReciterRewayaTranslation::class, "rewaya_id", "rewaya_id");
    }

}
