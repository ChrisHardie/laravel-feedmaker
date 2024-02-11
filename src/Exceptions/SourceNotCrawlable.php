<?php

namespace ChrisHardie\Feedmaker\Exceptions;

use ChrisHardie\Feedmaker\Models\Source;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SourceNotCrawlable extends Exception
{
    protected Source $source;

    public function __construct($message, $code, $previousException, $source)
    {
        parent::__construct($message, $code, $previousException);
        $this->source = $source;
    }

    public function report()
    {
        $message = $this->getMessage();
        $full_prev_message = '';
        if ($this->getPrevious()) {
            $full_prev_message = $this->getPrevious()->getMessage();
            $prev_message = preg_replace('/Stack trace:.*/s', '', $full_prev_message);
            $message .= '. ' . $prev_message;
        }

        if (empty($this->source->fail_count)) {
            $new_fail_count = 1;
        } else {
            $new_fail_count = $this->source->fail_count + 1;
        }

        // Calculate a new exponential next check time
        // e.g. 3^1 = 3m, 3^2 = 9m, 27m, 1.3h, 4h, 12h, 36h, 4.5d
        $this->source->update([
            'fail_count' => $new_fail_count,
            'last_fail_at' => Carbon::now(),
            'last_fail_reason' => $this->message,
            'next_check_after' => Carbon::now()->addMinutes(3 ** $new_fail_count),
        ]);

        $message = sprintf(
            'Updating source `%s` has failed %d %s, waiting at least %s for next check: %s',
            $this->source->name,
            $this->source->fail_count,
            Str::plural('time', $this->source->fail_count),
            $this->source->next_check_after->diffForHumans(),
            $message
        );

        if ($this->source->fail_count < config('feedmaker.feed_exception_min_for_warnings')) {
            Log::debug($message);
        } else {
            Log::warning($message);
        }
    }
}
