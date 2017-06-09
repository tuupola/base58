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

namespace Tuupola;

class Base58
{
    const GMP = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv";
    const BITCOIN = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
    const FLICKR = "123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    const RIPPLE = "rpshnaf39wBUDNEGHJKLM4PQRST7VWXYZ2bcdeCg65jkm8oFqi1tuvAxyz";
    const IPFS = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";

    private $encoder;
    private $options = [];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
        if (function_exists("gmp_init")) {
            $this->encoder = new Base58\GmpEncoder($this->options);
        }
        $this->encoder = new Base58\PhpEncoder($this->options);
    }

    public function encode($data)
    {
        return $this->encoder->encode($data);
    }

    public function decode($data, $integer = false)
    {
        return $this->encoder->decode($data, $integer);
    }
}
