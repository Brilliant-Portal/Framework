# BrilliantPortal

BrilliantPortal is an opinionated Laravel preset.

## Installation

You can install the package via composer:

```bash
# Require the framework.
composer require brilliant-portal/framework

# Run installation steps.
php artisan brilliant-portal-framework:install
# Options: --stack=livewire|inertia; --teams
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

### Teams

Add the `EnsureHasTeam` middleware to any routes that require a team.

```php
use BrilliantPortal\Framework\Http\Middleware\EnsureHasTeam;

Route::get('/profile', function () {
    //
})->middleware(EnsureHasTeam::class);
```

An `EnsureHasNoTeam` middleware is also available if useful.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
