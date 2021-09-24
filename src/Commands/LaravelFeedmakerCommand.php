<?php

namespace ChrisHardie\LaravelFeedmaker\Commands;

use Illuminate\Console\Command;

class LaravelFeedmakerCommand extends Command
{
    public $signature = 'laravel-feedmaker';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
