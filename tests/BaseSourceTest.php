<?php

use ChrisHardie\Feedmaker\Models\Source;
use ChrisHardie\Feedmaker\Sources\BaseSource;
use ChrisHardie\Feedmaker\Sources\RssItemCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ChrisHardie\Feedmaker\Exceptions\SourceNotCrawlable;

class TestBaseSource extends BaseSource
{
    public function generateRssItems(Source $source): RssItemCollection
    {
        return RssItemCollection::make([]);
    }

    public function publicLastUpdated(RssItemCollection $rssItems): string
    {
        return $this->lastUpdated($rssItems);
    }
}

test('it can resolve urls', function () {
    $source = new Source(['base_url' => 'http://example.com/path/']);
    $baseSource = new TestBaseSource();
    
    expect($baseSource->resolveUrl($source, 'subpage'))->toBe('http://example.com/path/subpage')
        ->and($baseSource->resolveUrl($source, '/rootpage'))->toBe('http://example.com/rootpage')
        ->and($baseSource->resolveUrl($source, 'http://other.com'))->toBe('http://other.com');
});

test('it can get url content using http facade', function () {
    Http::fake([
        'http://example.com' => Http::response('<html></html>', 200),
    ]);
    
    $source = new Source(['source_url' => 'http://example.com']);
    $baseSource = new TestBaseSource();
    
    $response = $baseSource->getUrl($source);
    
    expect($response->body())->toBe('<html></html>');
});

test('getUrl throws SourceNotCrawlable on failure', function () {
    Http::fake([
        'http://example.com' => Http::response('Error', 500),
    ]);
    
    $source = new Source(['source_url' => 'http://example.com']);
    $baseSource = new TestBaseSource();
    
    // Note: Http::get doesn't throw on 500 by default, but it might throw on connection errors.
    // Let's force a connection error mock if possible, or check if the code handles 500s.
    // BaseSource.php:36 try { return HTTP::get($url); } catch (\Exception $e) { ... }
    
    Http::fake(fn() => throw new Exception('Connection error'));
    
    expect(fn() => $baseSource->getUrl($source))
        ->toThrow(SourceNotCrawlable::class);
});

test('it returns correct last updated string', function () {
    $items = RssItemCollection::make([
        ['pubDate' => Carbon::parse('2023-01-01 10:00:00')],
        ['pubDate' => Carbon::parse('2023-01-02 12:00:00')],
    ]);
    
    $baseSource = new TestBaseSource();
    $lastUpdated = $baseSource->publicLastUpdated($items);
    
    expect($lastUpdated)->toBe(Carbon::parse('2023-01-02 12:00:00')->toRssString());
});

test('it writes rss items to file', function () {
    Storage::fake('feedmaker');
    
    $source = new Source([
        'name' => 'Test Source',
        'class_name' => 'TestSource',
        'source_url' => 'http://example.com',
        'home_url' => 'http://example.com',
    ]);
    
    $items = RssItemCollection::make([
        [
            'title' => 'Test Item',
            'pubDate' => Carbon::now(),
            'url' => 'http://example.com/item',
            'description' => 'Test Description',
        ]
    ]);
    
    $baseSource = new TestBaseSource();
    $baseSource->writeRssItemsToFile($items, $source);
    
    Storage::disk('feedmaker')->assertExists('testsource.rss');
});

test('it resets fail count and logs on successful write after failure', function () {
    Storage::fake('feedmaker');
    Log::shouldReceive('info')->once();
    
    $source = Source::factory()->create([
        'name' => 'Test Source',
        'class_name' => 'TestSource',
        'fail_count' => 5,
        'next_check_after' => Carbon::now()->addDay(),
    ]);
    
    config(['feedmaker.feed_exception_min_for_warnings' => 2]);
    
    $items = RssItemCollection::make([
        [
            'title' => 'Test Item',
            'pubDate' => Carbon::now(),
            'url' => 'http://example.com/item',
            'description' => 'Test Description',
        ]
    ]);
    
    $baseSource = new TestBaseSource();
    $baseSource->writeRssItemsToFile($items, $source);
    
    $source->refresh();
    expect($source->fail_count)->toBe(0);
    expect($source->next_check_after)->toBeNull();
    expect($source->last_succeed_at)->not->toBeNull();
});