<?php

namespace ChrisHardie\Feedmaker\Tests;

use ChrisHardie\Feedmaker\FeedmakerServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ChrisHardie\\Feedmaker\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FeedmakerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('filesystems.disks.feedmaker', [
            'driver' => 'local',
            'root' => __DIR__ . '/temp',
        ]);

        $migration = include __DIR__.'/../database/migrations/create_feedmaker_table.php.stub';
        $migration->up();
    }
}
