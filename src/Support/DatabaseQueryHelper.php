<?php

namespace ChrisHardie\Feedmaker\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DatabaseQueryHelper
{
    /**
     * Add a where clause that checks if a timestamp column is older than X minutes
     * using the specified column value for the minute interval
     *
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param string $timestampColumn
     * @param string $minutesColumn
     * @return Builder|\Illuminate\Database\Eloquent\Builder
     */
    public static function whereTimestampOlderThanColumnMinutes(
        $query,
        string $timestampColumn,
        string $minutesColumn
    ) {
        $driver = $query->getConnection()->getDriverName();

        return match ($driver) {
            'mysql' => $query->whereRaw(
                "{$timestampColumn} <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$minutesColumn} MINUTE)"
            ),
            'sqlite' => $query->whereRaw(
                "{$timestampColumn} <= datetime('now', '-' || {$minutesColumn} || ' minutes')"
            ),
            'pgsql' => $query->whereRaw(
                "{$timestampColumn} <= NOW() - INTERVAL '1 minute' * {$minutesColumn}"
            ),
            default => throw new \InvalidArgumentException("Unsupported database driver: {$driver}")
        };
    }

    /**
     * Get the raw SQL expression for checking if a timestamp is older than X minutes
     * using the specified column value for the minute interval
     */
    public static function timestampOlderThanColumnMinutesExpression(
        string $timestampColumn,
        string $minutesColumn,
        ?string $driver = null
    ): string {
        $driver ??= DB::connection()->getDriverName();

        return match ($driver) {
            'mysql' => "{$timestampColumn} <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$minutesColumn} MINUTE)",
            'sqlite' => "{$timestampColumn} <= datetime('now', '-' || {$minutesColumn} || ' minutes')",
            'pgsql' => "{$timestampColumn} <= NOW() - INTERVAL '1 minute' * {$minutesColumn}",
            default => throw new \InvalidArgumentException("Unsupported database driver: {$driver}")
        };
    }
}
