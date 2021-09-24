<?php

namespace ChrisHardie\Feedmaker;

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
            ->hasRoute('web')
            ->hasMigration('create_feedmaker_table')
            ->hasCommand(FeedmakerCommand::class);
    }
}
