<?php

namespace MHN\Aufnahme;

require_once '../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use MHN\Aufnahme\Domain\Model\FormData;

$db = Sql::getInstance();

$antraege = $db->queryToArray($db->query('SELECT antrag_id, formData FROM antraege'));

foreach ($antraege as $a) {
    $formData = new FormData($a['formData']);
    $decode = json_decode($a['formData'], true);
    $formData->set('mhn_mensa', $decode['mhn_mensa'] === 'j' || $decode['mhn_mensa'] === true);

    $db->update('antraege', ['formData' => (string)$formData], 'antrag_id = '  . $a['antrag_id']);
}

