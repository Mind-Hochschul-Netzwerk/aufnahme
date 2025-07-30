<?php
namespace App\Repository;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

use App\Model\Template;
use Hengeb\Db\Db;

/**
 * Verwaltet die Vorlagen in der Datenbank
 */
class TemplateRepository implements \App\Interfaces\Singleton
{
    use \App\Traits\Singleton;

    public function getOneByName(string $name): Template
    {
        $row = Db::getInstance()->query('SELECT * FROM templates WHERE name = :name', [
            'name' => $name
        ])->getRow();

        return $row ? $this->createTemplateObject($row) : throw new \OutOfBoundsException('template name invalid: ' . $name);
    }

    public function getAll()
    {
        $rows = Db::getInstance()->query('SELECT * FROM templates ORDER BY label')->getAll();
        return array_map(fn($row) => $this->createTemplateObject($row), $rows);
    }

    private function createTemplateObject(array $row): Template
    {
        return new Template($row['name'], $row['label'], $row['subject'], $row['text'], $row['hints']);
    }

    public function save(Template $template): void
    {
        Db::getInstance()->query('UPATE templates SET subject = :subject, text = :text WHERE name = :name', [
            'subject' => $template->getSubject(),
            'text' => $template->getText(),
            'name' => $template->getName(),
        ]);
    }
}
