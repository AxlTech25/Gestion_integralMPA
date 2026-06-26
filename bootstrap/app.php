<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            'permission' => \App\Http\Middleware\EnsurePermission::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('sgmi:ml-predict')
            ->dailyAt(config('sgmi.ml.schedule_time', '02:00'))
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->job(new \App\Jobs\SyncSigaPatrimonioJob)
            ->dailyAt(config('integrations.schedule.siga_patrimonio', '02:00'))
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->job(new \App\Jobs\SyncSigaOrganigramaJob)
            ->dailyAt(config('integrations.schedule.siga_organigrama', '02:15'))
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->job(new \App\Jobs\SyncSiafEjecucionJob)
            ->dailyAt(config('integrations.schedule.siaf_ejecucion', '03:00'))
            ->withoutOverlapping()
            ->onOneServer();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
