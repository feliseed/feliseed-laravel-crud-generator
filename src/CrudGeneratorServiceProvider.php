<?php

namespace Feliseed\LaravelCrudGenerator;

use Feliseed\LaravelCrudGenerator\Console\Commands\MakeCrudCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->commands([
            MakeCrudCommand::class,
        ]);
    }
}