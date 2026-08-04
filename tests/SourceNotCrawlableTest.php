<?php

use ChrisHardie\Feedmaker\Exceptions\SourceNotCrawlable;
use ChrisHardie\Feedmaker\Models\Source;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('reporting SourceNotCrawlable updates the source model', function () {
    $source = Source::factory()->create([
        'fail_count' => 0,
        'name' => 'Test Source'
    ]);

    $exception = new SourceNotCrawlable('Test Failure', 0, null, $source);
    $exception->report();

    $source->refresh();
    expect($source->fail_count)->toBe(1);
    expect($source->last_fail_at)->not->toBeNull();
    expect($source->last_fail_reason)->toBe('Test Failure');
    expect($source->next_check_after)->not->toBeNull();
    
    // 3^1 = 3 minutes
    $expectedNextCheck = Carbon::now()->addMinutes(3);
    expect($source->next_check_after->diffInMinutes($expectedNextCheck))->toBeLessThanOrEqual(1);
});

test('it calculates exponential backoff correctly', function () {
    $source = Source::factory()->create([
        'fail_count' => 2,
    ]);

    $exception = new SourceNotCrawlable('Test Failure', 0, null, $source);
    $exception->report();

    $source->refresh();
    expect($source->fail_count)->toBe(3);
    
    // 3^3 = 27 minutes
    $expectedNextCheck = Carbon::now()->addMinutes(27);
    expect($source->next_check_after->diffInMinutes($expectedNextCheck))->toBeLessThanOrEqual(1);
});

test('it logs as debug when fail count is below threshold', function () {
    Log::shouldReceive('debug')->once();
    Log::shouldReceive('warning')->never();

    $source = Source::factory()->create([
        'fail_count' => 0, // Will become 1
        'name' => 'Test Source'
    ]);

    config(['feedmaker.feed_exception_min_for_warnings' => 2]);

    $exception = new SourceNotCrawlable('Test Failure', 0, null, $source);
    $exception->report();
});

test('it logs as warning when fail count reaches threshold', function () {
    Log::shouldReceive('warning')->once();
    Log::shouldReceive('debug')->never();

    $source = Source::factory()->create([
        'fail_count' => 1, // Will become 2
        'name' => 'Test Source'
    ]);

    config(['feedmaker.feed_exception_min_for_warnings' => 2]);

    $exception = new SourceNotCrawlable('Test Failure', 0, null, $source);
    $exception->report();
});

test('it includes previous exception message in report', function () {
    Log::shouldReceive('debug')->with(Mockery::on(function ($message) {
        return str_contains($message, 'Test Failure') && str_contains($message, 'Previous Error');
    }))->once();

    $source = Source::factory()->create([
        'fail_count' => 0,
        'name' => 'Test Source'
    ]);

    $prev = new Exception('Previous Error');
    $exception = new SourceNotCrawlable('Test Failure', 0, $prev, $source);
    $exception->report();
});
