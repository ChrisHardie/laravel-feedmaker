<?php

namespace ChrisHardie\Feedmaker\Commands;

use ChrisHardie\Feedmaker\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FeedmakerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:update {class_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the feeds from their sources';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! empty($this->argument('class_name'))) {
            $sources = Source::where('class_name', $this->argument('class_name'))->get();
        } else {
            $sources = Source::checkable()->orderBy('last_check_at', 'asc')->get();
        }

        $source_class_base_path = "App\Sources\\";

        Log::debug('Checking ' . $sources->count() . ' sources for updates.');

        foreach ($sources as $source) {
            $source_class_path = $source_class_base_path . $source->class_name . '\\' . $source->class_name;
            if (class_exists($source_class_path)) {
                $source_class = new $source_class_path();

                try {
                    $rssItems = $source_class->generateRssItems($source);
                    $source_class->writeRssItemsToFile($rssItems, $source);
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }
    }
}
