<?php

namespace ChrisHardie\Feedmaker;

use ChrisHardie\Feedmaker\Commands\FeedmakerCommand;
use ChrisHardie\Feedmaker\Http\SourceController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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

    public function packageRegistered(): void
    {
        $this->registerRouteMacro();
    }

    public function packageBooted(): void
    {
        $this->scheduleFeedUpdates();
    }

    protected function scheduleFeedUpdates(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('feeds:update')->everyFiveMinutes();
        });
    }

    protected function registerRouteMacro(): self
    {
        Route::macro('feedsindex', function () {
            Route::get(config('feedmaker.url'), '\\' . SourceController::class)->name('feedmaker.index');
        });

        return $this;
    }
}
