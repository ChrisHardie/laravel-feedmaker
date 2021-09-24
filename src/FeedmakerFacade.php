<?php

namespace ChrisHardie\Feedmaker;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ChrisHardie\Feedmaker\Feedmaker
 */
class FeedmakerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'feedmaker';
    }
}
