<?php

use ChrisHardie\Feedmaker\Models\Source;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it has an rss filename attribute', function () {
    $source = new Source(['class_name' => 'My Test Source']);
    expect($source->rss_filename)->toBe('my-test-source.rss');
});

test('it has a base url attribute that falls back correctly', function () {
    $source = new Source(['source_url' => 'http://source.com']);
    expect($source->base_url)->toBe('http://source.com');

    $source->home_url = 'http://home.com';
    expect($source->base_url)->toBe('http://home.com');

    $source->base_url = 'http://base.com';
    expect($source->base_url)->toBe('http://base.com');
});

test('checkable scope filters correctly', function () {
    // Active and never checked
    Source::factory()->create(['active' => true, 'last_check_at' => null]);
    
    // Inactive
    Source::factory()->create(['active' => false]);
    
    // Active but checked recently (within 60 min frequency)
    Source::factory()->create([
        'active' => true, 
        'last_check_at' => Carbon::now()->subMinutes(30),
        'frequency' => 60
    ]);
    
    // Active and checked long ago
    Source::factory()->create([
        'active' => true, 
        'last_check_at' => Carbon::now()->subMinutes(90),
        'frequency' => 60
    ]);

    expect(Source::checkable()->count())->toBe(2);
});
