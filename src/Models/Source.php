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

    protected $appends = [
        'rss_filename',
    ];

    protected $fillable = [
        'fail_count',
        'last_check_at',
        'last_succeed_at',
        'last_fail_at',
        'last_fail_reason',
        'next_check_after',
    ];

    protected $casts = [
        'last_check_at' => 'datetime',
        'last_succeed_at' => 'datetime',
        'last_fail_at' => 'datetime',
        'next_check_after' => 'datetime',
    ];

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
    public function scopeCheckable(Builder $query)
    {
        $query
            ->where('active', true);

        $query->where(function ($query) {
            $query
                // Never been checked
                ->whereNull('last_check_at')
                // Haven't been checked in the last X minutes given sitewide update frequency
                // Haven't been checked in the last X minutes given feed-specific update frequency
                ->orWhereRaw('last_check_at <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL frequency MINUTE)');
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
