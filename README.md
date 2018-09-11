# Base58

This library implements Base58 encoding. In addition to integers it can encode and decode any arbitrary data. That said, Base58 is well suited for decoding big integers but is not designed to decode long portions of binary data.

[![Latest Version](https://img.shields.io/packagist/v/tuupola/base58.svg?style=flat-square)](https://packagist.org/packages/tuupola/base58)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/base58/master.svg?style=flat-square)](https://travis-ci.org/tuupola/base58)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/base58.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/base58)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/base58.svg?style=flat-square)](https://codecov.io/github/tuupola/base58)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base58
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
$base58 = new Tuupola\Base58;

$encoded = $base58->encode(random_bytes(128));
$decoded = $base58->decode($encoded);
```

Note that if you are encoding to and from integer you need to pass boolean `true` as the second argument for `decode()` method. This is because `decode()` method does not know if the original data was an integer or binary data.

``` php
$integer = $base58->encode(987654321); /* 1TFvCj */
print $base58->decode("1TFvCj", true); /* 987654321 */
```

If you prefer you can also use the implicit `decodeInteger()` method.

``` php
$integer = $base58->encode(987654321); /* 1TFvCj */
print $base58->decodeInteger("1TFvCj"); /* 987654321 */
```

Also note that encoding a string and an integer will yield different results.

``` php
$integer = $base58->encode(987654321); /* 1TFvCj */
$string = $base58->encode("987654321"); /* gE62MGeOBMPt */
```

## Character sets

By default Base58 uses GMP style character set. Shortcuts are provided for [Bitcoin](https://github.com/bitcoin/bitcoin/blob/master/src/base58.cpp), [Flickr](https://www.flickr.com/groups/api/discuss/72157616713786392/), [Ripple](https://wiki.ripple.com/Accounts) and [IPFS](https://github.com/richardschneider/net-ipfs-core#base58) character sets. You can also use any custom 58 characters.

```php
use Tuupola\Base58;

print Base58:GMP /* 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv */
print Base58:BITCOIN /* 123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz */
print Base58:FLICKR /* 123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ */
print Base58:RIPPLE /* rpshnaf39wBUDNEGHJKLM4PQRST7VWXYZ2bcdeCg65jkm8oFqi1tuvAxyz */
print Base58:IPFS /* 123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz */

$default = new Base58(["characters" => Base58:GMP]);
$bitcoin = new Base58(["characters" => Base58:BITCOIN]);
print $default->encode("Hello world!");/* 1LDlk6QWOejX6rPrJ */
print $bitcoin->encode("Hello world!"); /* 2NEpo7TZRhna7vSvL */
```

## Speed

Install GMP if you can. It is much faster pure PHP encoder. Below benchmarks are for encoding `random_bytes(128)` data. BCMatch encoder is also included but it is mostly just a curiosity. It is too slow to be usable.

```
$ composer phpbench

+-----------------------+-----------------+--------------+
| subject               | mean            | diff         |
+-----------------------+-----------------+--------------+
| benchGmpEncoder       | 70,821.530ops/s | 0.00%        |
| benchGmpEncoderCustom | 64,683.053ops/s | +9.49%       |
| benchPhpEncoder       | 25.801ops/s     | +274,387.68% |
| benchBcmathEncoder    | 7.505ops/s      | +943,548.44% |
+-----------------------+-----------------+--------------+
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
