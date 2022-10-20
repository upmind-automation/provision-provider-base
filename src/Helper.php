<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class to assist working with Provision Categories and Providers
 */
class Helper
{
    /**
     * Generate a random password
     *
     * @param int $length
     * @param string $charlist
     *
     * @return string
     */
    public static function generatePassword(
        int $length = 15,
        string $charlist = '0-9a-zA-Z!@#$%^&*_=+()[]{};:,.<>?~'
    ): string {
        $charlist = count_chars(preg_replace_callback('#.-.#', function (array $m): string {
            return implode('', range($m[0][0], $m[0][2]));
        }, $charlist), 3);
        $chLen = strlen($charlist);

        if ($length < 1 || $chLen < 2) {
            return self::generatePassword();
        }

        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $res .= $charlist[random_int(0, $chLen - 1)];
        }
        return $res;
    }

    /**
     * Generates a random password adhering to the given rules.
     *
     * @param int $length
     * @param bool $upper Whether or not upper-case alpha characters are required
     * @param bool $numeric Whether or not numeric characters are required
     * @param bool $special Whether or not special characters are required
     * @param string $specialCharsList List of special characters to choose from
     *
     * @return string
     */
    public static function generateStrictPassword(
        int $length,
        bool $upper,
        bool $numeric,
        bool $special,
        string $specialCharsList = '`~!@#$%^&*()_+{}|:"<>?-=[];\',./'
    ): string {
        $charLists = array_values(array_filter([
            implode(range('a', 'z')), // lowercase characters
            $upper ? implode(range('A', 'Z')) : null, // uppercase characters
            $numeric ? implode(range(0, 9)) : null, // numeric characters
            $special ? $specialCharsList : null, // special characters
        ]));

        if ($length < count($charLists)) {
            throw new InvalidArgumentException('Password length not long enough to acommodate all character types');
        }

        $passwordInvalid = function ($password) use ($charLists) {
            foreach ($charLists as $list) {
                if (!preg_match(sprintf('/[%s]/', preg_quote($list, '/')), $password)) {
                    return true;
                }
            }

            return false;
        };

        $attempts = 0;
        do {
            if ($attempts >= 10) {
                throw new RuntimeException(sprintf('Failed to generate valid password after %s attempts', $attempts));
            }

            $password = self::generatePasswordFromCharacterLists($length, $charLists);
            $attempts++;
        } while ($passwordInvalid($password));

        return $password;
    }

    /**
     * @param int $length Length of password to generate
     * @param string[] $charLists Array of character list strings
     *
     * @return string
     */
    protected static function generatePasswordFromCharacterLists(int $length, array $charLists): string
    {
        $charListLengths = array_map('strlen', $charLists);
        $numLists = count($charLists);

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $listI = random_int(0, $numLists - 1);

            $list = $charLists[$listI];
            $listLength = $charListLengths[$listI];

            $password .= substr($list, random_int(0, $listLength - 1), 1);
        }

        return $password;
    }
}
