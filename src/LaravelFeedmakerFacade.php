<?php

namespace ChrisHardie\LaravelFeedmaker;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ChrisHardie\LaravelFeedmaker\LaravelFeedmaker
 */
class LaravelFeedmakerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-feedmaker';
    }
}
