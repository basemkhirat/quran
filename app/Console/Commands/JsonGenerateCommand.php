<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $this->info('Generating sura.json');

        $sura_rows = json_decode(file_get_contents(database_path("json/sura.json")));

        $sura_rows = array_map(function ($sura) {

            $first_aya = DB::table("ayah")->orderBy("ayah_number", "asc")
                ->where("surah_id", $sura->number)->first();

            $sura->hizb = $first_aya->hizb;
            $sura->part = $first_aya->part;

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
