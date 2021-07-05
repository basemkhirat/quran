<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class CreateAya extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:aya';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create json file aya';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $obj = [];
        for ($i = 1; $i <= 604; $i++) {

            $data = DB::table('new_font')->where('page', '=', $i)->where('masahef_id', '=', 2)->get();

            $res = [];

            foreach ($data as $val) {
                $res[] = [$val->sura, $val->aya, "{$val->words}"];
            }
            $obj["{$i}"] = $res;
        }


        file_put_contents( base_path('public/ayat.json'),json_encode($obj));
        $this->info('aya file created successfully');

    }
}
