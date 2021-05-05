# BrilliantPortal

BrilliantPortal is an opinionated Laravel preset.

## Installation

You can install the package via composer:

```bash
composer require brilliant-portal/framework
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="BrilliantPortal\Framework\FrameworkServiceProvider" --tag="framework-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="BrilliantPortal\Framework\FrameworkServiceProvider" --tag="framework-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
