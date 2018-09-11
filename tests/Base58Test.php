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

namespace Tuupola\Base58;

use Tuupola\Base58;
use Tuupola\Base58Proxy;
use PHPUnit\Framework\TestCase;

class Base58Test extends TestCase
{

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldEncodeAndDecodeRandomBytes()
    {
        $data = random_bytes(128);
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        $decoded3 = (new BcmathEncoder)->decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base58)->encode($data);
        $decoded4 = (new Base58)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base58Proxy::encode($data);
        $decoded5 = Base58Proxy::decode($encoded5);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldEncodeAndDecodeIntegers()
    {
        $data = 987654321;
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded, true);
        $decoded2 = (new GmpEncoder)->decode($encoded2, true);
        $decoded3 = (new BcmathEncoder)->decode($encoded2, true);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base58)->encode($data);
        $decoded4 = (new Base58)->decode($encoded4, true);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base58Proxy::encode($data);
        $decoded5 = Base58Proxy::decode($encoded5, true);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base58)->encode($data);
        $decoded = (new Base58)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    public function testShouldEncodeAndDecodeWithLeadingZero()
    {
        $data = hex2bin("07d8e31da269bf28");
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        $decoded3 = (new BcmathEncoder)->decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base58)->encode($data);
        $decoded4 = (new Base58)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base58Proxy::encode($data);
        $decoded5 = Base58Proxy::decode($encoded5);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldUseDefaultCharacterSet()
    {
        $data = "Hello world!";

        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        $decoded3 = (new BcmathEncoder)->decode($encoded2);

        $this->assertEquals($encoded, "1LDlk6QWOejX6rPrJ");
        $this->assertEquals($encoded2, "1LDlk6QWOejX6rPrJ");
        $this->assertEquals($encoded3, "1LDlk6QWOejX6rPrJ");
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base58)->encode($data);
        $decoded4 = (new Base58)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base58Proxy::encode($data);
        $decoded5 = Base58Proxy::decode($encoded5);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldUseBitcoingCharacterSet()
    {
        $data = "Hello world!";

        $encoded = (new PhpEncoder(["characters" => Base58::BITCOIN]))->encode($data);
        $encoded2 = (new GmpEncoder(["characters" => Base58::BITCOIN]))->encode($data);
        $encoded3 = (new BcmathEncoder(["characters" => Base58::BITCOIN]))->encode($data);
        $decoded = (new PhpEncoder(["characters" => Base58::BITCOIN]))->decode($encoded);
        $decoded2 = (new GmpEncoder(["characters" => Base58::BITCOIN]))->decode($encoded2);
        $decoded3 = (new BcmathEncoder(["characters" => Base58::BITCOIN]))->decode($encoded2);

        $this->assertEquals($encoded, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($encoded2, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($encoded3, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base58)->encode($data);
        $decoded4 = (new Base58)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        Base58Proxy::$options = [
            "characters" => Base58::BITCOIN,
        ];
        $encoded5 = Base58Proxy::encode($data);
        $decoded5 = Base58Proxy::decode($encoded5);
        $this->assertEquals($encoded5, "2NEpo7TZRhna7vSvL");
        $this->assertEquals($data, $decoded5);
    }

    /**
     * @dataProvider zeroPrefixProvider
     */
    public function testEncodingAZeroBitStream($data)
    {
        $bcEncoder = new BcmathEncoder();
        $gmpEncoder = new GmpEncoder();
        $phpEncoder = new PhpEncoder();
        $this->assertSame($data, $bcEncoder->decode($bcEncoder->encode($data)));
        $this->assertSame($data, $gmpEncoder->decode($gmpEncoder->encode($data)));
        $this->assertSame($data, $phpEncoder->decode($phpEncoder->encode($data)));
    }
    public function zeroPrefixProvider()
    {
        return [
            "no leading zero bytes" => ["\x01"],
            "single zero byte" => ["\x00"],
            "multiple zero bytes" => ["\x00\x00"],
            "single zero byte prefix" => ["\x00\x01"],
            "multiple zero byte prefix" => ["\x00\x00\x00\x01"]
        ];
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
}
