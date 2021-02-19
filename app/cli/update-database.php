<?php
use MHN\Aufnahme\Sql;

require_once(__DIR__ . '/../vendor/autoload.php');

$sql = Sql::getInstance();

foreach (['antraege', 'daten', 'mails', 'users', 'voten'] as $tableName) {
    /** @var string $query */
    $query = 'ALTER TABLE ' . $tableName . ' ENGINE=InnoDB';
    echo $query . PHP_EOL;
    $sql->query($query);
}

// status 0 und 98 dürfen sich einloggen. Eine andere Bedeutung hat status nicht (mehr?).
$sql->query('UPDATE users SET status=0 WHERE status=98');
$sql->query('UPDATE users SET status=1 WHERE status!=0');

$sql->query('ALTER TABLE users MODIFY userid int(11) AUTO_INCREMENT');
$sql->query('ALTER TABLE antraege MODIFY antrag_id int(11) AUTO_INCREMENT');

$sql->query('ALTER TABLE users CHANGE md5pass password VARCHAR(255) NOT NULL');
