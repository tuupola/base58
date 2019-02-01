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

namespace Tuupola\Base58;

use InvalidArgumentException;
use RuntimeException;
use Tuupola\Base58;

class GmpEncoder
{
    private $options = [
        "characters" => Base58::GMP,
        "check" => false,
        "version" => 0x00,
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, (array) $options);

        $uniques = count_chars($this->options["characters"], 3);
        if (58 !== strlen($uniques) || 58 !== strlen($this->options["characters"])) {
            throw new InvalidArgumentException(
                "Character set must contain 58 unique characters"
            );
        }
    }

    /**
     * Encode given data to a base58 string
     */
    public function encode(string $data): string
    {
        if (true === $this->options["check"]) {
            $data = chr($this->options["version"]) . $data;
            $hash = hash("sha256", $data, true);
            $hash = hash("sha256", $hash, true);
            $checksum = substr($hash, 0, Base58::CHECKSUM_SIZE);
            $data .= $checksum;
        }
        $hex = bin2hex($data);

        $leadZeroBytes = 0;
        while ("" !== $hex && 0 === strpos($hex, "00")) {
            $leadZeroBytes++;
            $hex = substr($hex, 2);
        }

        /* gmp_init() cannot cope with a zero-length string. */
        if ("" === $hex) {
            $base58 = str_repeat(Base58::GMP[0], $leadZeroBytes);
        } else {
            $base58 = str_repeat(Base58::GMP[0], $leadZeroBytes) . gmp_strval(gmp_init($hex, 16), 58);
        }

        if (Base58::GMP === $this->options["characters"]) {
            return $base58;
        }
        return strtr($base58, Base58::GMP, $this->options["characters"]);
    }

    /**
     * Decode given base58 string back to data
     */
    public function decode(string $data): string
    {
        $this->validateInput($data);

        if (Base58::GMP !== $this->options["characters"]) {
            $data = strtr($data, $this->options["characters"], Base58::GMP);
        }

        $leadZeroBytes = 0;
        while ("" !== $data && 0 === strpos($data, Base58::GMP[0])) {
            $leadZeroBytes++;
            $data = substr($data, 1);
        }

        /* gmp_init() cannot cope with a zero-length string. */
        if ("" === $data) {
            return str_repeat("\x00", $leadZeroBytes);
        }

        $hex = gmp_strval(gmp_init($data, 58), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        $decoded = (string) hex2bin(str_repeat("00", $leadZeroBytes) . $hex);

        if (true === $this->options["check"]) {
            $hash = substr($decoded, 0, -(Base58::CHECKSUM_SIZE));
            $hash = hash("sha256", $hash, true);
            $hash = hash("sha256", $hash, true);
            $checksum = substr($hash, 0, Base58::CHECKSUM_SIZE);

            if (0 !== substr_compare($decoded, $checksum, -(Base58::CHECKSUM_SIZE))) {
                $message = sprintf(
                    'Checksum "%s" does not match the expected "%s"',
                    bin2hex(substr($decoded, -(Base58::CHECKSUM_SIZE))),
                    bin2hex($checksum)
                );
                throw new RuntimeException($message);
            }

            $version = substr($decoded, 0, Base58::VERSION_SIZE);
            $version = ord($version);

            if ($version !==  $this->options["version"]) {
                $message = sprintf(
                    'Version "%s" does not match the expected "%s"',
                    $version,
                    $this->options["version"]
                );
                throw new RuntimeException($message);
            }

            $decoded = substr($decoded, Base58::VERSION_SIZE, -(Base58::CHECKSUM_SIZE));
        }

        return $decoded;
    }

    /**
     * Encode given integer to a base58 string
     */
    public function encodeInteger(int $data): string
    {
        $base58 = gmp_strval(gmp_init($data, 10), 58);

        if (Base58::GMP === $this->options["characters"]) {
            return $base58;
        }

        return strtr($base58, Base58::GMP, $this->options["characters"]);
    }

    /**
     * Decode given base58 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        $this->validateInput($data);

        if (Base58::GMP !== $this->options["characters"]) {
            $data = strtr($data, $this->options["characters"], Base58::GMP);
        }

        $hex = gmp_strval(gmp_init($data, 58), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        return (int) hexdec($hex);
    }

    private function validateInput(string $data): void
    {
        /* If the data contains characters that aren't in the character set. */
        if (strlen($data) !== strspn($data, $this->options["characters"])) {
            $valid = str_split($this->options["characters"]);
            $invalid = str_replace($valid, "", $data);
            $invalid = count_chars($invalid, 3);
            throw new InvalidArgumentException(
                "Data contains invalid characters \"{$invalid}\""
            );
        }
    }
}
