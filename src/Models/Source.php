<?php

namespace ChrisHardie\Feedmaker\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Source extends Model
{
    use HasFactory;

    protected $appends = array(
        'rss_filename',
    );

    protected $fillable = array(
        'fail_count',
        'last_check_at',
        'last_succeed_at',
        'last_fail_at',
        'last_fail_reason',
        'next_check_after',
    );

    protected $dates = array(
        'last_check_at',
        'last_succeed_at',
        'last_fail_at',
        'next_check_after',
    );

    public function getBaseUrlAttribute($value)
    {
        if (empty($value)) {
            if (! empty($this->home_url)) {
                $value = $this->home_url;
            } else {
                $value = $this->source_url;
            }
        }

        return $value;
    }

    public function getRssFilenameAttribute(): string
    {
        return Str::slug($this->class_name) . '.rss';
    }

    /**
     * Scope a query to only include tracking objects not paused
     *
     * @param  Builder  $query
     * @param  string  $frequency
     * @return mixed
     */
    public function scopeCheckable(Builder $query, string $frequency = '')
    {
        // Set default frequency to Source-specific or 60 mins
        $check_frequency_minutes = (! empty($this->frequency)) ? (int) $this->frequency : config('feedmaker.default_update_frequency');

        $query
            ->where('active', true);

        $query->where(function ($query) use ($check_frequency_minutes) {
            $query
                // Never been checked
                ->whereNull('last_check_at')
                // Haven't been checked in the last X minutes given sitewide update frequency
                ->orWhere(
                    'last_check_at',
                    '<',
                    Carbon::now()->subMinutes($check_frequency_minutes)
                );
        });

        // Sources where no next check is set or where it has passed.
        $query->where(function ($query) {
            $query
                // Never been checked
                ->whereNull('next_check_after')
                // Haven't been checked in the last X minutes given sitewide update frequency
                ->orWhere(
                    'next_check_after',
                    '<=',
                    Carbon::now()
                );
        });
        return $query;
    }
}
