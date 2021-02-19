<?php
namespace MHN\Aufnahme\Service;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use MHN\Aufnahme\Interfaces;
use MHN\Aufnahme\Traits;
use Symfony\Component\Yaml\Yaml;

/**
 * Stellt Werte von Konfigurationsvariablen bereit
 */
class Configuration implements Interfaces\Singleton
{
    use Traits\Singleton;

    /** @var string Pfad zur Konfigurationsdatei */
    const FILENAME = '/var/www/configuration.yml';

    /** @var array */
    private $data = [];

    /**
     * Instanziierung nur über Configuration::getInstance() (aus Interfaces\Singleton)
     */
    private function __construct()
    {
        $this->readFromFile();
        $this->replaceEnvironmentMarkers();
    }

    /**
     * Lädt die Konfigurationsdatei.
     *
     * @return void
     */
    private function readFromFile()
    {
        $this->data = Yaml::parse(file_get_contents(self::FILENAME));
    }

    /**
     * Werte aus Umgebungsvariablen laden
     *
     * @return void
     *
     * @throws \UnexpectedValueException Umgebungsvariable soll geladen werden, ist aber nicht gesetzt.
     */
    private function replaceEnvironmentMarkers()
    {
        array_walk_recursive($this->data, function (&$item) {
            $matcher = '/^\\$(?P<key>[A-Za-z_][A-Za-z0-9_]*)$/';
            if (!preg_match($matcher, $item, $matches)) {
                return;
            }
            $key = $matches['key'];
            $value = getenv($key);
            if ($value === false) {
                throw new \UnexpectedValueException(
                    'Konfigurationsfehler: Umgebungsvariable ' . $key . ' nicht gesetzt.',
                    1490541873
                );
            }
            $item = $value;
        });
    }

    /**
     * Lesezugriff auf die Konfiguration
     *
     * @param string $key
     *
     * @return mixed|mixed[]
     *
     * @throws \OutOfBoundsException Konfigurationsvariable wird angefordert, ist aber nicht gesetzt.
     */
    public function get($key)
    {
        if (!isset($this->data[$key])) {
            throw new \OutOfBoundsException('Konfigurationsvariable nicht gesetzt: ' . $key, 1490541885);
        }

        return $this->data[$key];
    }
}
