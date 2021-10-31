<?php

namespace App\Providers;

use App\Exceptions\InvalidAuthException;
use App\Services\Trello\TrelloApiGateway;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        try {
            $this->app->bind(TrelloApiGateway::class, function ($app) {
                return new TrelloApiGateway(
                    config('prello.auth.key'),
                    config('prello.auth.token'),
                );
            });
        } catch (\Throwable $e) {
            throw new InvalidAuthException($e->getMessage());
        }
    }
}
