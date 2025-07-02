<?php
namespace App;

use App\Domain\Repository\UserRepository;
use Smarty;

class UserController
{
    public function exec()
    {
        Service\SmartyContainer::getInstance()->getSmarty()->assign('webusers', UserRepository::getInstance()->findAll());
    }
}

function benutzer__laden($parameter)
{
    $b = new UserController($parameter);
    $b->exec();
}
