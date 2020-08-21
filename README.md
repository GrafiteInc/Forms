![Grafite Forms](GrafiteForms-banner.png)

**Forms** - An amazing Forms component for Laravel.

[![Build Status](https://github.com/GrafiteInc/Forms/workflows/PHP%20Package%20Tests/badge.svg?branch=master)](https://github.com/GrafiteInc/Forms/actions?query=workflow%3A%22PHP+Package+Tests%22)
[![Maintainability](https://api.codeclimate.com/v1/badges/8c00a046fec32d8b8ac7/maintainability)](https://codeclimate.com/github/GrafiteInc/Forms/maintainability)
[![Packagist](https://img.shields.io/packagist/dt/grafite/forms.svg)](https://packagist.org/packages/grafite/forms)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/grafite/forms)

The Forms package lets you generate forms as well as fields with standard make commands. Inside your forms for models you can specify the fields that need to be generated and then simply pass the form to the view. No more writing html forms, error handling etc. It can handle Eloquent relationships and easily work with ajax requests for more dynamic form submissions.

##### Author(s):
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), mattlantz at gmail dot com)

## Requirements

1. PHP 7.3+
2. OpenSSL

## Compatibility and Support

| Laravel Version | Package Tag | Supported |
|-----------------|-------------|-----------|
| ^7.x | 4.x | yes |
| ^7.x | 3.x | no |
| ^5.8.x - 7.x | 2.x | no |
| 5.4.x - 5.8.x | 1.3.x | no |
| 5.4.x | 1.1.x | no |
| 5.3.x | 1.0.x | no |

### Installation

Start a new Laravel project:
```php
composer create-project laravel/laravel your-project-name
```

Then run the following to add Forms
```php
composer require "grafite/forms"
```

Time to publish those assets!
```php
php artisan vendor:publish --provider="Grafite\Forms\FormsProvider"
```

## Documentation

[https://docs.grafite.ca/utilities/forms](https://docs.grafite.ca/utilities/forms)

## Upgrading from 3.x to 4.x (Renaming)
The package was renamed in version 3 to 4. This means that the following would need to be changed on your code base:

`form-maker.php` -> `forms.php`

`Grafite\FormMaker` -> `Grafite\Forms`

`@formMaker` -> `@forms`

`<x-f></x-f>` -> `<x-fm></x-fm>`

`<x-f-action></x-f-action>` -> `<x-fm-action></x-fm-action>`

`<x-f-delete></x-f-delete>` -> `<x-fm-delete></x-fm-delete>`

`<x-f-search></x-f-search>` -> `<x-fm-search></x-fm-search>`

## License
Forms is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
