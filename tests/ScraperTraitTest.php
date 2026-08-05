<?php

use ChrisHardie\Feedmaker\Exceptions\SourceNotCrawlable;
use ChrisHardie\Feedmaker\Models\Source;
use ChrisHardie\Feedmaker\Sources\BaseSource;
use ChrisHardie\Feedmaker\Sources\RssItemCollection;
use ChrisHardie\Feedmaker\Sources\ScraperTrait;
use Symfony\Component\DomCrawler\Crawler;

class TestScraper extends BaseSource
{
    use ScraperTrait;

    public function parse(Crawler $crawler, Source $source): RssItemCollection
    {
        return RssItemCollection::make([
            ['title' => $crawler->filter('h1')->text()],
        ]);
    }
}

test('it can generate rss items using scraper trait', function () {
    $source = new Source(['source_url' => 'http://example.com']);

    $scraper = Mockery::mock(TestScraper::class)->makePartial();
    $scraper->shouldReceive('getCrawler')
        ->with($source)
        ->andReturn(new Crawler('<html><body><h1>Test Title</h1></body></html>'));

    $items = $scraper->generateRssItems($source);

    expect($items)->toBeInstanceOf(RssItemCollection::class)
        ->and($items)->toHaveCount(1)
        ->and($items->first()['title'])->toBe('Test Title');
});

test('it throws SourceNotCrawlable exception on error', function () {
    $source = new Source(['source_url' => 'http://example.com']);

    $scraper = Mockery::mock(TestScraper::class)->makePartial();
    $scraper->shouldReceive('getCrawler')
        ->andThrow(new Exception('Network error'));

    expect(fn () => $scraper->generateRssItems($source))
        ->toThrow(SourceNotCrawlable::class, 'Problem fetching and parsing source');
});
