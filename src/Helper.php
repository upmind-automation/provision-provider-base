<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase;

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
}
