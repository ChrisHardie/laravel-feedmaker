<?php

use ChrisHardie\Feedmaker\Sources\RssItemCollection;
use Illuminate\Support\Collection;

test('it is a collection', function () {
    $collection = new RssItemCollection();
    expect($collection)->toBeInstanceOf(Collection::class);
});

test('it can be instantiated with items', function () {
    $items = [
        ['title' => 'Test Item 1'],
        ['title' => 'Test Item 2'],
    ];
    $collection = RssItemCollection::make($items);
    expect($collection)->toHaveCount(2)
        ->and($collection->first()['title'])->toBe('Test Item 1');
});
