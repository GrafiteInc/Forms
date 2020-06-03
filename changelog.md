# Change Log - FormMaker
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).
----

## [v2.12.1] - 2020-06-03

### Added
- Div and Heading tags for HTML Snippets

## [v2.12.0] - 2020-05-29

### Added
- New Html Snippets for field spacing

## [v2.11.1] - 2020-05-15

### Changed
- Submits are now buttons instead of inputs

## [v2.11.0] - 2020-05-14

### Added
- New Dropzone field
- New FileWithPreview field

### Changed
- Refactored Typeahead for more customization

### Fixed
- Issue with template name variable
- Issue with wanting no buttons

## [v2.10.0] - 2020-05-12

### Added
- New field templates
- New typeahead field

### Changed
- Minor updates for doc blocks

## [v2.9.1] - 2020-05-08

### Fixed
- Issue with naming in Bootstrap HasOne

## [v2.9.0] - 2020-05-06

### Added
- New datepicker field

## [v2.8.0] - 2020-05-06

### Added
- Custom styles for fields
- Slug Field
- Tags Field
- Bootstrap Select Fields
- Bootstrap Toggle Field

### Fixed
- Issue with IDs having spaces

## [v2.7.1] - 2020-05-02

### Fixed
- Issue with PHP 7.2 and EOT

## [v2.7.0] - 2020-04-22

### Added
- Javascript and style asset injecting

### Changed
- Another hard written bootstrap class

## [v2.6.2] - 2020-04-16

### Fixed
- Issue with improper case

## [v2.6.1] - 2020-04-13

### Fixed
- Issue with missing default button class
- Issue with default classes
- Issue with false labels

## [v2.6.0] - 2020-04-12

### Added
- Config options for all remaining classes

### Changed
- Code formating is now PSR12

### Fixed
- Issue with custom form classes and horizontal

## [v2.5.1] - 2020-04-10

### Fixed
- Minor issue with spelling

## [v2.5.0] - 2020-04-09

### Added
- Method for accessing rendered fields without buttons and form wrappers

### Fixed
- Issue with no label on checkboxes

## [v2.4.6] - 2020-04-09

### Changed
- Improved default label naming

## [v2.4.5] - 2020-04-09

### Changed
- Location of field validaiton
- Improved model instance access

### Fixed
- Issue with attribute 'value' as array

## [v2.4.4] - 2020-04-07

### Added
- Ability to access the model instance in a model form

## [v2.4.3] - 2020-04-07

### Added
- The ability to set the form class as a property

## [v2.4.2] - 2020-03-04

### Added
- Support for Laravel 7.x

## [v2.4.1] - 2020-01-31

### Fixed
- Issue with routes being arrays
-  or having array parameters

## [v2.4.0] - 2020-01-31

### Added
- Ability to set values of Fields

### Fixed
- More bootstrap dependency removal

## [v2.3.1] - 2020-01-25

### Fixed
- Corrected some missing bootstrap hardcoded classes

## [v2.3.0] - 2020-01-22

### Added
- New config options allowing for no bootstrap based classes

## [v2.2.1] - 2020-01-21

### Fixed
- Minor issue with not escaping values

## [v2.2.0] - 2020-01-16

### Added
- New null value option for selects and relationship fields

## [v2.1.11] - 2019-11-29

### Fixed
- Minor issue with boolean value

## [v2.1.10] - 2019-10-10

### Fixed
- Issue with supporting custom tags

## [v2.1.9] - 2019-10-10

### Fixed
- Issue with missing directory for Fields

## [v2.1.8] - 2019-09-06

### Added
- Laravel 6.0 compatibility

## [v2.1.7] - 2019-08-27

### Fixed
- Issue with wrapping Hidden fields with label

## [v2.1.6] - 2019-08-26

### Added
- New section based column layout

## [v2.1.5] - 2019-08-24

### Added
- New custom confirm method for JS

## [v2.1.4] - 2019-07-31

### Fixed
- Label errors tag

## [v2.1.3] - 2019-07-31

### Changed
- General code improvements

### Fixed
- Issue with base form maker

## [v2.1.2] - 2019-07-31

### Added
- New base form maker command
- New form sections

### Changed
- Save key is now submit

### Fixed
- Standard route consistency
- Array values for checkboxes

## [v2.1.1] - 2019-07-14

### Added
- New setRoute method

## [v2.1.0] - 2019-07-02

### Added
- New BaseForm - for non model based forms

## [v2.0.3] - 2019-06-30

### Added
- Made cancel buttons optional
- Better descriptions in stubs

## [v2.0.2] - 2019-06-18

### Changed
- Php versions

