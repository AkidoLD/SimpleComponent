# SimpleComponent

A simple PHP library for rendering HTML on the server in a safe and intuitive way.

## Status

🚧 **Work in Progress** - Not ready for production use.

## Goal

The main goal of this project is to **generate HTML code on the server easily, intuitively, and securely**.  
It is designed to help you build reusable components with minimal boilerplate while keeping your code clean.

## Features in v0.1.0

- Basic `Component` class for generating HTML elements
- `BemHelper` for generating BEM-compliant CSS class strings
- `BemxHelper` for BEM generation with parent context
- Custom exceptions for validation and error handling

## Usage Example

```php
use AkidoLd\SimpleComponent\Components\Component;
use AkidoLd\SimpleComponent\Utils\BEM\BemHelper;

require_once __DIR__ . '/vendor/autoload.php';

// Create a basic button component
$button = new Component('button')
    ->addContent('Click me')
    ->addClass(BemHelper::generate('btn', 'label', ['large', 'primary']));

echo $button;

// Example output:
// <button class="btn__label btn__label--large btn__label--primary">Click me</button>
```

## Planned Features

* [X] Simple and secure HTML generation
* [X] Easy-to-use component API
* [X] More helpers for BEMX and advanced component composition

## Installation

Since SimpleComponent is not published on Packagist, you have two ways to include it in your project:

### 1. Clone the repository

```bash
git clone https://github.com/AkidoLd/SimpleComponent.git
cd SimpleComponent
composer install
```

Then, include Composer's autoloader in your project:

```php
require_once '/path/to/SimpleComponent/vendor/autoload.php';
```

### 2. Add the repository directly to your `composer.json`

If you want Composer to manage updates and dependencies:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/AkidoLd/SimpleComponent.git"
        }
    ],
    "require": {
        "akido-ld/simple-component": "dev-main"
    }
}
```

Then run:

```bash
composer update
```

And include Composer's autoloader:

```php
require_once 'vendor/autoload.php';
```

> Both methods allow you to use SimpleComponent without relying on Packagist.

Or manually include the files if needed.

## Documentation

For more details on BEM methodology, see: [BEM documentation](https://getbem.com/introduction/).
`BemHelper` and `BemxHelper` classes have full PHPDoc comments for reference.

## License

MIT License – see [LICENSE](LICENSE) file for details.
