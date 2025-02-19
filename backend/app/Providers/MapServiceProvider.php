<?php

namespace App\Providers;

use App\Infrastructure\Map\MapHelper;
use Illuminate\Support\ServiceProvider;

class MapServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('map', function () {
            return new MapHelper();
        });
    }
}
