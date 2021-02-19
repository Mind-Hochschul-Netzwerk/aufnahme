<?php
/**
 * Created by PhpStorm.
 * User: Guido Drechsel
 * Date: 29.10.16
 * Time: 17:44
 */

// Composer
require_once '../vendor/autoload.php';

$input = json_decode(file_get_contents('php://input'), true);

require_once('../lib/importiere.php');
