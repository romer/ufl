<?php

namespace Ufl;

use Exception;

/**
 * Class StringUtility
 * @package Ufl
 */
class StringUtility
{
    /**
     * @param string $separator
     * @param int $version
     * @return string
     */
    public static function uuid($separator = '-', $version = 4)
    {
        switch ($version) {
            case 4:
                return static::randomUUID($separator);
        }
        return '';
    }

    /**
     * @param string $separator
     * @return string
     */
    public static function randomUUID($separator = '-')
    {
        return sprintf(
            implode($separator, array('%04x%04x', '%04x', '%04x', '%04x', '%04x%04x%04x')),
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param int $length
     * @param bool $isFast
     * @return false|string
     * @throws Exception
     */
    public static function random($length = 32, $isFast = true)
    {
        if ($isFast) {
            $count = $length / 32;
            $randomStr = '';
            for ($i = 0; $i < $count; $i++) {
                $randomStr .= self::randomUUID('');
            }
        } else {
            $str = '';
            $generateLength = ceil($length / 2);
            if (function_exists('random_bytes')) {
                $str = random_bytes($generateLength);
            } else if (function_exists('mcrypt_create_iv')) {
                $str = mcrypt_create_iv($generateLength, MCRYPT_DEV_URANDOM);
            } else if (function_exists('openssl_random_pseudo_bytes')) {
                $str = openssl_random_pseudo_bytes($generateLength);
            }
            if ($str === '') {
                return self::random($length);
            }
            $randomStr = bin2hex($str);
        }
        return substr($randomStr, 0, $length);
    }
}
