<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ayat2 extends Model
{

    protected $table = "new_font";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'words','font','page','id'
    ];

    /**
     * @var array
     */
    protected $appends = ['glyphs'];

    /**
     * @return string
     */
    public function getGlyphsAttribute() {
        return explode(' ',$this->words);
    }

    /**
     * @param $page_number
     * @return mixed
     */
    public static function getAyat2ByPageNumber($page_number)
    {
        $results = Ayat2::where('page', $page_number)->get();
        return $results;
    }
}





