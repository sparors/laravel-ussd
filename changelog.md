# Changelog

All notable changes to `laravel Ussd` will be documented in this file.

## [Unreleased]
### Changed
- Add more test
- License File changed to github format
- Updated Readme

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

[Unreleased]: ../../compare/v0.1.0...HEAD
[v0.1.0]: ../../releases/tag/v0.1.0
