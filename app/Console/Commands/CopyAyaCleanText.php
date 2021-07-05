<?php

namespace App\Console\Commands;

use App\Models\Ayah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyAyaCleanText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:aya';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy aya clean text from quran_text table to ayah table for searching purpose.';

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
        $this->info('Clean Text copy started....');

        foreach (DB::table('quran_text')->orderBy('id', 'asc')->cursor() as $qt)
            Ayah::where('masahef_id', 2)->where('surah_id', $qt->sura)->where('ayah_number', $qt->aya)->update(['clean_text' => $qt->text]);

        $this->info('Clean text copy done.');
    }
}
