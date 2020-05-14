![Grafite FormMaker](GrafiteFormMaker-banner.png)

**FormMaker** - An amazing Form generating component for Laravel.

[![Build Status](https://github.com/GrafiteInc/FormMaker/workflows/PHP%20Package%20Tests/badge.svg?branch=master)](https://github.com/GrafiteInc/FormMaker/actions?query=workflow%3A%22PHP+Package+Tests%22)
[![Maintainability](https://api.codeclimate.com/v1/badges/8c00a046fec32d8b8ac7/maintainability)](https://codeclimate.com/github/GrafiteInc/FormMaker/maintainability)
[![Packagist](https://img.shields.io/packagist/dt/grafite/formmaker.svg)](https://packagist.org/packages/grafite/formmaker)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/grafite/formmaker)

The FormMaker package lets you generate forms as well as fields with standard make commands. Inside your forms for models you can specify the fields that need to be generated and then simply pass the form to the view. No more writing html forms, error handling etc. It can handle Eloquent relationships and easily work with ajax requests for more dynamic form submissions.

##### Author(s):
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), mattlantz at gmail dot com)

## Requirements

1. PHP 7.2+
2. OpenSSL

## Compatibility and Support

| Laravel Version | Package Tag | Supported |
|-----------------|-------------|-----------|
| ^5.8.x - 7.x | 2.x | yes |
| 5.4.x - 5.8.x | 1.3.x | no |
| 5.4.x | 1.1.x | no |
| 5.3.x | 1.0.x | no |

### Installation

Start a new Laravel project:
```php
composer create-project laravel/laravel your-project-name
```

Then run the following to add FormMaker
```php
composer require "grafite/formmaker"
```

Time to publish those assets!
```php
php artisan vendor:publish --provider="Grafite\FormMaker\FormMakerProvider"
```

## Documentation

[https://docs.grafite.ca/utilities/form_maker](https://docs.grafite.ca/utilities/form_maker)

## License
FormMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
