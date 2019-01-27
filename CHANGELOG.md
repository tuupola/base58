# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [0.2.3](https://github.com/tuupola/base58/compare/0.2.2...0.2.3) - 2019-01-21
### Fixed
- Typo in homepage url caused `composer.json` to fail validation

## [0.2.2](https://github.com/tuupola/base58/compare/0.2.1...0.2.2) - 2019-01-21
### Added
- Improved tests synced from tuupola/base62

## [0.2.1](https://github.com/tuupola/base58/compare/0.2.0...0.2.1) - 2018-09-11
### Fixed
- GMP driver output was not matching others when binary data had leading 0x00

## [0.2.0](https://github.com/tuupola/base58/compare/0.1.0...0.2.0) - 2018-09-11

### Fixed
- Leading 0x00 was stripped from binary data ([#1](https://github.com/tuupola/base58/issues/1), [#3](https://github.com/tuupola/base58/pull/3))

### Removed
- Support for PHP 5.5.

### Added
- Implicit `decodeInteger()` and `encodeInteger()` methods.

## 0.1.0 - 2017-07-09

Initial release.
