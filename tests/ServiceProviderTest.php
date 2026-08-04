<?php

use Illuminate\Console\Scheduling\Schedule;
use ChrisHardie\Feedmaker\FeedmakerServiceProvider;

test('it schedules the feeds:update command', function () {
    $schedule = app(Schedule::class);
    
    $events = collect($schedule->events())->filter(function ($event) {
        return str_contains($event->command, 'feeds:update');
    });
    
    expect($events)->not->toBeEmpty();
    expect($events->first()->expression)->toBe('*/5 * * * *');
});
