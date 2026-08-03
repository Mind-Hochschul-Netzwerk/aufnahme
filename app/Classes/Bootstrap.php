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
use App\Service\OpenIdConnect;
use Hengeb\Db\Db;
use Hengeb\Router\Exception\InvalidRouteException;
use Hengeb\Router\ServiceContainer;

/**
 * Service container
 */
class Bootstrap extends ServiceContainer {
    private bool $isEmbedded = false;

    public function __construct()
    {
        parent::__construct();
        $this->startDebugger();
        $this->detectAndHandleEmbedding();
        $this->getMaintenanceRunner()->run();
        $this->registerService(CurrentUserInterface::class, fn() => $this->getCurrentUser());
        $this->getService(\Hengeb\Router\LatteExtension::class)->timezone = 'Europe/Berlin';
        $this->getService(\Latte\Engine::class)->addExtension(new LatteExtension($this->isEmbedded));
        $this->getRouter()
            ->addExceptionHandler(InvalidRouteException::class, [Controller::class, 'handleException'])
            ->addType(Antrag::class, fn($id) => $this->getAntragRepository()->getOneById(intval($id)))
            ->addType(Template::class, fn(string $name) => $this->getTemplateRepository()->getOneByName($name));
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

    public function getCurrentUser(): CurrentUser
    {
        return $this->createService(CurrentUser::class, fn() => new CurrentUser($this->getRequest(), $this->getUserRepository()));
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
            emailService: $this->getEmailService(),
        ));
    }

    public function getOpenIdConnect(): OpenIdConnect
    {
        return $this->createService(OpenIdConnect::class, fn() => new OpenIdConnect(
            providerUrl: getenv('OIDC_PROVIDER_URL') ?: '',
            clientId: getenv('OIDC_CLIENT_ID') ?: '',
            clientSecret: getenv('OIDC_CLIENT_SECRET') ?: '',
            redirectUrl: 'https://aufnahme.' . getenv('DOMAINNAME') . '/login',
            request: $this->getRequest(),
            publicProviderHost: getenv('OIDC_PUBLIC_URL') ? parse_url(getenv('OIDC_PUBLIC_URL'), PHP_URL_HOST) : null,
        ));
    }

    public function getAntragRepository(): AntragRepository
    {
        return $this->createService(AntragRepository::class, fn() => new AntragRepository(
            $this->getService(Db::class),
            $this->getEmailRepository(),
            $this->getVoteRepository()
        ));
    }

    public function getEmailRepository(): EmailRepository
    {
        return $this->createService(EmailRepository::class, fn() => new EmailRepository(
            $this->getService(Db::class),
        ));
    }

    public function getTemplateRepository(): TemplateRepository
    {
        return $this->createService(TemplateRepository::class, fn() => new TemplateRepository(
            $this->getService(Db::class),
        ));
    }

    public function getUserRepository(): UserRepository
    {
        return $this->createService(UserRepository::class, fn() => new UserRepository(
            ldapHost: getenv('LDAP_HOST'),
            ldapBindDn: getenv('LDAP_BIND_DN'),
            ldapBindPassword: getenv('LDAP_BIND_PASSWORD'),
            ldapPeopleDn: getenv('LDAP_PEOPLE_DN'),
        ));
    }

    public function getVoteRepository(): VoteRepository
    {
        return $this->createService(VoteRepository::class, fn() => new VoteRepository(
            $this->getService(Db::class),
            $this->getUserRepository(),
        ));
    }
}
