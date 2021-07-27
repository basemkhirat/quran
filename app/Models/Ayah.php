<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ayah extends Model
{
    protected $table = 'ayah';
    protected $primaryKey = 'ayah_id';
    protected $guarded = ['ayah_id'];
    public $timestamps = false;
    protected $appends = ["is_favorited", "is_commented"];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * get favorites
     */
    public function favorites()
    {
        return $this->hasMany(AyahFavorite::class, 'ayah_id', 'ayah_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * get favorites
     */
    public function comments()
    {
        return $this->hasMany(AyahComment::class, 'ayah_id', 'ayah_id');
    }

    public function getIsFavoritedAttribute()
    {
        if (auth("api")->check()) {
            $is_favorited = DB::table("ayah_favorites")->where("ayah_id", $this->attributes["id"])
                ->where("user_id", auth("api")->user()->id)
                ->count();

            return $is_favorited > 0 ? true : false;
        }
       
        return false;
    }

    public function getIsCommentedAttribute()
    {
        if (auth("api")->check()) {
            $is_commented = DB::table("ayah_comments")->where("ayah_id", $this->attributes["id"])
                ->where("user_id", auth("api")->user()->id)
                ->count();

            return $is_commented > 0 ? true : false;
        }
       
        return false;
    }
}
