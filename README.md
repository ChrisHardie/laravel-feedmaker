# Laravel Feedmaker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrishardie/laravel-feedmaker.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-feedmaker)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/chrishardie/laravel-feedmaker/run-tests?label=tests)](https://github.com/chrishardie/laravel-feedmaker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/chrishardie/laravel-feedmaker/Check%20&%20fix%20styling?label=code%20style)](https://github.com/chrishardie/laravel-feedmaker/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chrishardie/laravel-feedmaker.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-feedmaker)

Laravel package to enable crawling/parsing HTML pages and generating corresponding RSS feeds

## Installation

You can install the package via composer:

```bash
composer require chrishardie/laravel-feedmaker
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="ChrisHardie\Feedmaker\FeedmakerServiceProvider" --tag="feedmaker-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="ChrisHardie\Feedmaker\FeedmakerServiceProvider" --tag="feedmaker-config"
```

This is the contents of the published config file:

```php
return [
    // How often to update feeds from sources, in minutes
    'default_update_frequency' => 60,

    // Feed index web route
    'url' => '/',
];

```

Add a new disk to your `config/filesystems.php` file, to define where the generated RSS feeds will be stored:

```php
    'disks' => [
        ...
        'feedmaker' => [
            'driver' => 'local',
            'root' => storage_path('app/feeds'),
            'url' => env('APP_URL').'/feeds',
            'visibility' => 'public',
        ],
        ...
    'links' => [
        ...
        public_path('feeds') => storage_path('app/feeds'),

```

Then, run `artisan storage:link` to make sure the storage disk is in place.

To display an index of available feeds, configure the `$url` variable in the config file and add the following to your `routes/web.php` file:

```php
Route::feedsindex();
```

If you want to get notices about issues related to updating the feeds from sources, make sure you define a logging destination. For example, to receive Slack notifications, make sure `LOG_SLACK_WEBHOOK_URL` is defined in `.env` and then set your `LOG_CHANNEL` to include a log stack that includes Slack.

## Usage

There are two steps for adding a new source to be included:

1. Create a new Source model. If you don't have an admin interface, you can do this via tinker:

```bash
$ artisan tinker
>>> $s = new ChrisHardie\Feedmaker\Models\Source
>>> $s->class_name = 'YourSource'
>>> $s->source_url = 'https://www.example.com/news'
>>> $s->name = 'Source Name'
>>> $s->home_url = 'https://example.com/'
>>> $s->frequency = 60
>>> $s->save();
```

This tells the application the basic info about your source including the PHP class that will define how to scrape/crawl it, the URL to crawl, and the human-facing name and main URL.

2. Create a source class in `app/Sources/YourSource/YourSource.php` that defines a `generateRssItems()` method returning a collection of items to include in the RSS feed. Here's an example:

```php
<?php

namespace App\Sources\YourSource;

use ChrisHardie\Feedmaker\Sources\BaseSource;
use ChrisHardie\Feedmaker\Sources\RssItemCollection;
use ChrisHardie\Feedmaker\Models\Source;

class YourSource extends BaseSource
{
    /**
     * @param Source $source
     * @return RssItemCollection
     * @throws \JsonException
     * @throws SourceNotCrawlable
     */
    public function generateRssItems(Source $source) : RssItemCollection
    {
        $items = array();
        $html = HTTP::get($source->source_url);
        ...
        return RssItemCollection::make($items);    
    }
}
```

If you will be scraping a URL's dom via CSS or XPath selectors, you can use the scraper trait to simplify this. It handles the generateRssItems method for you, and all you have to do is define a `parsse()` method that returns an RssItemCollection:

```php
<?php

namespace App\Sources\YourSource;

use ChrisHardie\Feedmaker\Sources\BaseSource;
use ChrisHardie\Feedmaker\Sources\RssItemCollection;
use ChrisHardie\Feedmaker\Models\Source;

class YourSource extends BaseSource
{
    use ScraperTrait;

    /**
     * @throws SourceNotCrawlable
     */
    public function parse(Crawler $crawler, Source $source) : RssItemCollection
    {
        $items = array();
        $nodes = $crawler->filter('.news-items');
        foreach ($nodes as $node) {
            ...
        }
        return RssItemCollection::make($items);
    }
```

The RssItemCollection must contain the following keys for each item:

* pubDate: Carbon date object
* title: string
* url: string
* description: string

Optionally, it can also contain these keys:

* guid: a URL that will become the unique/GUID for the RSS item instead of the url

Then, you can force a check of your source and generate a corresponding feed:

`$ artisan feeds:update YourSource`

View the result at `https://yoursite.com/feeds/yoursource.rss`

An index of all generated feeds should be available at the URI defined in the config file.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Issues and Pull Requests are welcome.

## Credits

- [Chris Hardie](https://github.com/ChrisHardie)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
