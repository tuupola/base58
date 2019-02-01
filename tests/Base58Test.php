<?php

/*

Copyright (c) 2017-2019 Mika Tuupola

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

/**
 * @see       https://github.com/tuupola/base58
 * @license   https://www.opensource.org/licenses/mit-license.php
 */

namespace Tuupola\Base58;

use InvalidArgumentException;
use RuntimeException;
use Tuupola\Base58;
use Tuupola\Base58Proxy;
use PHPUnit\Framework\TestCase;

class Base58Test extends TestCase
{
    protected function tearDown()
    {
        Base58Proxy::$options = [
            "characters" => Base58::GMP,
        ];
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeRandomBytes($characters)
    {
        $data = random_bytes(128);

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base58->decode($encoded4));
        $this->assertEquals($data, Base58Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeIntegers($characters)
    {
        $data = 987654321;

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded3 = $bcmath->encodeInteger($data);
        $encoded4 = $base58->encodeInteger($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $bcmath->decodeInteger($encoded3));
        $this->assertEquals($data, $base58->decodeInteger($encoded4));
        $this->assertEquals($data, Base58Proxy::decodeInteger($encoded5));
    }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base58)->encode($data);
        $decoded = (new Base58)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeWithLeadingZero($characters)
    {
        $data = hex2bin("07d8e31da269bf28");

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base58->decode($encoded4));
        $this->assertEquals($data, Base58Proxy::decode($encoded5));
    }

    public function testShouldUseDefaultCharacterSet()
    {
        $data = "Hello world!";

        $php = new PhpEncoder();
        $gmp = new GmpEncoder();
        $bcmath = new BcmathEncoder();
        $base58 = new Base58();

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        // Base58Proxy::$options = [
        //     "characters" => $characters,
        // ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded, "1LDlk6QWOejX6rPrJ");
        $this->assertEquals($encoded2, "1LDlk6QWOejX6rPrJ");
        $this->assertEquals($encoded3, "1LDlk6QWOejX6rPrJ");
        $this->assertEquals($encoded4, "1LDlk6QWOejX6rPrJ");
        $this->assertEquals($encoded5, "1LDlk6QWOejX6rPrJ");

        $data = hex2bin("0000010203040506");
        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        // Base58Proxy::$options = [
        //     "characters" => $characters,
        // ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded, "00T6JZQu6");
        $this->assertEquals($encoded2, "00T6JZQu6");
        $this->assertEquals($encoded3, "00T6JZQu6");
        $this->assertEquals($encoded4, "00T6JZQu6");
        $this->assertEquals($encoded5, "00T6JZQu6");
    }

