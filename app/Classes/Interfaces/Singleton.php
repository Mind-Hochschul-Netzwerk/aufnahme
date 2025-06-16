<?php
namespace MHN\Aufnahme\Interfaces;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

/**
 * Interface für Singletons
 *
 * Alle Singletons müssen dieses Interface implementieren. Der Trait "Singleton"
 * ist der Standard-Trait dazu.
 */
interface Singleton
{
    /**
     * Gibt die Instanz der Klasse zurück
     */
    public static function getInstance(): static;
}
