# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [4.4.0] - xxxx-xx-xx
- Added support for `UserFrosting/i18n` 4.4.0
- Fix bad param when loading YAML from `RequestSchema`
- Enables extension of the `RequestDataTransformer` class by allowing implementation of a custom `transformField` function ([#29])

## [4.3.0] - 2019-06-22
- Dropping support for PHP 5.6 & 7.0
- Updated dependencies
- Updated PHPUnit to 7.5

## [4.2.2] - 2019-03-28
- Fix error if custom validator doesn't provides a message.
- Removed broken and deprecated `RequestSchema::loadSchema` method.
- 100% Test coverage ([#24])

## [4.2.1] - 2019-01-13
- Fix issue with ResourceLocator

## [4.2.0] - 2019-01-13
- Updated Dependencies for 4.2

## [4.1.3] - 2019-01-10
### Fixed
- Fix warning with PHP 7.3

## [4.1.2] - 2018-11-13
### Fixed
- Updated Run Method to add NameSpace Array ([#23](https://github.com/userfrosting/fortress/pull/23))

## [4.1.1] - 2017-07-10
### Fixed
- Properly recognize schema keys with no content in RequestDataTransformer ([userfrosting/UserFrosting#766](https://github.com/userfrosting/UserFrosting/issues/766))

## [4.1.0] - 2017-06-18
### Changed
- Factor out schema loading, path building, and repository to use userfrosting/support components

### Added
- Unit tests

## [4.0.1] - 2017-02-27
### Added
- Implement equals, not_equals, telephone, uri, and username rules


[4.3.1]: https://github.com/userfrosting/fortress/compare/4.3.0...4.3.1
[4.3.0]: https://github.com/userfrosting/fortress/compare/4.2.2...4.3.0
[4.2.2]: https://github.com/userfrosting/fortress/compare/4.2.1...4.2.2
[4.2.1]: https://github.com/userfrosting/fortress/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/userfrosting/fortress/compare/4.1.2...4.2.0
[4.1.3]: https://github.com/userfrosting/fortress/compare/4.1.2...4.1.3
[4.1.2]: https://github.com/userfrosting/fortress/compare/v4.1.1...4.1.2
[4.1.1]: https://github.com/userfrosting/fortress/compare/4.1.0...v4.1.1
[4.1.0]: https://github.com/userfrosting/fortress/compare/4.0.1...4.1.0
[4.0.1]: https://github.com/userfrosting/fortress/compare/4.0.0...4.0.1
[#24]: https://github.com/userfrosting/fortress/issues/24
[#29]: https://github.com/userfrosting/fortress/pull/29
