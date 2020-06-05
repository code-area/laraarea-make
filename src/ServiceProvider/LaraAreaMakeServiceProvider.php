<?php

namespace LaraAreaMake\ServiceProvider;
//
use LaraAreaMake\Console\Commands\MakeModel;
use LaraAreaSupport\LaraAreaServiceProvider;

class LaraAreaMakeServiceProvider extends LaraAreaServiceProvider
{
    /**
     *
     */
    public function boot()
    {
        $this->mergeConfig(__DIR__);
        $this->runningInConsole([
            MakeModel::class,
        ]);
    }

    /**
     *
     */
    public function register()
    {
        $this->registerConstants(__DIR__, 'class_constants.php');
        $this->registerConstants(__DIR__);
        $this->registerFunctions(__DIR__);
    }
}

