# FormMaker

**FormMaker** - A remarkably magical form and input maker tool for Laravel.

[![Build Status](https://travis-ci.org/YABhq/FormMaker.svg?branch=master)](https://travis-ci.org/YABhq/FormMaker)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/YABhq/FormMaker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/YABhq/FormMaker/?branch=develop)
[![Packagist](https://img.shields.io/packagist/dt/yab/formmaker.svg)](https://packagist.org/packages/yab/formmaker)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/yab/formmaker)

The FormMaker package provides a set of tools for generating HTML forms with as little as 1 line of code. Don't want to write boring HTML, neither do we. The FormMaker will generate error containers, all fields defined by either the table or object column types, or if you prefer to have more control define a config. In the case that you want to write more than 1 line of code, FormMaker comes with the InputMaker service as well. With the InputMaker you can create any form of input, including html for Eloquent relationships.

##### Author(s):
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), matt at yabhq dot com)
* [Chris Blackwell](https://github.com/chrisblackwell) ([@chrisblackwell](https://twitter.com/chrisblackwell), chris at yabhq dot com)

## Requirements

1. PHP 5.6+
2. OpenSSL

## Compatability and Support

| Laravel Version | Package Tag | Supported |
|-----------------|-------------|-----------|
| 5.4.x | 1.1.x | yes |
| 5.3.x | 1.0.x | no |

### Installation

Start a new Laravel project:
```php
composer create-project laravel/laravel your-project-name
```

Then run the following to add FormMaker
```php
composer require "yab/formmaker"
```

Add this to the `config/app.php` in the providers array:
```php
Yab\FormMaker\FormMakerProvider::class
```

Time to publish those assets!
```php
php artisan vendor:publish --provider="Yab\FormMaker\FormMakerProvider"
```

## Documentation

[https://laracogs.com/docs/services/form_maker](https://laracogs.com/docs/services/form_maker)<br>
[https://laracogs.com/docs/services/input_maker](https://laracogs.com/docs/services/input_maker)

## License
FormMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
