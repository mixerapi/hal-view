# MixerApi

[![Latest Version on Packagist]()]()
[![License: MIT]()](LICENSE.md)
[![Build Status]()]()
[![Coverage Status]()]()

## Installation

```bash
composer require {vendor/package}
bin/cake plugin load {plugin}
```

Alternatively after composer installing you can manually load the plugin in your Application:

```php
# src/Application.php
public function bootstrap(): void
{
    // other logic...
    //$this->addPlugin('');
}
```

## Unit Tests

```bash
vendor/bin/phpunit
```

## Code Standards

```bash
composer check
```
