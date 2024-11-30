<?php

namespace App;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\DynamicQueue;

class DynamicQueueServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('queue.dynamic', function () {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            return new DynamicWorker(
                $this->app['queue'],
                $this->app['events'],
                $this->app[ExceptionHandler::class],
                $isDownForMaintenance
            );
        });

        $this->app->singleton(DynamicQueue::class, static function ($app) {
            return new DynamicQueue(
                $app['queue.dynamic'],
                $app['cache.store']
            );
        });

        $this->commands([
            DynamicQueue::class,
        ]);
    }
}