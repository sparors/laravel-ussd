# Changelog

All notable changes to `laravel Ussd` will be documented in this file.

## [Unreleased]

## [v2.5.0] - 2022-06-19
### Added
- Add configuring USSDs using decorator pattern.

## [v2.4.2] - 2022-06-12
### Changed
- Change Action class `setRecord` method to return `$this`

## [v2.4.1] - 2022-03-31
### Fixed
- Clean up

## [v2.4.0] - 2022-02-22

### Added
- Add Laravel 9 support
- Add PHP 8.1 support

## [v2.3.1] - 2021-10-15

### Fixed
- Coding style

## [v2.3.0] - 2021-06-27
### Added
- Add missen test to improve coverage
### Change
- Minor bug fixes
- Upgrade test dependencies


## [v2.2.0] - 2020-09-11
### Added
- Add Support for laravel 8

## [v2.1.0] - 2020-05-26
### Change
- Initial State method of machine can accept action
- All internal private methods to protected

## [v2.0.0] - 2020-05-24
### Added
- Action class to run application logics
- Artisan command to create action class
- increment method to records
- decrement method to records
### Changed
- config file class namespace split to action and state namespace
- Updated changelog
- Readme
- machine class now runs ussd actions
- Updated contributing

## [v1.0.0] - 2020-05-12
### Added
- More test
- Machine SetInitialState can take callable
### Changed
- State type changed to action
- License File changed to github format
- Updated Readme
- Updated changelog

## [v0.1.0] - 2020-05-02
### Added
- Ussd Package Project with README, contributing, changelog, license, etc.
- State class to define what should occur at various stages when user navigates
- Artisan command to create State classes
- Machine class to run all linked States
- HasManipulators traits to help Machine Class with common functions
- Menu class to be used create user menus in the various states
- Decision class to decide on how to link the various states after accepting user's input
- Record class to save data
- Ussd Class to provide access other classes
- Ussd facade to proxy to the Ussd class
- Ussd config to allow developers customize behaviour
- Ussd service Provider class to allow laravel know how to integrate the package

[Unreleased]: ../../compare/v2.5.0...HEAD
[v2.5.0]: ../../compare/v2.4.2...v2.5.0
[v2.4.2]: ../../compare/v2.4.1...v2.4.2
[v2.4.1]: ../../compare/v2.4.0...v2.4.1
[v2.4.0]: ../../compare/v2.3.1...v2.4.0
[v2.3.1]: ../../compare/v2.3.0...v2.3.1
[v2.3.0]: ../../compare/v2.2.0...v2.3.0
[v2.2.0]: ../../compare/v2.1.0...v2.2.0
[v2.1.0]: ../../compare/v2.0.0...v2.1.0
[v2.0.0]: ../../compare/v1.0.0...v2.0.0
[v1.0.0]: ../../compare/v0.1.0...v1.0.0
[v0.1.0]: ../../releases/tag/v0.1.0