    public function testShouldUseBitcoinCharacterSet()
    {
        $data = "Hello world!";

        $php = new PhpEncoder(["characters" => Base58::BITCOIN]);
        $gmp = new GmpEncoder(["characters" => Base58::BITCOIN]);
        $bcmath = new BcmathEncoder(["characters" => Base58::BITCOIN]);
        $base58 = new Base58(["characters" => Base58::BITCOIN]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => Base58::BITCOIN,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($encoded2, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($encoded3, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($encoded4, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($encoded5, "2NEpo7TZRhna7vSvL");

        $data = hex2bin("0000010203040506");

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded, "11W7LcTy7");
        $this->assertEquals($encoded2, "11W7LcTy7");
        $this->assertEquals($encoded3, "11W7LcTy7");
        $this->assertEquals($encoded4, "11W7LcTy7");
        $this->assertEquals($encoded5, "11W7LcTy7");
    }

    public function testShouldUseCustomCharacterSet()
    {
        $data = "Hello world!";
        $characters = "9876543210ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv";

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded, "8LDlk3QWOejX3rPrJ");
        $this->assertEquals($encoded2, "8LDlk3QWOejX3rPrJ");
        $this->assertEquals($encoded3, "8LDlk3QWOejX3rPrJ");
        $this->assertEquals($encoded4, "8LDlk3QWOejX3rPrJ");
        $this->assertEquals($encoded5, "8LDlk3QWOejX3rPrJ");

        $data = hex2bin("0000010203040506");

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded, "99T3JZQu3");
        $this->assertEquals($encoded2, "99T3JZQu3");
        $this->assertEquals($encoded3, "99T3JZQu3");
        $this->assertEquals($encoded4, "99T3JZQu3");
        $this->assertEquals($encoded5, "99T3JZQu3");
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeBigIntegers($characters)
    {
        $data = PHP_INT_MAX;

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded3 = $bcmath->encodeInteger($data);
        $encoded4 = $base58->encodeInteger($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $bcmath->decodeInteger($encoded3));
        $this->assertEquals($data, $base58->decodeInteger($encoded4));
        $this->assertEquals($data, Base58Proxy::decodeInteger($encoded5));
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnDecodeInvalidData($encoder)
    {
        $this->expectException(InvalidArgumentException::class);

        $invalid = "invalid~data-%@#!@*#-foo";

        (new $encoder)->decode($invalid, false);
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnDecodeInvalidDataWithCustomCharacterSet($encoder)
    {
        $this->expectException(InvalidArgumentException::class);

        /* This would normally be valid, however the custom character set */
        /* is missing the T character. */
        $invalid = "T8dgcjRGuYUueWht";
        $options = [
            "characters" => "9876543210ABCDEFGHIJKLMNOPQRS-UVWXYZabcdefghijklmnopqrstuv"
        ];


        (new $encoder($options))->decode($invalid, false);
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionWithInvalidCharacterSet($encoder)
    {
        $this->expectException(InvalidArgumentException::class);

        $options = [
            "characters" => "0023456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv"
        ];

        new $encoder($options);
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroByte($characters)
    {
        $data = "\x00";

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base58->decode($encoded4));
        $this->assertEquals($data, Base58Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytes($characters)
    {
        $data = "\x00\x00\x00";

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base58->decode($encoded4));
        $this->assertEquals($data, Base58Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroBytePrefix($characters)
    {
        $data = "\x00\x01\x02";

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base58->decode($encoded4));
        $this->assertEquals($data, Base58Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytePrefix($characters)
    {
        $data = "\x00\x00\x00\x01\x02";

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base58 = new Base58(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base58->decode($encoded4));
        $this->assertEquals($data, Base58Proxy::decode($encoded5));
    }

    public function testBug1()
    {
        $php = new PhpEncoder(["characters" => Base58::BITCOIN]);
        $gmp = new GmpEncoder(["characters" => Base58::BITCOIN]);
        $bcmath = new BcmathEncoder(["characters" => Base58::BITCOIN]);

        $decoded = $php->decode("1gbCKFk");
        $decoded2 = $gmp->decode("1gbCKFk");
        $decoded3 = $bcmath->decode("1gbCKFk");

        $this->assertEquals($decoded, $decoded);
        $this->assertEquals($decoded, $decoded2);
        $this->assertEquals($decoded, $decoded3);

        $encoded = $php->encode($decoded);
        $encoded2 = $gmp->encode($decoded2);
        $encoded3 = $bcmath->encode($decoded2);

        $this->assertEquals("1gbCKFk", $encoded);
        $this->assertEquals("1gbCKFk", $encoded2);
        $this->assertEquals("1gbCKFk", $encoded3);
    }

    /**
     * @dataProvider bitcoinCheckProvider
     */
    public function testShouldEncodeAndDecodeBitcoinWithCheck($hex, $expected)
    {
        $options = [
            "characters" => Base58::BITCOIN,
            "check" => true,
            "version" => 0x00,
        ];

        $data = hex2bin($hex);

        $php = new PhpEncoder($options);
        $gmp = new GmpEncoder($options);
        $bcmath = new BcmathEncoder($options);
        $base58 = new Base58($options);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base58->encode($data);

        Base58Proxy::$options = $options;
        $encoded5 = Base58Proxy::encode($data);

        $this->assertEquals($expected, $encoded);
        $this->assertEquals($expected, $encoded2);
        $this->assertEquals($expected, $encoded3);
        $this->assertEquals($expected, $encoded4);
        $this->assertEquals($expected, $encoded5);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base58->decode($encoded4));
        $this->assertEquals($data, Base58Proxy::decode($encoded5));
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowWithInvalidChecksum($encoder)
    {
        $this->expectException(RuntimeException::class);

        $options = [
            "characters" => Base58::BITCOIN,
            "check" => true,
            "version" => 0x00,
        ];

        //$encoded = "1PMycacnJaSqwwJqjawXBErnLsZ7RkXUAs";
        $encoded = "1PMycacnJaSqwwJqjawXBErnLsZ7RkXUAS";

        (new $encoder($options))->decode($encoded);
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowWithInvalidVersion($encoder)
    {
        $this->expectException(RuntimeException::class);

        $options = [
            "characters" => Base58::BITCOIN,
            "check" => true,
            "version" => 0x00,
        ];

        /* This was encoded using version 0x01 */
        $encoded = "nhabgv51kuimNSvm1GqfN8ZyNp44FGNnC";

        (new $encoder($options))->decode($encoded);
    }

    public function characterSetProvider()
    {
        return [
            "GMP character set" => [Base58::GMP],
            "Bitcoin character set" => [Base58::BITCOIN],
            "Flickr character set" => [Base58::FLICKR],
            "Ripple character set" => [Base58::RIPPLE],
            "IPFS character set" => [Base58::IPFS],
            "custom character set" => ["9876543210ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv"],
        ];
    }

    public function encoderProvider()
    {
        return [
            "PHP encoder" => [PhpEncoder::class],
            "GMP encoder" => [GmpEncoder::class],
            "BCMath encoder" => [BcmathEncoder::class],
            "Base encoder" => [Base58::class],
        ];
    }

    /* https://en.bitcoin.it/wiki/Technical_background_of_version_1_Bitcoin_addresses */
    /* https://github.com/luke-jr/libbase58/blob/master/tests/decode-b58c.sh */
    /* https://github.com/anaskhan96/base58check/blob/master/base58check_test.go */
    /* https://github.com/dartcoin/base58check/blob/master/test/base58check_test.dart */
    public function bitcoinCheckProvider()
    {
        return [
            "Data from Bitcoin Wiki" => [
                "f54a5851e9372b87810a8e60cdd2e7cfd80b6e31",
                "1PMycacnJaSqwwJqjawXBErnLsZ7RkXUAs"
            ],
            "Data from luke-jr/libbase58" => [
                "5a1fc5dd9e6f03819fca94a2d89669469667f9a0",
                "19DXstMaV43WpYg4ceREiiTv2UntmoiA9j"
            ],
            "Data from anaskhan96/base58check" => [
                "44d00f6eb2e5491cd7ab7e7185d81b67a23c4980f62b2ed0914d32b7eb1c5581",
                "1XJjHG4gLiJfxrx82yPFWC8tu8cxKvaQjZNvVfSrsfiX4mbUsw"
            ],
            "Data from dartcoin/base58check" => [
                "65a16059864a2fdbc7c99a4723a8395bc6f188eb",
                "1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62i"
            ]
        ];
    }
}
