<?php

namespace ChrisHardie\Feedmaker\Commands;

use Illuminate\Console\Command;

class FeedmakerCommand extends Command
{
    public $signature = 'feedmaker';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
