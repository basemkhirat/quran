<?php

namespace App\Models;

use App\Http\Controllers\BookController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transes extends Model
{
    protected $table = 'transes';
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * get transes caption
     */
    public function caption()
    {
        return $this->belongsTo(Caption::class, 'key', 'key');
    }

    /**
     * @param $keys
     * @param $sura_number
     * @param $aya_number
     * @return array
     */
    public static function getTransByKeys($keys, $sura_number, $aya_number)
    {
        $out = [];
        $ayah = '';
        $other = [];
        $keyArr = explode(',', $keys);

        foreach ($keyArr as $key) {
            $other[$key.'_footnotes'] = (object)[];
            $other[$key.'_dir'] = 'rtl';
        }

        $books = Book::whereIn('short_name', $keyArr)->where('books_type_id', '=', 2)->get();
        foreach ($books as $book_id) {
            $data = BookContent::where('books_id', '=', $book_id->books_id)->where('surah_id', '=', $sura_number)->where('ayah_from_id', '=', $aya_number)->first();
            $out[$book_id->short_name] = '';
            $ayah = optional(Ayah::where('surah_id', $sura_number)->where('ayah_number', $aya_number)->whereNull('masahef_id')->first())->uthmani_text;

            if ($data) {
                $out[$book_id->short_name] = $data->revised_text;
                $other[$book_id->short_name.'_footnotes'] = (object) (new BookController)->getFootnotes($data->revised_text, $book_id->books_id);
                $other[$book_id->short_name.'_dir'] = $book_id->languages_id == 4 ? 'ltr': 'rtl';
                $other[$book_id->short_name.'_book_id'] = $book_id->books_id;
            }
        }

        return array_merge([
            'sura_number' => $sura_number,
            'aya_number' => $aya_number,
            'aya_text' => $ayah,
            'translate' => $out
        ], $other);
    }
}
