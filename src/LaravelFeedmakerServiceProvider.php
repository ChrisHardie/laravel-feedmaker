<?php

namespace ChrisHardie\LaravelFeedmaker;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ChrisHardie\LaravelFeedmaker\Commands\LaravelFeedmakerCommand;

class LaravelFeedmakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-feedmaker')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-feedmaker_table')
            ->hasCommand(LaravelFeedmakerCommand::class);
    }
}
