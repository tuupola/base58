<?php

/*
 * This file is part of the Base58 package
 *
 * Copyright (c) 2017-2018 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base58
 *
 */

namespace Tuupola\Base58;

use InvalidArgumentException;
use Tuupola\Base58;

class GmpEncoder
{
    private $options = [
        "characters" => Base58::GMP,
    ];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);

        $uniques = count_chars($this->options["characters"], 3);
        if (58 !== strlen($uniques) || 58 !== strlen($this->options["characters"])) {
            throw new InvalidArgumentException(
                "Character set must contain 58 unique characters"
            );
        }
    }

    public function encode($data, $integer = false)
    {
        if (is_integer($data) || true === $integer) {
            $base58 = gmp_strval(gmp_init($data, 10), 58);
        } else {
            $hex = bin2hex($data);

            $leadZeroBytes = 0;
            while ("" !== $hex && 0 === strpos($hex, "00")) {
                $leadZeroBytes++;
                $hex = substr($hex, 2);
            }

            // Prior to PHP 7.0 substr() returns false
            // instead of the empty string
            if (false === $hex) {
                $hex = "";
            }

            // gmp_init() cannot cope with a zero-length string
            if ("" === $hex) {
                return str_repeat($this->options["characters"][0], $leadZeroBytes);
            }

            $base58 = str_repeat($this->options["characters"][0], $leadZeroBytes) . gmp_strval(gmp_init($hex, 16), 58);
        }

        if (Base58::GMP === $this->options["characters"]) {
            return $base58;
        }
        return strtr($base58, Base58::GMP, $this->options["characters"]);
    }

    public function decode($data, $integer = false)
    {
        // If the data contains characters that aren't in the character set
        if (strlen($data) !== strspn($data, $this->options["characters"])) {
            throw new InvalidArgumentException("Data contains invalid characters");
        }

        if (Base58::GMP !== $this->options["characters"]) {
            $data = strtr($data, $this->options["characters"], Base58::GMP);
        }

        $leadZeroBytes = 0;
        while ("" !== $data && 0 === strpos($data, $this->options["characters"][0])) {
            $leadZeroBytes++;
            $data = substr($data, 1);
        }

        // Prior to PHP 7.0 substr() returns false
        // instead of the empty string
        if (false === $data) {
            $data = "";
        }

        // gmp_init() cannot cope with a zero-length string
        if ("" === $data) {
            return str_repeat("\x00", $leadZeroBytes);
        }

        $hex = gmp_strval(gmp_init($data, 58), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        /* Return as integer when requested. */
        if ($integer) {
            return hexdec($hex);
        }
        return hex2bin(str_repeat("00", $leadZeroBytes) . $hex);
    }

    public function encodeInteger($data)
    {
        return $this->encode($data, true);
    }

    public function decodeInteger($data)
    {
        return $this->decode($data, true);
    }
}
