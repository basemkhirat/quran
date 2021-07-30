<?php

namespace App\Console;

use App\Console\Commands\CopyAyaCleanText;
use App\Console\Commands\CreateAya;
use App\Console\Commands\JsonGenerateCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateAya::class,
        CopyAyaCleanText::class,
        JsonGenerateCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
