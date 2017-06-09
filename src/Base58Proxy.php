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

use Tuupola\Base58;

class Base58Proxy
{
    public static $options = [
        "characters" => Base58::GMP,
    ];

    public static function encode($data, $options = [])
    {
        return (new Base58(self::$options))->encode($data);
    }

    public static function decode($data, $integer = false, $options = [])
    {
        return (new Base58(self::$options))->decode($data, $integer);
    }
}
