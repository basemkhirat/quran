<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use stdClass;

class JsonGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Static JSON files for frontend caching';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->info('Generating aya_text_hightlights.json');

        $rows = DB::table("aya_text_highlights")
            ->orderBy("id", "asc")
            ->get();

        $list = [];

        foreach ($rows as $row) {
            $list[] = [
                $row->x1,
                $row->y1,
                $row->x2,
                $row->y2,
                $row->page,
                $row->sura,
                $row->aya,
            ];
        }

        file_put_contents(base_path('public/data/aya_text_hightlights.json'), json_encode($list));

        $this->info('Generating pos.json');

        $ayat = DB::table("ayah")->where("masahef_id", config("main.moshaf_id"))
            ->orderBy("ayah_id", "asc")
            ->get();

        $list = [];

        $i = 1;
        foreach ($ayat as $aya) {
            $list[] = [
                $aya->page_id,
                $aya->surah_id,
                $aya->ayah_number,
                $i
            ];
            $i++;
        }

        file_put_contents(base_path('public/data/pos.json'), json_encode($list));

        $this->info('Generating page.json');

        $pages = [];

        for ($i = 1; $i <= 604; $i++) {

            $part = DB::table("ayah")->where("masahef_id", config("main.moshaf_id"))
                ->where("page_id", $i)
                ->orderBy("part", "desc")
                ->first();

            $hizb = DB::table("ayah")->where("masahef_id", config("main.moshaf_id"))
                ->where("page_id", $i)
                ->orderBy("hizb", "desc")
                ->first();

            $page = DB::table("ayah")->where("masahef_id", config("main.moshaf_id"))
                ->where("page_id", $i)
                ->orderBy("ayah_number", "asc")
                ->first();


            $pages[] = (object) [
                "n" => $page->page_id,
                "p" => $part->part,
                "h" => $hizb->hizb,
                "s" => $page->surah_id,
                "a" => $page->ayah_number,
                "c" => DB::table("ayah")->where("masahef_id", config("main.moshaf_id"))
                    ->where("page_id", $i)
                    ->count()
            ];
        }

        file_put_contents(base_path('public/data/page.json'), json_encode($pages));

        $this->info('Generating aya.json');

        $rows = DB::table("ayah")->where("masahef_id", config("main.moshaf_id"))->get();

        $ayat = [];

        foreach ($rows as $row) {
            $ayat[$row->surah_id . "-" . $row->ayah_number] = $row->uthmani_text;
        }

        file_put_contents(base_path('public/data/aya.json'), json_encode($ayat));

        $this->info('Generating sura.json');

        $sura_rows = json_decode(file_get_contents(database_path("json/sura.json")));

        $sura_rows = array_map(function ($sura) {

            $first_aya = DB::table("ayah")->orderBy("ayah_number", "asc")
                ->where("surah_id", $sura->number)->first();

            $sura->hizb = $first_aya->hizb;
            $sura->part = $first_aya->part;
            $sura->font = str_replace("??? ", "", DB::table("surah")->where("surah_id", $sura->number)->first()->title_uthmani);

            return $sura;
        }, $sura_rows);

        file_put_contents(base_path('public/data/sura.json'), json_encode($sura_rows));

        $this->info('Generating juz.json');

        $juz_rows = json_decode(file_get_contents(database_path("json/juz.json")));

        $juz_rows = array_map(function ($juz) {

            $first_aya = DB::table("ayah")->orderBy("ayah_id", "asc")
                ->where("part", $juz->number)->first();

            $juz->sura = $first_aya->surah_id;
            $juz->hizb = $first_aya->hizb;
            $juz->aya = $first_aya->ayah_number;

            return $juz;
        }, $juz_rows);

        file_put_contents(base_path('public/data/juz.json'), json_encode($juz_rows));

        $this->info('Generating hizb.json');

        $hizb_rows = json_decode(file_get_contents(database_path("json/hizb.json")));

        $hizb_rows = array_map(function ($hizb) {

            $first_aya = DB::table("ayah")->orderBy("ayah_id", "asc")
                ->where("hizb", $hizb->number)->first();

            $hizb->sura = $first_aya->surah_id;
            $hizb->part = $first_aya->part;
            $hizb->aya = $first_aya->ayah_number;

            return $hizb;
        }, $hizb_rows);

        file_put_contents(base_path('public/data/hizb.json'), json_encode($hizb_rows));

        $this->info("All files generated successfully");
    }
}
