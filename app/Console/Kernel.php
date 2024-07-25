<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SyncRightMoveProperties;
use App\Jobs\SyncOnTheMarketProperties;
use App\Jobs\SyncZooplaProperties;
use App\Models\ZooplaSettings;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

/**
    protected function schedule(Schedule $schedule): void
    {
        // Sync properties with RightMove every hour
        $schedule->call(function () {
            try {
                (new SyncRightMoveProperties())->handle();
            } catch (\Exception $e) {
                Log::error('RightMove sync failed: ' . $e->getMessage());
            }
        })->hourly();

        // Sync properties with OnTheMarket
        $schedule->call(function () {
            try {
                $frequency = config('services.onthemarket.sync_frequency', 'hourly');
                if ($frequency === 'hourly') {
                    (new SyncOnTheMarketProperties())->handle();
                }
            } catch (\Exception $e) {
                Log::error('OnTheMarket hourly sync failed: ' . $e->getMessage());
            }
        })->hourly();

        $schedule->call(function () {
            try {
                $frequency = config('services.onthemarket.sync_frequency', 'hourly');
                if ($frequency === 'daily') {
                    (new SyncOnTheMarketProperties())->handle();
                }
            } catch (\Exception $e) {
                Log::error('OnTheMarket daily sync failed: ' . $e->getMessage());
            }
        })->daily();

        $schedule->call(function () {
            try {
                $frequency = config('services.onthemarket.sync_frequency', 'hourly');
                if ($frequency === 'weekly') {
                    (new SyncOnTheMarketProperties())->handle();
                }
            } catch (\Exception $e) {
                Log::error('OnTheMarket weekly sync failed: ' . $e->getMessage());
            }
        })->weekly();

        // Sync properties with Zoopla
        $schedule->command('zoopla:sync-properties')
            ->hourly()
            ->when(function () {
                $zooplaSettings = ZooplaSettings::first();
                return $zooplaSettings ? $zooplaSettings->sync_frequency === 'hourly' : true;
            })
            ->withoutOverlapping()
            ->onFailure(function () {
                Log::error('Zoopla hourly sync failed');
            })
            ->onSuccess(function () {
                Log::info('Zoopla hourly sync completed successfully');
            });
    
        $schedule->command('zoopla:sync-properties')
            ->daily()
            ->when(function () {
                $zooplaSettings = ZooplaSettings::first();
                return $zooplaSettings ? $zooplaSettings->sync_frequency === 'daily' : false;
            })
            ->withoutOverlapping()
            ->onFailure(function () {
                Log::error('Zoopla daily sync failed');
            })
            ->onSuccess(function () {
                Log::info('Zoopla daily sync completed successfully');
            });
    
        $schedule->command('zoopla:sync-properties')
            ->weekly()
            ->when(function () {
                $zooplaSettings = ZooplaSettings::first();
                return $zooplaSettings ? $zooplaSettings->sync_frequency === 'weekly' : false;
            })
            ->withoutOverlapping()
            ->onFailure(function () {
                Log::error('Zoopla weekly sync failed');
            })
            ->onSuccess(function () {
                Log::info('Zoopla weekly sync completed successfully');
            });
    }

**/

    /**
     * Register the commands for the application.
     */


    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ScheduleLeaseNotifications)->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
