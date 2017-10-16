# Change Log - FormMaker
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).
----

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