<?php

namespace ChrisHardie\Feedmaker;

use ChrisHardie\Feedmaker\Http\SourceController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ChrisHardie\Feedmaker\Commands\FeedmakerCommand;

class FeedmakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('feedmaker')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_feedmaker_table')
            ->hasCommand(FeedmakerCommand::class);
    }

    public function packageRegistered() :void
    {
        $this->registerRouteMacro();
    }

    protected function registerRouteMacro(): void
    {
        $router = $this->app['router'];

        $router->macro('feeds', function ($baseUrl = '') use ($router) {
            $url = url(config('feedmaker.url'));

            $router->get($url, '\\'.SourceController::class)->name("feedmaker.index");
        });
    }
}
