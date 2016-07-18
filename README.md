# FormMaker

**FormMaker** - A remarkably magical form and input maker tool for Laravel.

[![Codeship](https://img.shields.io/codeship/30f7f800-2f40-0134-aa06-4a25dba64f1f.svg?maxAge=2592000)](https://packagist.org/packages/yab/formmaker)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/YABhq/FormMaker/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/YABhq/FormMaker/?branch=develop)
[![Packagist](https://img.shields.io/packagist/dt/yab/formmaker.svg?maxAge=2592000)](https://packagist.org/packages/yab/formmaker)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?maxAge=2592000)](https://packagist.org/packages/yab/formmaker)

The FormMaker package provides a set of tools for generating HTML forms with as little as 1 line of code. Don't want to write boring HTML, neither do we. The FormMaker will generate error containers, all fields defined by either the table or object column types, or if you prefer to have more control define a config.

##### Author(s):
* [Matt Lantz](https://github.com/mlantz) ([@mattylantz](http://twitter.com/mattylantz), matt at yabhq dot com)
* [Chris Blackwell](https://github.com/chrisblackwell) ([@chrisblackwell](https://twitter.com/chrisblackwell), chris at yabhq dot com)

## Requirements

1. PHP 5.6+
2. OpenSSL
3. Laravel 5.1+

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

----

# FormMaker Guide

## Blade Directives

```
@form_maker_table()
@form_maker_object()
@form_maker_array()
@form_maker_columns()
```

## Helpers

```
form_maker_table()
form_maker_object()
form_maker_array()
form_maker_columns()
```

## Facades

```
FormMaker::fromTable()
FormMaker::fromObject()
FormMaker::fromArray()
FormMaker::getTableColumns()
```

## Common Components

## Simple Fields

These components are the most simplistic:

```
class: 'a css class'
reformatted: true|false // Reformats the column name to remove underscores etc
populated: true|false // Fills in the form with values
idAndTimestamps: true|false // ignores the id and timestamp columns
```

### Columns

Columns is an array of the following nature which can be used in place of the columns component in any of the fromX methods:

```
[
    'id' => [
        'type' => 'hidden',
    ],
    'name' => [
        'type' => '', // defaults to standard text input
        'placeholder' => 'User Name Goes Here!',
        'alt_name' => 'User Name',
        'custom' => 'custom DOM attributes etc',
        'class' => 'css class names',
        'before' => '<span class="input-group-addon" id="basic-addon1">@</span>',
        'after' => '<span class="input-group-addon" id="basic-addon2">@example.com</span>',
    ],
    'job' => [
        'type' => 'select',
        'alt_name' => 'Your Job',
        'custom' => 'multiple', // custom attributes like multiple, disabled etc
        'options' => [
            'key 1' => 'value_1',
            'key 2' => 'value_2',
        ]
    ],
    'roles' => [
        'type' => 'relationship',
        'class' => 'App\Repositories\Roles\Roles',
        'label' => 'name' // the field for the label in the select input generated
    ]
]
```

Types supported in the Column Config:

* text (converts to textarea)
* password
* checkbox
* checkbox-inline
* radio
* select
* hidden
* number
* float
* decimal

_** If no type is set the FormMaker will default to a standard text input_

Columns with the following names will not be displayed by default: id, created_at, updated_at. You would need to override this setting in the creation of the form.

### View

You can create a custom view that the FormMaker will use: This is an example:

```
<div class="row">
    <div class="form-group {{ $errorHighlight }}">
        <label class="control-label" for="{!! $labelFor !!}">{!! $label !!}</label>
        <div class="row">
            {!! $input !!}
        </div>
    </div>
    {!! $errorMessage !!}
</div>
```

## fromTable()

```
FormMaker::fromTable($table, $columns = null, $class = 'form-control', $view = null, $reformatted = true, $populated = false, $idAndTimestamps = false)
```

### Example:

```
FormMaker::fromTable('users')
```

The fromTable method will crawl the specified table and build the form out of the columns and types of coloumns. You can freely customize it (see below) the basic above example will result in:

```
<div class="form-group ">
    <label class="control-label" for="Name">Name</label>
    <input  id="Name" class="form-control" type="" name="name" placeholder="Name">
</div>
<div class="form-group ">
    <label class="control-label" for="Email">Email</label>
    <input  id="Email" class="form-control" type="" name="email" placeholder="Email">
</div>
<div class="form-group ">
    <label class="control-label" for="Password">Password</label>
    <input  id="Password" class="form-control" type="" name="password" placeholder="Password">
</div>
<div class="form-group ">
    <label class="control-label" for="Remember_token">Remember Token</label>
    <input  id="Remember_token" class="form-control" type="" name="remember_token" placeholder="Remember Token">
</div>
```

## fromObject()

Within the same rules as above we can rather than provide a table string we can insert an object such as `Auth::user()` or any single object retrieved from the database.

```
fromObject($object, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
```

## fromArray()

From array works in the same context as fromTable, and fromObject, we're able to in this case provide a simple array list of properties. The key difference with fromArray is that we can provide these in one of two formats:

```
[ 'name', 'birthday' ]
```

OR

```
[ 'name' => 'string', 'birthday' => 'date' ]
```

The full list of field types compatible are:

* integer
* string
* datetime
* date
* float
* binary
* blob
* boolean
* datetimetz
* time
* array
* json_array
* object
* decimal
* bigint
* smallint

```
fromArray($array, $columns = null, $view = null, $class = 'form-control', $populated = true, $reformatted = false, $idAndTimestamps = false)
```

## getTableColumns()

The getTableColumns method utilizes Doctrines Dbal component to map your database table and provide the columns and types. This is perfect initial builds of an editor form off an object.

Example:

```
FormMaker::fromObject(Books::find(1), FormMaker::getFromColumns('books'))
```

This will build the form off the columns of the table. Though the fromObject will scan through the object, but providing the table columns as the columns input allows the inputs to be set to thier correct type.
```

----

# InputMaker Guide

The nice part about the input maker is that its the core of the form maker only pulled out. So this way you can reduce your HTML writing significanly with its blade directives or helpers.

## Blade Directives

```
@input_maker_label()
@input_maker_create()
```

## Helpers

```
input_maker_label()
input_maker_create()
```

## Facades

```
InputMaker::label()
InputMaker::create()
```

## Common Components

## Simple Fields For Everything!

The label generator is the easiest:

```
input_maker_label('name', ['class' => 'something'])
```

The input generator has a few more parts:

```
input_maker_create($name, $field, $object = null, $class = 'form-control', $reformatted = false, $populated = true)
```

The $field paramter is an array which can be highly configured.

### Example $feild Config

```
[
    'type' => '', // defaults to standard text input
    'placeholder' => 'User Name Goes Here!',
    'alt_name' => 'User Name',
    'custom' => 'custom DOM attributes etc',
    'class' => 'css class names',
    'before' => '<span class="input-group-addon" id="basic-addon1">@</span>',
    'after' => '<span class="input-group-addon" id="basic-addon2">@example.com</span>',
]
```

For Relationships:
```
[
    'model' => 'Full class as string',
    'label' => 'visible name for the options',
    'value' => 'value for the options',
]

Example without User:
@input_maker_create('roles', ['type' => 'relationship', 'model' => 'App\Repositories\Role\Role', 'label' => 'label', 'value' => 'name'])

Example with User:
@input_maker_create('roles', ['type' => 'relationship', 'model' => 'App\Repositories\Role\Role', 'label' => 'label', 'value' => 'name'], $user)
```

Types supported in the Config:

* string
* text (converts to textarea)
* password
* checkbox
* checkbox-inline
* radio
* select
* hidden
* number
* float
* decimal
* relationship

_** If no type is set the InputMaker will default to a standard text input_

----

## License
FormMaker is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Bug Reporting and Feature Requests
Please add as many details as possible regarding submission of issues and feature requests

### Disclaimer
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
