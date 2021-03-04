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

// only allow backend calls
$ip = ip2long($_SERVER['REMOTE_ADDR']);
if (!(
        (ip2long("172.16.0.0") <= $ip && $ip <= ip2long("172.31.255.255"))
     || (ip2long("192.168.0.0") <= $ip && $ip <= ip2long("192.168.255.255"))
    )) {
    die("forbidden");
}

try {
    $antragId = Token::decode($_REQUEST['token'], null, getenv('TOKEN_KEY'))[0];
} catch (\Exception $e) {
    die("token invalid");
}

$antrag = new Antrag($antragId);
if ($antrag->getStatus() !== Antrag::STATUS_AUFGENOMMEN) {
    die("status invalid");
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