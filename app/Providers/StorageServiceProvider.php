<?php

namespace App\Providers;

use App\Log;
use App\Site;
use App\Table;
use Illuminate\Support\ServiceProvider;
use App\Storage\MysqlStorage;

class StorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('StorageDriverInterface', 'App\Storage\MysqlStorage');
        
        $this->app->singleton('StorageServiceSite', function($app){
           return new Site($app->make('StorageDriverInterface'));
        });
       
        $this->app->singleton('StorageServiceLog', function($app){
           return new Log($app->make('StorageDriverInterface'));
        });

        $this->app->singleton('StorageServiceTable', function($app){
           return new Table($app->make('StorageDriverInterface'));
        });

    }
}
