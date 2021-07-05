<?php

namespace App\Models;

use App\Http\Controllers\BookController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tafsir extends Model
{

    protected $table = "quran_text";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * Get tafsir from other tables corresponding tables by $keys parameter
     *
     * @param $keys
     * @param $sura_number
     * @param $aya_number
     * @return array
     */
    public static function getTafsirByKeys($keys, $sura_number, $aya_number)
    {
        $out = [];
        $ayah = '';
        $other = [];

        $keyArr = explode(',', $keys);

        foreach ($keyArr as $key) {
            $other[$key.'_footnotes'] = (object)[];
            $other[$key.'_dir'] = 'rtl';
        }

        $books = Book::whereIn('short_name', $keyArr)->where('books_type_id', '=', 1)->get();

        foreach ($books as $book_id) {
            $data = BookContent::where('books_id', '=', $book_id->books_id)->where('surah_id', '=', $sura_number)->where('ayah_from_id', '=', $aya_number)->first();
            $out[$book_id->short_name] = '';

            if ($data) {
                $out[$book_id->short_name] = $data->revised_text;
                $other[$book_id->short_name.'_footnotes'] = (object) (new BookController)->getFootnotes($data->revised_text, $book_id->books_id);
                $other[$book_id->short_name.'_dir'] = $book_id->languages_id == 4 ? 'ltr': 'rtl';
                $other[$book_id->short_name.'_book_id'] = $book_id->books_id;
            }

            $ayah = optional(Ayah::where('surah_id', $sura_number)->where('ayah_number', $aya_number)->whereNull('masahef_id')->first())->uthmani_text;
        }

        return array_merge([
            'sura_number' => $sura_number,
            'aya_number' => $sura_number,
            'text' => $ayah,
            'tafsir' => $out
        ], $other);

    }

    /**
     * Get all aya definitions (tafasir) based on sura_number
     *
     * @param $keys
     * @param $sura_number
     * @return array
     */
    public static function getPageTafsir($keys, $sura_number)
    {

        $keys_array = explode(',', $keys);
        $out = [];
        $body = [];

        foreach ($keys_array as $key) {
            $metadata = DB::table('tafasir')
                ->select('caption')
                ->where('key', '=', $key)
                ->get()->toArray();

            $out[$key] = $metadata[0]->caption;
        }

        foreach ($keys_array as $key) {
            $data = DB::table("$key")
                ->where('sura', $sura_number)
                ->get()->toArray();

            foreach ($data as $row){
                $body[$sura_number . '_' . $row->aya][$key] = $row->nass;
            }

        }

        $results = [
            'meta' => $out,
            'body' => $body
        ];

        return $results;

    }


}
