<?php

namespace App\Indices;

use Basemkhirat\Elasticsearch\Model;
use App\Models\Aya;


class QuranIndex extends Model
{

    protected $index = "ayat";
    protected $type = "ayat";

    protected $appends = [
        "id"
    ];

    protected $selectable = [
        "id",
        "number",
        "text",
        "sura",
        "hizb",
        "quarter",
        "juz",
        "page",
        "rewaya_id",
        "moshaf_id"
    ];

    protected $unselectable = [];

    protected $hidden = [
        "_index",
        "_type",
        "_id",
        "_score",
        "_highlight"
    ];

    /**
     * ID getter
     * @return int
     */
    public function getIdAttribute()
    {
        return (int)$this->getID();
    }

    /**
     * Name getter
     * @param $name
     * @return |null
     */
    public function getTextAttribute($text)
    {

        if (request()->filled("q")) {
            if (isset($this->_highlight["text"])) {
                return $this->_highlight["text"][0];
            }
        }

        return $text;
    }

    /**
     * ReIndex narrators
     * @param $ids
     * @return mixed
     */
    public static function reIndex($ids)
    {

        $ids = is_array($ids) ? $ids : [$ids];

        $Ayat = Aya::where('masahef_id', config('main.moshaf_id'))
            ->where('rewaya_id', config('main.rewaya_id'))
            ->whereIn("ayah_id", $ids)
            ->get();

        $documents = [];

        foreach ($Ayat as $aya) {

            $quarter = $aya->quarter;

            $row = [
                "id" => $aya->ayah_id,
                "number" => $aya->ayah_number,
                "uthmani_text" => $aya->search_text,
                "text" => $aya->uthmani_text,
                "sura" => $aya->surah_id,
                "quarter" => $aya->quarter_id,
                "hizb" => $quarter->hizb_id,
                "juz" => $quarter->hizb->part_id,
                "page" => (int) $aya->page_id,
                "rewaya_id" => $aya->rewaya_id,
                "moshaf_id" => $aya->masahef_id
            ];

            $documents[$aya->ayah_id] = $row;
        }

        return self::type((new self)->getType())->bulk($documents);
    }
}
