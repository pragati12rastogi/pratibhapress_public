<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     * everyMinute
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('backup:run')->daily();
        $schedule->command('backup:run')->weekly();
        $schedule->command('backup:run')->monthly();
        $schedule->command('backup:run')->yearly();
        $schedule->command('backup:clean')->daily();
        $schedule->call('App\Http\Controllers\Employee\Checklist@future_task_status')->dailyAt('23:00');
        $schedule->call('App\Http\Controllers\Employee\Checklist@Auto_update')->dailyAt('23:30');
        // $schedule->call('App\Http\Controllers\Email\EmailController@daily_reports')->dailyAt('20:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
