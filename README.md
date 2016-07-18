# FormMaker

**FormMaker** - A remarkably magical form maker tool for Laravel.

The FormMaker package provides a set of tools for generating HTML forms with as little as 1 line of code. Don't want to write boring HTML, neither do we. The FormMaker will generate error containers, all fields defined by either the table or object column types, or if you prefer to have more control define a config.

##### Author(s):
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), matt at yabhq dot com)
* [Chris Blackwell](https://github.com/chrisblackwell) ([@chrisblackwell](https://twitter.com/chrisblackwell), chris at yabhq dot com)

## Detailed Documentation
Please consult the documentation here: [http://laracogs.com/docs/Utilties/FormMaker](http://laracogs.com/docs/Utilties/FormMaker)

## Requirements

1. PHP 5.6+
2. OpenSSL
3. Laravel 5.1+

----

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

##### After these few steps you have the following tools at your fingertips:

The package comes with facades, helpers, and blade directives.

#### FormMaker Facades
```php
FormMaker::fromTable($table, $columns = null, $class = 'form-control', $view = null, $reformatted = true, $populated = false, $idAndTimestamps = false)
FormMaker::fromObject($object, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
FormMaker::fromArray($array, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
```

#### InputMaker Facades
```php
InputMaker::label($name, $attributes = [])
InputMaker::create($name, $field, $object = null, $class = 'form-control', $reformatted = false, $populated = false)
```

#### FormMaker Blade
```php
@form_maker_table($table, $columns = null, $class = 'form-control', $view = null, $reformatted = true, $populated = false, $idAndTimestamps = false)
@form_maker_object($object, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
@form_maker_array($array, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
```

#### InputMaker Blade
```php
@input_maker_label($name, $attributes = [])
@input_maker_create($name, $field, $object = null, $class = 'form-control', $reformatted = false, $populated = false)
```

## License
FormMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
