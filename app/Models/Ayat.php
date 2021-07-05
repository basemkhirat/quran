<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ayat extends Model
{

    protected $table = "glyph_ayah";
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
        'glyph_id','glyph_ayah_id','page_number','position',
    ];


    /**
     * @param $page_number
     * @return mixed
     */
    public static function getAyatByPageNumber($page_number)
    {
        $results = [];
        $data    = Ayat::where('page_number', $page_number)->groupBy('ayah_number')->get();

        foreach ($data as $row){

            $glyphs  = Ayat::where('page_number', $page_number)
                ->where('ayah_number', $row->ayah_number)
                ->where('sura_number', $row->sura_number)
                ->get()->toArray();

            $glyph_codes = [];
            foreach ($glyphs as $glyph){
                $glyph_codes[] = $glyph['glyph_code'] ;
            }

            $results[] = [
                    'sura'   => $row->sura_number,
                    'ayah'   => $row->ayah_number,
                    'glyphs' => $glyph_codes
                ];
        }

        return $results;
    }
}





