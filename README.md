# Base58

This library implements Base58 encoding. In addition to integers it can encode and decode any arbitrary data. That said, Base58 is well suited for decoding big integers but is not designed to decode long portions of binary data.

[![Latest Version](https://img.shields.io/packagist/v/tuupola/base58.svg?style=flat-square)](https://packagist.org/packages/tuupola/base58)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/workflow/status/tuupola/base58/Tests/2.x?style=flat-square)](https://github.com/tuupola/base58/actions)[![Coverage](https://img.shields.io/codecov/c/github/tuupola/base58.svg?style=flat-square)](https://codecov.io/github/tuupola/base58)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base58
```

This branch requires PHP 7.1 or up. The older 1.x branch supports also PHP 5.6 and 7.0.

``` bash
$ composer require "tuupola/base58:^1.0"
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
$base58 = new Tuupola\Base58;

$encoded = $base58->encode(random_bytes(128));
$decoded = $base58->decode($encoded);
```

If you are encoding to and from integer use the implicit decodeInteger() and encodeInteger() methods.

``` php
$integer = $base58->encodeInteger(987654321); /* 1TFvCj */
print $base58->decodeInteger("1TFvCj", true); /* 987654321 */
```

Also note that encoding a string and an integer will yield different results.

``` php
$string = $base58->encode("987654321"); /* gE62MGeOBMPt */
$integer = $base58->encodeInteger(987654321); /* 1TFvCj */
```

## Character sets

By default Base58 uses GMP style character set. Shortcuts are provided for [Bitcoin](https://github.com/bitcoin/bitcoin/blob/master/src/base58.cpp), [Flickr](https://www.flickr.com/groups/api/discuss/72157616713786392/), [Ripple](https://wiki.ripple.com/Accounts) and [IPFS](https://github.com/richardschneider/net-ipfs-core#base58) character sets. You can also use any custom 58 characters.

```php
use Tuupola\Base58;

print Base58::GMP /* 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv */
print Base58::BITCOIN /* 123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz */
print Base58::FLICKR /* 123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ */
print Base58::RIPPLE /* rpshnaf39wBUDNEGHJKLM4PQRST7VWXYZ2bcdeCg65jkm8oFqi1tuvAxyz */
print Base58::IPFS /* 123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz */

$default = new Base58(["characters" => Base58::GMP]);
$bitcoin = new Base58(["characters" => Base58::BITCOIN]);
print $default->encode("Hello world!"); /* 1LDlk6QWOejX6rPrJ */
print $bitcoin->encode("Hello world!"); /* 2NEpo7TZRhna7vSvL */
```

## Base58Check support

This library supports the [Base58Check](https://en.bitcoin.it/wiki/Base58Check_encoding) encoding used in Bitcoin addresses. Decoding validates both version and the checksum. If either of them fails a `RuntimeException` will be thrown;

```php
use Tuupola\Base58;

$base58check = new Base58([
    "characters" => Base58::BITCOIN,
    "check" => true,
    "version" => 0x00
]);

print $base58check->encode("Hello world!"); /* 19wWTEnNTWna86WmtFsTAr5 */

try {
    $base58check->decode("19wWTEnNTWna86WmtFsTArX");
} catch (RuntimeException $exception) {
    /* Checksum "84fec52c" does not match the expected "84fec512" */
    print $exception->getMessage();
}
```

## Speed

Install GMP if you can. It is much faster pure PHP encoder. Below benchmarks are for encoding `random_bytes(128)` data. BCMatch encoder is also included but it is mostly just a curiosity. It is too slow to be usable.

```
$ vendor/bin/phpbench run benchmarks/ --report=default

+-----------------------+------------------+--------------+
| subject               | mean             | diff         |
+-----------------------+------------------+--------------+
| benchGmpEncoder       | 101,832.994ops/s | 0.00%        |
| benchGmpEncoderCustom | 97,656.250ops/s  | +4.28%       |
| benchPhpEncoder       | 305.913ops/s     | +33,188.19%  |
| benchBcmathEncoder    | 32.457ops/s      | +313,643.79% |
+-----------------------+------------------+--------------+
```

## Static Proxy

If you prefer to use static syntax use the provided static proxy.

``` php
use Tuupola\Base58Proxy as Base58;

$encoded = Base58::encode(random_bytes(128));
$decoded = Base58::decode($encoded);
```

## Testing

You can run tests either manually or automatically on every code change. Automatic tests require [entr](http://entrproject.org/) to work.

``` bash
$ make test
```
``` bash
$ brew install entr
$ make watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
