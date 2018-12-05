<?php

namespace Celebpost\Console;

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
        Commands\PostInstagram::class,
        Commands\UserTime::class,
        Commands\DeletePost::class,
        Commands\SynchronAffiliate::class,
        Commands\UserTimeLog::class
        // Commands\UpdatePublishSchedule::class
        // Commands\FillProxy::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      /*
			// $schedule->command('send:instagram')->withoutOverlapping()->timezone(''.env('IG_TIMEZONE').'');
      $schedule->command('send:instagram')->timezone(''.env('IG_TIMEZONE').'');
      // $schedule->command('count:userstime')->everyFiveMinutes()->withoutOverlapping();
      // $schedule->command('count:userstime')->everyThirtyMinutes()->withoutOverlapping();
      $schedule->command('count:userstime')->hourly()->withoutOverlapping();
      // $schedule->command('delete:post')->withoutOverlapping();
      $schedule->command('delete:post');
      $schedule->command('synchron:affiliate')->withoutOverlapping();
      //$schedule->command('fill:proxy')->everyThirtyMinutes()->withoutOverlapping();
      $schedule->command('count:timelog')->daily();
      // $schedule->command('update:publishschedule')->daily();  // ga dipake, cuman panggil link dari cron biasa, karena ga bs baca public_path
			*/
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
