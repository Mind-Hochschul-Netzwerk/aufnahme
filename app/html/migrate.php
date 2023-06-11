<?php

namespace MHN\Aufnahme;

require_once '../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use MHN\Aufnahme\Domain\Model\FormData;

$db = Sql::getInstance();

$antraege = $db->queryToArray($db->query('SELECT antrag_id, formData FROM antraege'));

header('Content-Type: text/plain');

foreach ($antraege as $a) {
    $formData = new FormData($a['formData']);
    $decode = json_decode($a['formData'], true);
    $formData->set('mhn_telefon', implode(', ', array_filter([$decode['mhn_telefon'], $decode['mhn_mobil']])));
    $formData->set('mhn_ws_strasse', implode(' ', array_filter([$decode['mhn_ws_strasse'], $decode['mhn_ws_hausnr']])));
    $formData->set('mhn_zws_strasse', implode(' ', array_filter([$decode['mhn_zws_strasse'], $decode['mhn_zws_hausnr']])));
    $db->update('antraege', ['formData' => (string)$formData], 'antrag_id = '  . $a['antrag_id']);
}