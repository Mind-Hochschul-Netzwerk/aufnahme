<?php
namespace MHN\Aufnahme\Domain\Repository;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use DateTime;
use MHN\Aufnahme\Domain\Model\Template;
use MHN\Aufnahme\Sql;

/**
 * Verwaltet die Vorlagen in der Datenbank
 */
class TemplateRepository implements \MHN\Aufnahme\Interfaces\Singleton
{
    use \MHN\Aufnahme\Traits\Singleton;

    /** @var string */
    const TABLE_NAME = 'templates';

    /** @var Sql */
    private $sql = null;

    /**
     * Instanziierung nur durch Interfaces\Singleton::getInstance() aufgerufen
     */
    private function __construct()
    {
        $this->sql = Sql::getInstance();
    }

    public function getOneByName(string $name)
    {
        $result = $this->sql->select(self::TABLE_NAME, '*', 'name = "' . $this->sql->escape($name) . '" ORDER BY name');

        $row = $result->fetch_assoc();
        if (!$row) {
            throw new \OutOfBoundsException('template name invalid: ' . $name);
        }
        return $this->createTemplateObject($row);
    }

    public function getAll()
    {
        $result = $this->sql->select(self::TABLE_NAME, '*');

        $entries = [];

        while ($row = $result->fetch_assoc()) {
            $entries[]= $this->createTemplateObject($row);
        }

        return $entries;
    }

    private function createTemplateObject(array $row): Template
    {
        return new Template($row['name'], $row['label'], $row['subject'], $row['text'], $row['hints']);
    }

    public function save(Template $template): void
    {
        $this->sql->update(self::TABLE_NAME, [
            'subject' => $template->getSubject(),
            'text' => $template->getText(),
        ], 'name = "' . $this->sql->escape($template->getName()) . '"');
    }
}
