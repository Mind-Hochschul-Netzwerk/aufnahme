<?php
namespace MHN\Aufnahme\Service;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

/**
 * Passwort-Hashing und Token-Generierung
 */
class PasswordManager implements \MHN\Aufnahme\Interfaces\Singleton
{
    use \MHN\Aufnahme\Traits\Singleton;

    /** @var int PBKDF2-Parameter für das Passwort-Hashing */
    const SALT_SIZE = 16;

    /** @var string PBKDF2-Parameter für das Passwort-Hashing */
    const ALGORITHM = 'sha256';

    /** @var int PBKDF2-Parameter für das Passwort-Hashing */
    const ITERATIONS = 10000;

    /** @var int PBKDF2-Parameter für das Passwort-Hashing */
    const LENGTH = 128;

    /** @var int MD5-Salt, der bei alten Passwörtern benutzt wurde */
    const OLD_MD5_SALT = 'kjbsd68hnj';

    /** @var int Länge eines automatisch generierten Passworts */
    const PASSWORD_LENGTH = 12;

    /**
     * Prüft, ob ein Plaintext-Passwort zu einem Hash passt.
     *
     * @param string $hash
     * @param string $plainTextPassword
     *
     * @return bool
     *
     * @throws \UnexpectedValueException wenn der Hash-Typ nicht unterstützt wird.
     */
    public function validate($hash, $plainTextPassword)
    {
        if (!preg_match('/^:/', $hash)) {
            return $hash === md5(self::OLD_MD5_SALT . $plainTextPassword);
        }
        if (!preg_match('/^:pbkdf2/', $hash)) {
            throw new \UnexpectedValueException('Unsupported hash type', 1491306058);
        }

        list($dummy, $type, $algorithm, $iterations, $length, $encodedSalt, $realHash) = explode(':', $hash);

        $hashedPassword = base64_encode(
            hash_pbkdf2(
                $algorithm,
                $plainTextPassword,
                base64_decode($encodedSalt),
                $iterations,
                $length,
                true
            )
        );
        return $realHash === $hashedPassword;
    }

    /**
     * Generiert einen base64-kodierten PBKDF2-Hash zu einem Passwort.
     *
     * @param string $plainTextPassword
     *
     * @return string
     */
    public function hash($plainTextPassword)
    {
        $salt = mcrypt_create_iv(self::SALT_SIZE, MCRYPT_DEV_URANDOM);
        $hash = hash_pbkdf2(self::ALGORITHM, $plainTextPassword, $salt, self::ITERATIONS, self::LENGTH, true);
        $hashParts = [
            '', 'pbkdf2', self::ALGORITHM, self::ITERATIONS, self::LENGTH, base64_encode($salt), base64_encode($hash),
        ];
        return implode(':', $hashParts);
    }

    /**
     * Gibt einen zufälligen String mit vorgegebener Länge zurück.
     *
     * Zeichensatz: 0-9 A-Z a-z + /
     *
     * @param int $length Zeichenanzahl, maximal 344
     *
     * @return string
     */
    public function generateRandomString($length = self::PASSWORD_LENGTH)
    {
        return substr(base64_encode(mcrypt_create_iv(256, MCRYPT_DEV_URANDOM)), 0, $length);
    }
}
