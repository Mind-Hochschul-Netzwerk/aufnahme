<?php
declare(strict_types=1);
namespace MHN\Aufnahme\Service;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

/**
 * Web Token
 */
class Token
{
    private function __construct() 
    {
    }

    /**
     * $info is some information that changes after the token has been used
     */
    public static function encode(array $payload, string $info = '', string $key): string
    {
        $str = rtrim(base64_encode(json_encode($payload)), '=');
        $sig = self::generateSignature($str, $info, $key);
        return str_replace('=', '', strtr("$str:$sig", '+/', '-_')); // replace some characters so the token does not have to be urlencode'd
    }

    /**
     * @param $callback shall generate the exact $info that the token was created with if the token is valid
     *           it can also perform checks on the payload and throw an exception if it has become invalid
     * @throws \RuntimeException if the token is invalid
     */
    public static function decode(string $token, ?callable $callback = null, string $key): array
    {
        try {
            list($str, $sig) = explode(':', strtr($token, '-_', '+/'));
            $payload = (array)json_decode(base64_decode($str));
            $info = ($callback === null) ? '' : $callback($payload);
            if ($sig !== self::generateSignature($str, $info, $key)) {
                throw new \Exception('signature wrong');    
            }
            return $payload;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ($msg) {
                $msg = ": $msg";
            }
            throw new \RuntimeException('Invalid token' . $msg);
        }
    }

    private static function generateSignature($string, $info, $key) {
        return rtrim(base64_encode(hash('sha256', $string.$info.$key, true)), '=');
    }
}