## [v2.0.1] - 2019-06-18

### Fixed
- PHP versions for tests

## [v2.0.0] - 2019-06-18

### Added
- New Fields classes
- New Form class
- New ModelForm class

### Changed
- FormMaker now creates form from ModelForm classes rather than arrays and config files

### Removed
- LaravelCollective packages
- InputMaker
- Blade Directives
- some Helpers

## [v1.3.5] - 2019-06-18

### Fixed
- Issue with 5.8 compatibility

## [v1.3.4] - 2018-08-28

### Added
- Ability to handle arrays for custom attributes

## [v1.3.3] - 2018-07-02

### Fixed
- Issue with non array attributes

## [v1.3.2] - 2018-04-19

### Fixed
- Allows customizable for attribute on labels

## [v1.3.1] - 2018-04-14

### Added
- New customizable Ids

### Fixed
- Issue with bootstrap input wrapping

## [v1.3.0] - 2018-03-19

### Changed
- New branding

## [v1.2.10] - 2018-03-06

### Changed
- Makes checkboxes bootstrap 4 compatible

## [v1.2.9] - 2018-02-27

### Fixed
- Issue with null relationships

## [v1.2.9] - 2018-01-25

### Fixed
- Fixed issue with null values for relationships

## [v1.2.8] - 2018-01-09

### Fixed
- Issue with dates as strings

## [v1.2.7] - 2018-01-02

### Fixed
- Dates and relationships
- Minor coorection for checkboxes

## [v1.2.6] - 2017-11-27

### Added
- New name option
- New set columns layout option

### Fixed
- Issue with relationships
- Issue with horizontal selects

## [v1.2.5] - 2017-11-25

### Fixed
- Fixed issue for double quotes

## [v1.2.4] - 2017-10-30

### Added
- New theme support

### Changed
- Version support

## [v1.2.3] - 2017-10-27

### Added
- Nullable option for dropdowns

## [v1.2.2] - 2017-10-24

### Fixed
- Issue with default class for checkboxes

## [v1.2.1] - 2017-10-16

### Added
- Nullable option for dropdowns

### Fixed
- Exception with relationships
- Custom class config

## [v1.2.0] - 2017-08-31

### Changed
- Laravel 5.5 support

## [v1.1.4] - 2017-08-25

### Fixed
- Minor issue with test setup

## [v1.1.3] - 2017-08-25

### Added
- Support for horizontal forms
- Support for multiple selects and hasMany and belongsToMany relationships

### Fixed
- Minor dbal issue

## [v1.1.2] - 2017-05-15

### Changed
- Dropped forcing first letter uppercase on labels etc

### Fixed
- Issue with html class wrappers

## [v1.1.1] - 2017-03-29

### Fixed
- Issue with deleted_at columns

## [v1.1.0] - 2017-01-27

### Changed
- Laravel 5.4 compatibility

## [v1.0.15] - 2017-01-24

### Added
- New default_value config

### Changed
- Set compatibility guide
- Minor CS changes

### Fixed
- Issue with null fields

## [v1.0.14] - 2017-01-09

### Fixed
- Repaired issue with certain number values

## [v1.0.13] - 2016-12-20

### Fixed
- Issue with setting columns in fromTable()

## [v1.0.12] - 2016-12-03

### Changed
- getTableColumns moved connection to class level setting

### Fixed
- Improved column reading
- Fixed issue with multiple selects

## [v1.0.11] - 2016-11-27

### Fixed
- Fixes an issue with setting the default connection

## [v1.0.10] - 2016-11-26

### Added
- Now supports multiple select for select inputs

### Changed
- Improved the nesting detection and tests

### Fixed
- Issue with nested properties in names

## [v1.0.9] - 2016-11-17

### Fixed
- Tagging realignment

## [v1.0.8] - 2016-11-16

### Added
- Now you can set the connection for the table
- More unit tests

## [v1.0.7] - 2016-10-28

### Fixed
- Issue with filling in textareas with names
- Removed underscores from placeholders

## [v1.0.6] - 2016-10-20

### Changed
- Allowing alternate relationship names

## [v1.0.5] - 2016-10-13

### Fixed
- Issue with classes

## [v1.0.4] - 2016-08-26

### Fixed
- Composer fixes

## [v1.0.3] - 2016-08-24

### Added
- Support for Laravel 5.3

## [v1.0.2] - 2016-07-28

### Added
- New ability to specify methods and params for relationship inputs
- More unit tests

## [v1.0.1] - 2016-07-24

### Changed
- Minor testing improvements

## [v1.0.0] - 2016-07-18

### Changed
- Initial build separating from Laracogs