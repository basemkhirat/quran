<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Book extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'books_id';
    protected $guarded = ['books_id'];
    public $timestamps = false;

    protected $appends = ["is_favorited"];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * get favorites
     */
    public function favorites()
    {
        return $this->hasMany(BookFavorite::class, 'book_id', 'books_id');
    }

    public function getIsFavoritedAttribute()
    {
        if (auth("api")->check()) {
            $is_favorited = DB::table("books_favorites")->where("book_id", $this->id)
                ->where("user_id", auth("api")->user()->id)
                ->count();

            return $is_favorited > 0 ? true : false;
        }

        return false;
    }
}
