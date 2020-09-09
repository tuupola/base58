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

abstract class BaseEncoder
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
            throw new InvalidArgumentException("Character set must contain 58 unique characters");
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
            $checksum = substr($hash, 0, 4);
            $data .= $checksum;
        }

        $data = str_split($data);
        $data = array_map("ord", $data);

        $leadingZeroes = 0;
        while (!empty($data) && 0 === $data[0]) {
            $leadingZeroes++;
            array_shift($data);
        }

        $converted = $this->baseConvert($data, 256, 58);

        if (0 < $leadingZeroes) {
            $converted = array_merge(
                array_fill(0, $leadingZeroes, 0),
                $converted
            );
        }

        return implode("", array_map(function ($index) {
            return $this->options["characters"][$index];
        }, $converted));
    }

    /**
     * Decode given base58 string back to data
     */
    public function decode(string $data): string
    {
        $this->validateInput($data);

        $data = str_split($data);
        $data = array_map(function ($character) {
            return strpos($this->options["characters"], $character);
        }, $data);

        $leadingZeroes = 0;
        while (!empty($data) && 0 === $data[0]) {
            $leadingZeroes++;
            array_shift($data);
        }

        $converted = $this->baseConvert($data, 58, 256);

        if (0 < $leadingZeroes) {
            $converted = array_merge(
                array_fill(0, $leadingZeroes, 0),
                $converted
            );
        }

        $decoded = implode("", array_map("chr", $converted));
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
        $data = [$data];

        $converted = $this->baseConvert($data, 256, 58);

        return implode("", array_map(function ($index) {
            return $this->options["characters"][$index];
        }, $converted));
    }

    /**
     * Decode given base58 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        $this->validateInput($data);

        $data = str_split($data);
        $data = array_map(function ($character) {
            return strpos($this->options["characters"], $character);
        }, $data);

        $converted = $this->baseConvert($data, 58, 10);
        return (integer) implode("", $converted);
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

    abstract public function baseConvert(array $source, int $sourceBase, int $targetBase): array;
}
