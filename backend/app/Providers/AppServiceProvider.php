<?php

namespace App\Providers;

use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;
use App\Infrastructure\Map\DistanceCalculation\EuclideanDistanceCalculator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DistanceCalculatorInterface::class, EuclideanDistanceCalculator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
