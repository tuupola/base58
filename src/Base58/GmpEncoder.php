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

class GmpEncoder
{
    private $options = [
        "characters" => Base58::GMP,
    ];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
    }

    public function encode($data)
    {
        if (is_integer($data)) {
            $base58 = gmp_strval(gmp_init($data, 10), 58);
        } else {
            $hex = bin2hex($data);
            $base58 = gmp_strval(gmp_init($hex, 16), 58);
        }

        if (Base58::GMP === $this->options["characters"]) {
            return $base58;
        }
        return strtr($base58, Base58::GMP, $this->options["characters"]);
    }

    public function decode($data, $integer = false)
    {
        if (Base58::GMP !== $this->options["characters"]) {
            $data = strtr($data, $this->options["characters"], Base58::GMP);
        }

        $hex = gmp_strval(gmp_init($data, 58), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        /* Return as integer when requested. */
        if ($integer) {
            return hexdec($hex);
        }

        return hex2bin($hex);
    }
}
