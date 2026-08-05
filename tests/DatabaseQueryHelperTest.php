<?php

use ChrisHardie\Feedmaker\Support\DatabaseQueryHelper;
use Illuminate\Support\Facades\DB;

test('it generates correct mysql expression', function () {
    $expression = DatabaseQueryHelper::timestampOlderThanColumnMinutesExpression('last_check_at', 'frequency', 'mysql');
    expect($expression)->toBe('last_check_at <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL frequency MINUTE)');
});

test('it generates correct sqlite expression', function () {
    $expression = DatabaseQueryHelper::timestampOlderThanColumnMinutesExpression('last_check_at', 'frequency', 'sqlite');
    expect($expression)->toBe("last_check_at <= datetime('now', '-' || frequency || ' minutes')");
});

test('it generates correct pgsql expression', function () {
    $expression = DatabaseQueryHelper::timestampOlderThanColumnMinutesExpression('last_check_at', 'frequency', 'pgsql');
    expect($expression)->toBe("last_check_at <= NOW() - INTERVAL '1 minute' * frequency");
});

test('it throws exception for unsupported driver', function () {
    expect(fn () => DatabaseQueryHelper::timestampOlderThanColumnMinutesExpression('col', 'freq', 'oracle'))
        ->toThrow(InvalidArgumentException::class);
});

test('whereTimestampOlderThanColumnMinutes adds correct clause', function () {
    $query = DB::table('sources');
    DatabaseQueryHelper::whereTimestampOlderThanColumnMinutes($query, 'last_check_at', 'frequency');

    $driver = $query->getConnection()->getDriverName();
    $sql = $query->toSql();

    if ($driver === 'sqlite') {
        expect($sql)->toContain("last_check_at <= datetime('now', '-' || frequency || ' minutes')");
    }
});
