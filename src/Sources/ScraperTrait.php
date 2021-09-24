<?php

namespace ChrisHardie\Feedmaker\Sources;

use ChrisHardie\Feedmaker\Exceptions\SourceNotCrawlable;
use ChrisHardie\Feedmaker\Models\Source;
use Symfony\Component\DomCrawler\Crawler;

trait ScraperTrait
{
    abstract public function parse(Crawler $crawler, Source $source) : RssItemCollection;

    /**
     * @param Source $source
     * @return RssItemCollection|false
     * @throws SourceNotCrawlable
     */
    public function generateRssItems(Source $source) : RssItemCollection
    {
        try {
            $crawler = $this->getCrawler($source);
            return $this->parse($crawler, $source);
        } catch (\Exception $e) {
            throw new SourceNotCrawlable(
                'Problem fetching and parsing source',
                0,
                $e,
                $source
            );
        }
    }
}
