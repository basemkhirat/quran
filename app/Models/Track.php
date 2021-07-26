<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Track extends Model
{
    protected $table = 'audio_tracks';
    protected $primaryKey = 'id';
    protected $appends = ["is_favorited"];


    public function Timing()
    {
        return $this->hasMany(Timing::class,'audio_tracks_id','id');
    }

    public function reciter()
    {
        return $this->belongsTo(Reciters::class,'audio_reciters_id','id');
    }

    public function sura()
    {
        return $this->belongsTo(Surah::class,'surah_id','surah_id');
    }

    public function getIsFavoritedAttribute()
    {
        if (auth("api")->check()) {
            $is_favorited = DB::table("audio_tracks_favorites")->where("track_id", $this->id)
                ->where("user_id", auth("api")->user()->id)
                ->count();

            return $is_favorited > 0 ? true : false;
        }

        return false;
    }
}
