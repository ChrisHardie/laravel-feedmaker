<?php

use ChrisHardie\Feedmaker\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('the feedsindex route macro registers the route', function () {
    config(['feedmaker.url' => '/test-feeds']);
    Route::feedsindex();

    $this->get('/test-feeds')->assertOk();
});

test('it displays active sources with last_succeed_at', function () {
    config(['feedmaker.url' => '/feeds']);
    Route::feedsindex();

    $activeSource = Source::factory()->create([
        'active' => true,
        'last_succeed_at' => Carbon::now()->subDay(),
        'name' => 'Active Source',
    ]);

    $inactiveSource = Source::factory()->create([
        'active' => false,
        'last_succeed_at' => Carbon::now()->subDay(),
        'name' => 'Inactive Source',
    ]);

    $neverSucceededSource = Source::factory()->create([
        'active' => true,
        'last_succeed_at' => null,
        'name' => 'Never Succeeded Source',
    ]);

    $response = $this->get('/feeds');

    $response->assertOk();
    $response->assertViewIs('feedmaker::feedsindex');
    $response->assertViewHas('sources', function ($sources) use ($activeSource) {
        return $sources->contains($activeSource) && $sources->count() === 1;
    });
    $response->assertSee('Active Source');
    $response->assertDontSee('Inactive Source');
    $response->assertDontSee('Never Succeeded Source');
});
