<?php
declare(strict_types=1);
namespace MHN\Aufnahme;

/**
* Gibt die Daten an die Mitgliederverwaltung weiter
*
* @author Henrik Gebauer <mensa@henrik-gebauer.de>
*/

// Composer
require_once '../vendor/autoload.php';

use MHN\Aufnahme\Service\Token;
use MHN\Aufnahme\Antrag;

try {
    $antragId = Token::decode($_REQUEST['token'], null, getenv('TOKEN_KEY'))[0];
} catch (\Exception $e) {
    die("token invalid");
}

$antrag = new Antrag($antragId);
if ($antrag->getStatus() !== Antrag::STATUS_AUFGENOMMEN) {
    die("forbidden");
}

if ($_REQUEST['action'] === 'data') {
    header('Content-Type: application/json');
    echo json_encode($antrag->getDaten()->toArray());
} elseif ($_REQUEST['action'] === 'finish') {
    $antrag->setStatus(Antrag::STATUS_AKTIVIERT, "");
    $antrag->save();
    echo "success";
} else {
    echo "invalid request";
}