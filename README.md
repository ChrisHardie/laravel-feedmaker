# Laravel Feedmaker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrishardie/laravel-feedmaker.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-feedmaker)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/chrishardie/laravel-feedmaker/run-tests?label=tests)](https://github.com/chrishardie/laravel-feedmaker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/chrishardie/laravel-feedmaker/Check%20&%20fix%20styling?label=code%20style)](https://github.com/chrishardie/laravel-feedmaker/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chrishardie/laravel-feedmaker.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-feedmaker)

Laravel package to enable crawling/parsing HTML pages and generating corresponding RSS feeds

## Installation

You can install the package via composer:

```bash
composer config repositories.laravel-feedmaker vcs https://github.com/ChrisHardie/laravel-feedmaker.git
composer require chrishardie/laravel-feedmaker:dev-main
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
## Usage

```php
$feedmaker = new ChrisHardie\Feedmaker();
echo $feedmaker->echoPhrase('Hello, ChrisHardie!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Chris Hardie](https://github.com/ChrisHardie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
