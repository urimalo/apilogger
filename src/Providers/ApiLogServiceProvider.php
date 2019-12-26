<?php

namespace AWT\Providers;

use AWT\Console\Commands\ClearApiLogger;
use AWT\Http\Middleware\ApiLogger;
use AWT\Contracts\ApiLoggerInterface;
use AWT\DBLogger;
use AWT\FileLogger;
use Exception;
use Illuminate\Support\ServiceProvider;

class ApiLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bindServices();
    }

    public function bindServices(){
        $instance = FileLogger::class;
        $this->app->singleton(ApiLoggerInterface::class,$instance);

        $this->app->singleton('apilog', function ($app) use ($instance){
            return new ApiLogger($app->make($instance));
        });
    }
}
