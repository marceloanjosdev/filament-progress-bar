# Filament Progress Bar

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marceloanjosdev/filament-progress-bar.svg?style=flat-square)](https://packagist.org/packages/marceloanjosdev/filament-progress-bar)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/marceloanjosdev/filament-progress-bar/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/marceloanjosdev/filament-progress-bar/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/marceloanjosdev/filament-progress-bar/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/marceloanjosdev/filament-progress-bar/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/marceloanjosdev/filament-progress-bar.svg?style=flat-square)](https://packagist.org/packages/marceloanjosdev/filament-progress-bar)

A simple, global and non-intrusive **progress bar system for Filament**.

This package allows you to track long-running processes (jobs, imports, exports, background tasks) and display their progress **globally in the Filament admin panel**, without wiring Livewire components or custom widgets per page.

It is designed to be:
- **Simple** (explicit API: init → update → complete)
- **Safe for queued jobs**
- **Cache-based** (Redis optional, not required)
- **Filament-native** (top bar overlay, polling handled automatically)

---

## Features

- Global progress bar overlay in Filament
- Multiple simultaneous progress bars
- Determinate and indeterminate progress
- Auto-polling only when progress bars are active
- Safe default behaviour for queued jobs
- No Redis requirement (uses Laravel cache)
- Redis-ready if you want it later

---

## Installation

Install the package via Composer:

```bash
composer require marceloanjosdev/filament-progress-bar
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="filament-progress-bar-config"
```

No migrations are required.

---

## Configuration

This is the default configuration file:

```php
return [
    'cache_store' => null,

    'key_prefix' => 'filament-progress-bar',

    'ttl_seconds' => 3600,

    'display' => [
        /*
         | Available options:
         | - 'percent'  → 75%
         | - 'total'    → 75/100
         | - 'both'     → 75/100 · 75%
         */
        'meta' => 'both',
    ],

    'polling' => [
        'enabled' => true,

        // When there are no bars
        'idle_interval_ms' => 5000,

        // When there is at least one bar
        'active_interval_ms' => 1000,

        // Route & middleware for the internal endpoint used by the Filament UI
        'route' => '/filament-progress-bar/progress',
        'middleware' => ['web', 'auth'],
    ],
];

```

---

## Usage

```php
   app(ProgressManager::class)->init(
       key: 'progress:one',
       totalRecords: 100,
       autoComplete: true,
       label: 'Import Clients'
    );

    app(ProgressManager::class)->update('progress:one');

    app(ProgressManager::class)->complete('progress:one');
```

---

## Testing

```bash
composer test
```

---

## License

MIT License.
