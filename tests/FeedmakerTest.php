<?php

use ChrisHardie\Feedmaker\Feedmaker;
use ChrisHardie\Feedmaker\FeedmakerFacade;

test('the facade resolves to the feedmaker service', function () {
    $instance = FeedmakerFacade::getFacadeRoot();
    expect($instance)->toBeInstanceOf(Feedmaker::class);
});

test('the feedmaker service can be instantiated', function () {
    $feedmaker = new Feedmaker();
    expect($feedmaker)->toBeInstanceOf(Feedmaker::class);
});
