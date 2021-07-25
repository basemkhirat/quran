<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ReciterRewaya;
use Illuminate\Support\Facades\DB;

class Reciter extends Model
{
    protected $table = 'audio_reciters';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    protected $appends = ["is_favorited"];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * get tracks
     */
    public function rewayat()
    {
        return $this->hasMany(ReciterRewaya::class, 'reciter_id', 'id');
    }

    public function getIsFavoritedAttribute()
    {
        if (auth("api")->check()) {
            $is_favorited = DB::table("audio_reciters_favorites")->where("reciter_id", $this->id)
                ->where("user_id", auth("api")->user()->id)
                ->count();

            return $is_favorited > 0 ? true : false;
        }

        return false;
    }
}
