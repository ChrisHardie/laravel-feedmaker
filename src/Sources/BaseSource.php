<?php

namespace ChrisHardie\Feedmaker\Sources;

use ChrisHardie\Feedmaker\Exceptions\SourceNotCrawlable;
use ChrisHardie\Feedmaker\Models\Source;
use Goutte\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\UriResolver;

abstract class BaseSource
{
    abstract public function generateRssItems(Source $source): RssItemCollection;

    /**
     * @param Source $source
     * @param null   $url
     * @return \Illuminate\Http\Client\Response|null
     * @throws SourceNotCrawlable
     */
    public function getUrl(Source $source, $url = null): ?\Illuminate\Http\Client\Response
    {
        if (empty($source->source_url) && empty($url)) {
            return null;
        }

        if (empty($url)) {
            $url = $source->source_url;
        }

        try {
            return HTTP::get($url);
        } catch (\Exception $e) {
            throw new SourceNotCrawlable(
                'Problem running GET request: ' . $e->getMessage(),
                0,
                $e,
                $source
            );
        }
    }

    /**
     * @param Source $source
     * @param null   $url Optional URL to override default source URL
     * @return \Symfony\Component\DomCrawler\Crawler|null
     * @throws SourceNotCrawlable
     */
    public function getCrawler(Source $source, $url = null): ?\Symfony\Component\DomCrawler\Crawler
    {
        if (empty($source->source_url) && empty($url)) {
            return null;
        }

        if (empty($url)) {
            $url = $source->source_url;
        }

        try {
            $client = new Client();

            return $client->request('GET', $url);
        } catch (\Exception $e) {
            throw new SourceNotCrawlable(
                'Problem running GET request: ' . $e->getMessage(),
                0,
                $e,
                $source
            );
        }
    }

    public function writeRssItemsToFile(RssItemCollection $rssItems, Source $source): void
    {
        $rssXml = View::make(
            'feedmaker::feedrss',
            [
                'source' => $source,
                'lastUpdated' => $this->lastUpdated($rssItems),
                'items' => $rssItems
                    ->sortByDesc('pubDate')
                    ->take(20),
            ]
        );
        Storage::disk('feedmaker')->put($source->rss_filename, $rssXml->render(), 'public');

        if (0 < $source->fail_count) {
            Log::info(sprintf(
                'Updating feed for source `%s` was successful after %d %s.',
                $source->name,
                $source->fail_count,
                Str::plural('failure', $source->fail_count)
            ));
        }

        $source->update([
            'last_succeed_at' => Carbon::now(),
            'fail_count' => 0,
            'next_check_after' => null,
        ]);
    }

    public function resolveUrl(Source $source, $url): string
    {
        $url = UriResolver::resolve($url, $source->base_url);

        return $url;
    }

    protected function lastUpdated(RssItemCollection $rssItems): string
    {
        if ($rssItems->isEmpty()) {
            return '';
        }

        $updatedAt = $rssItems
            ->sortBy(fn ($feedItem) => $feedItem['pubDate'])
            ->last()['pubDate'];

        return $updatedAt->toRssString();
    }
}
