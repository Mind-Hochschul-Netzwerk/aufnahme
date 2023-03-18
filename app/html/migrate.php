<?php

namespace MHN\Aufnahme;

require_once '../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

const SCHEMA_FILENAME = '/var/www/Resources/Private/formData.yml';
$schema = Yaml::parse(file_get_contents(SCHEMA_FILENAME));

$db = Sql::getInstance();

$db->query('ALTER TABLE antraege ADD formData TEXT NOT NULL');
$db->query('ALTER TABLE antraege ADD mail VARCHAR(255) NOT NULL');
$antraege = $db->queryToArray($db->query('SELECT antrag_id, fragen_werte FROM antraege'));

foreach ($antraege as $a) {
    $fragen_werte = unserialize($a['fragen_werte']);

    foreach ($fragen_werte as $k=>$v) {
        $fragen_werte[strtolower($k)] = $v;
    }

    $daten = $db->queryToArray($db->query('SELECT * FROM daten WHERE antrag_id = ' . $a['antrag_id']));
    $daten = $daten[0];

    foreach ($daten as $k=>$v) {
        $daten[strtolower($k)] = $v;
    }

    $formData = [];
    foreach ($schema as $name=>$type) {
        switch ($type) {
            case "text":
            case "mail":
                $formData[$name] = (string) (isset($daten[$name]) ? $daten[$name] : $fragen_werte[$name]);
                break;
            case "bool":
                $formData[$name] = $daten[$name] === "j";
                break;
            case "datetime":
                $v = $daten[$name];
                if (!$v) {
                    $formData[$name] = null;
                    break;
                }
                $date = new \DateTime($daten[$name]);
                $formData[$name] = $date->format('c');
                break;
        }
    }

    $json = json_encode($formData);
    $db->update('antraege', ['formData' => $json, 'mail' => $daten['user_email']], 'antrag_id = '  . $a['antrag_id']);
}

// $db->query('ALTER TABLE antraege DROP COLUMN fragen_werte');
// $db->query('DROP TABLE daten');
