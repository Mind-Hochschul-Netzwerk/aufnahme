<?php
/**
 * @author Henrik Gebauer <henrik@mind-hochschul-netzwerk.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

declare(strict_types=1);

namespace App;

use App\Controller\Controller;
use App\Model\Antrag;
use App\Model\Template;
use App\Repository\AntragRepository;
use App\Repository\EmailRepository;
use App\Repository\TemplateRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use App\Service\CurrentUser;
use App\Service\EmailService;
use App\Service\LatteExtension;
use App\Service\Ldap;
use App\Service\MaintenanceRunner;
use Hengeb\Db\Db;
use Hengeb\Router\Exception\InvalidRouteException;
use Hengeb\Router\Router;
use Latte\Engine as Latte;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Tracy\Debugger;

/**
 * Service container
 */
class Bootstrap {
    private array $instances = [];

    private bool $isEmbedded = false;

    public function run() {
        $this->startDebugger();

        $this->detectAndHandleEmbedding();

        $this->getMaintenanceRunner()->run();

        $this->getRouter()->dispatch($this->getRequest(), $this->getCurrentUser())->send();
    }

    private function startDebugger(): void
    {
        Debugger::enable(str_ends_with(getenv('DOMAINNAME'), 'localhost') ? Debugger::Development : Debugger::Production);
    }

    private function detectAndHandleEmbedding(): void
    {
        $this->isEmbedded = !empty($_GET['embed']);

        if ($this->isEmbedded) {
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
    }

    private function createService(string $classname, ?callable $setup = null): object
    {
        return $this->instances[$classname] ??= ($setup ? $setup() : new $classname);
    }

    public function getService(string $class): object
    {
        $class = basename(str_replace('\\', '/', $class)); // C
        return $this->{'get' . $class}();
    }

    public function getCurrentUser(): CurrentUser
    {
        return $this->createService(CurrentUser::class, fn() => new CurrentUser($this->getRequest(), $this->getUserRepository()));
    }

    public function getDb(): Db
    {
        return $this->createService(Db::class, fn() => new Db([
            'host' => getenv('MYSQL_HOST') ?: 'localhost',
            'port' => getenv('MYSQL_PORT') ?: 3306,
            'user' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'database' => getenv('MYSQL_DATABASE') ?: 'database',
        ]));
    }

    public function getEmailService(): EmailService
    {
        return $this->createService(EmailService::class, function () {
            $emailService = new EmailService(
                host: getenv('SMTP_HOST'),
                user: getenv('SMTP_USER'),
                password: getenv('SMTP_PASSWORD'),
                secure: getenv('SMTP_SECURE'),
                port: getenv('SMTP_PORT'),
                fromAddress: getenv('FROM_ADDRESS'),
                domain: getenv('DOMAINNAME'),
            );
            return $emailService;
        });
    }

    public function getLatte(): Latte
    {
        return $this->createService(Latte::class, function () {
            $latte = new Latte;
            $latte->setTempDirectory('/tmp/latte');
            $latte->setLoader(new \Latte\Loaders\FileLoader('/var/www/templates'));
            $latte->addExtension(new LatteExtension(
                router: $this->getRouter(),
                currentUser: $this->getCurrentUser(),
                isEmbedded: $this->isEmbedded,
            ));
            return $latte;
        });
    }

    public function getLdap(): Ldap
    {
        return $this->createService(Ldap::class, fn() => new Ldap(
            host: getenv('LDAP_HOST'),
            bindDn: getenv('LDAP_BIND_DN'),
            bindPassword: getenv('LDAP_BIND_PASSWORD'),
        ));
    }

    public function getMaintenanceRunner(): MaintenanceRunner
    {
        return $this->createService(MaintenanceRunner::class, fn() => new MaintenanceRunner(
            antragRepository: $this->getAntragRepository(),
            templateRepository: $this->getTemplateRepository(),
            userRepository: $this->getUserRepository(),
            emailService: $this->getEmailService(),
        ));
    }

    public function getRequest(): Request
    {
        return $this->createService(Request::class, function () {
            $request = Request::createFromGlobals();
            $request->setSession(new Session());
            return $request;
        });
    }

    public function getRouter(): Router
    {
        return $this->createService(Router::class, function () {
            $router = new Router(__DIR__ . '/Controller');

            $router
                ->addExceptionHandler(InvalidRouteException::class, [Controller::class, 'handleException'])

                ->addType(Antrag::class, fn($id) => $this->getAntragRepository()->getOneById(intval($id)))
                ->addType(Template::class, fn(string $name) => $this->getTemplateRepository()->getOneByName($name))

                ->addService(Latte::class, $this->getLatte(...))
                ->addService(Db::class, $this->getDb(...))
                ->addService(EmailService::class, $this->getEmailService(...))
                ->addService(AntragRepository::class, $this->getAntragRepository(...))
                ->addService(EmailRepository::class, $this->getEmailRepository(...))
                ->addService(TemplateRepository::class, $this->getTemplateRepository(...))
                ->addService(UserRepository::class, $this->getUserRepository(...))
                ->addService(VoteRepository::class, $this->getVoteRepository(...))
                ;

            return $router;
        });
    }

    public function getAntragRepository(): AntragRepository
    {
        return $this->createService(AntragRepository::class, fn() => new AntragRepository(
            $this->getDb(),
            $this->getEmailRepository(),
            $this->getVoteRepository()
        ));
    }

    public function getEmailRepository(): EmailRepository
    {
        return $this->createService(EmailRepository::class, fn() => new EmailRepository(
            $this->getDb(),
        ));
    }

    public function getTemplateRepository(): TemplateRepository
    {
        return $this->createService(TemplateRepository::class, fn() => new TemplateRepository(
            $this->getDb(),
        ));
    }

    public function getUserRepository(): UserRepository
    {
        return $this->createService(UserRepository::class, fn() => new UserRepository(
            emailService: $this->getEmailService(),
            ldapHost: getenv('LDAP_HOST'),
            ldapBindDn: getenv('LDAP_BIND_DN'),
            ldapBindPassword: getenv('LDAP_BIND_PASSWORD'),
            ldapPeopleDn: getenv('LDAP_PEOPLE_DN'),
            ldapGroupsDn: getenv('LDAP_ROLES_DN'),
        ));
    }

    public function getVoteRepository(): VoteRepository
    {
        return $this->createService(VoteRepository::class, fn() => new VoteRepository(
            $this->getDb(),
            $this->getUserRepository(),
        ));
    }
}
