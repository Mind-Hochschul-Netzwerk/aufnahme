<?php
declare(strict_types=1);

/**
 * front controller
 */

namespace App;

use App\Controller\Controller;
use App\Model\Antrag;
use App\Repository\AntragRepository;
use App\Service\CurrentUser;
use App\Service\MaintenanceRunner;
use App\Service\Tpl;
use Hengeb\Router\Exception\InvalidRouteException;
use Hengeb\Router\Router;
use Symfony\Component\HttpFoundation\Request;

require_once '../vendor/autoload.php';

(new MaintenanceRunner())->run();

$router = new Router(__DIR__ . '/../Classes/Controller');

$router->addExceptionHandler(InvalidRouteException::class, [Controller::class, 'handleException']);

$router->addType(Antrag::class, fn($id) => AntragRepository::getInstance()->getOneById(intval($id)));

$isEmbedded = !empty($_GET['embed']);
Tpl::getInstance()->set('isEmbedded', $isEmbedded);
if ($isEmbedded) {
    $parentUrl = empty($_GET['parentUrl']) ? '' : filter_var($_GET['parentUrl'], FILTER_VALIDATE_URL);
    $urlComponents = $parentUrl ? parse_url($parentUrl) : [];
    if (!empty($urlComponents['query'])) {
        parse_str($urlComponents['query'], $parentQuery);
        foreach ($parentQuery as $k=>$v) {
            if (!isset($_GET[$k])) {
                $_GET[$k] = $v;
            }
        }
    }
}

$request = Request::createFromGlobals();
$currentUser = CurrentUser::getInstance();
$currentUser->setRequest($request);

Tpl::getInstance()->set('currentUser', $currentUser);
Tpl::getInstance()->set('_csrfToken', fn() => $router->createCsrfToken());
Tpl::getInstance()->set('_timeZone', new \DateTimeZone('Europe/Berlin'));

$response = $router->dispatch($request, $currentUser)->send();
