<?php

/*
 * This file is part of the Base58 package
 *
 * Copyright (c) 2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base58
 *
 */

namespace Tuupola\Base58;

use Tuupola\Base58;
use Tuupola\Base58Proxy;

class Base58Test extends \PHPUnit_Framework_TestCase
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
}
