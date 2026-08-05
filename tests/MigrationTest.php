<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('the sources table has all required columns', function () {
    expect(Schema::hasTable('sources'))->toBeTrue();

    $columns = [
        'id',
        'created_at',
        'updated_at',
        'class_name',
        'last_check_at',
        'last_succeed_at',
        'last_fail_at',
        'last_fail_reason',
        'fail_count',
        'next_check_after',
        'source_url',
        'name',
        'base_url',
        'home_url',
        'frequency',
        'respect_timestamp',
        'active',
    ];

    foreach ($columns as $column) {
        expect(Schema::hasColumn('sources', $column))->toBeTrue("Column {$column} is missing in sources table");
    }
});
