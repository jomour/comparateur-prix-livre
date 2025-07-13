<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PriceParserInterface;
use App\Services\AmazonPriceParserService;
use App\Services\CulturaPriceParserService;
use App\Services\FnacPriceParserService;
use App\Services\AnilistService;
use App\Actions\EstimateMangaPrice;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind les implémentations concrètes à l'interface PriceParserInterface
        $this->app->bind(PriceParserInterface::class . '.amazon', AmazonPriceParserService::class);
        $this->app->bind(PriceParserInterface::class . '.cultura', CulturaPriceParserService::class);
        $this->app->bind(PriceParserInterface::class . '.fnac', FnacPriceParserService::class);
        
        // Bind AnilistService
        $this->app->bind(AnilistService::class, AnilistService::class);
        
        // Bind EstimateMangaPrice Action
        $this->app->bind(EstimateMangaPrice::class, EstimateMangaPrice::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
