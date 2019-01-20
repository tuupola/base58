<?php

/*

Copyright (c) 2017-2018 Mika Tuupola

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

    public function testShouldThrowExceptionOnDecodeInvalidData()
    {
        $invalid = "invalid~data-%@#!@*#-foo";

        $decoders = [
            new PhpEncoder(),
            new GmpEncoder(),
            new BcmathEncoder(),
            new Base58(),
        ];

        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                $decoder->decode($invalid, false);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }
    }

    public function testShouldThrowExceptionOnDecodeInvalidDataWithCustomCharacterSet()
    {
        /* This would normally be valid, however the custom character set */
        /* is missing the T character. */
        $invalid = "T8dgcjRGuYUueWht";
        $options = [
            "characters" => "9876543210ABCDEFGHIJKLMNOPQRS-UVWXYZabcdefghijklmnopqrstuv"
        ];

        $decoders = [
            new PhpEncoder($options),
            new GmpEncoder($options),
            new BcmathEncoder($options),
            new Base58($options),
        ];

        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                $decoder->decode($invalid, false);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }
    }

    public function testShouldThrowExceptionWithInvalidCharacterSet()
    {
        $options = [
            "characters" => "0023456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
        ];

        $decoders = [
            PhpEncoder::class,
            GmpEncoder::class,
            BcmathEncoder::class,
            Base58::class,
        ];

        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($options);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }

        $options = [
            "characters" => "00123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
        ];


        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($options);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }
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
        $decoded = (new PhpEncoder(["characters" => Base58::BITCOIN]))->decode("1gbCKFk");
        $decoded2 = (new GmpEncoder(["characters" => Base58::BITCOIN]))->decode("1gbCKFk");
        $decoded3 = (new BcmathEncoder(["characters" => Base58::BITCOIN]))->decode("1gbCKFk");

        $this->assertEquals($decoded, $decoded);
        $this->assertEquals($decoded, $decoded2);
        $this->assertEquals($decoded, $decoded3);

        $encoded = (new PhpEncoder(["characters" => Base58::BITCOIN]))->encode($decoded);
        $encoded2 = (new GmpEncoder(["characters" => Base58::BITCOIN]))->encode($decoded2);
        $encoded3 = (new BcmathEncoder(["characters" => Base58::BITCOIN]))->encode($decoded2);

        $this->assertEquals("1gbCKFk", $encoded);
        $this->assertEquals("1gbCKFk", $encoded2);
        $this->assertEquals("1gbCKFk", $encoded3);
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
}
