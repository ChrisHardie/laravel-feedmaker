<?php

namespace App\Sources\TestSource;

use ChrisHardie\Feedmaker\Models\Source;
use ChrisHardie\Feedmaker\Sources\BaseSource;
use ChrisHardie\Feedmaker\Sources\RssItemCollection;

class TestSource extends BaseSource
{
    public static $called = false;

    public function generateRssItems(Source $source): RssItemCollection
    {
        self::$called = true;

        return RssItemCollection::make([]);
    }
}

namespace App\Sources\FailingSource;

use ChrisHardie\Feedmaker\Models\Source;
use ChrisHardie\Feedmaker\Sources\BaseSource;
use ChrisHardie\Feedmaker\Sources\RssItemCollection;

class FailingSource extends BaseSource
{
    public function generateRssItems(Source $source): RssItemCollection
    {
        throw new \Exception('Test Exception');
    }
}

namespace ChrisHardie\Feedmaker\Tests;

use App\Sources\TestSource\TestSource;
use ChrisHardie\Feedmaker\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

test('the feeds:update command calls the source class', function () {
    $source = Source::factory()->create([
        'class_name' => 'TestSource',
        'active' => true,
        'last_check_at' => null,
    ]);

    // Ensure the dummy class's static flag is reset
    TestSource::$called = false;

    Artisan::call('feeds:update', ['class_name' => 'TestSource']);

    expect(TestSource::$called)->toBeTrue();

    $source->refresh();
    expect($source->last_check_at)->not->toBeNull();
});

test('the feeds:update command processes checkable sources', function () {
    // Checkable source
    $source1 = Source::factory()->create([
        'class_name' => 'TestSource',
        'active' => true,
        'last_check_at' => null,
    ]);

    // Non-checkable source (inactive)
    $source2 = Source::factory()->create([
        'class_name' => 'TestSource',
        'active' => false,
        'last_check_at' => null,
    ]);

    TestSource::$called = false;

    Artisan::call('feeds:update');

    expect(TestSource::$called)->toBeTrue();

    $source1->refresh();
    $source2->refresh();

    expect($source1->last_check_at)->not->toBeNull();
    expect($source2->last_check_at)->toBeNull();
});

test('the command handles exceptions in source classes', function () {
    $source = Source::factory()->create([
        'class_name' => 'FailingSource',
        'active' => true,
        'last_check_at' => null,
    ]);

    Artisan::call('feeds:update', ['class_name' => 'FailingSource']);

    $source->refresh();
    expect($source->last_check_at)->not->toBeNull();
});
