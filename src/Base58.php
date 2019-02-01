<?php

declare(strict_types = 1);

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

namespace Tuupola;

class Base58
{
    const GMP = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv";
    const BITCOIN = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
    const FLICKR = "123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    const RIPPLE = "rpshnaf39wBUDNEGHJKLM4PQRST7VWXYZ2bcdeCg65jkm8oFqi1tuvAxyz";
    const IPFS = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";

    const VERSION_SIZE = 1;
    const CHECKSUM_SIZE = 4;

    private $encoder;
    private $options = [];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
        if (function_exists("gmp_init")) {
            $this->encoder = new Base58\GmpEncoder($this->options);
        }
        $this->encoder = new Base58\PhpEncoder($this->options);
    }

    /**
     * Encode given data to a base58 string
     */
    public function encode(string $data): string
    {
        return $this->encoder->encode($data);
    }

    /**
     * Decode given base58 string back to data
     */
    public function decode(string $data): string
    {
        return $this->encoder->decode($data);
    }

    /**
     * Encode given integer to a base58 string
     */
    public function encodeInteger(int $data): string
    {
        return $this->encoder->encodeInteger($data);
    }

    /**
     * Decode given base58 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        return $this->encoder->decodeInteger($data);
    }
}
