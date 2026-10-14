# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Projekt

"AK-Tool" – Aufnahmetool der Aufnahmekommission des Mind-Hochschul-Netzwerks (MHN). PHP-8.3-Webanwendung ohne Framework, eigener schlanker Router/DB-Layer (`hengeb/*`-Pakete), Latte-Templates, läuft per Docker Compose.

## Entwicklung

Erstinbetriebnahme:

    $ composer install -d app
    $ make rebuild
    $ make dev

Danach erreichbar unter `https://aufnahme.docker.localhost/` (Zertifikatswarnung ignorieren), Login `webteam` / `webteam1`.

Alle weiteren Make-Targets (Produktion starten, Shell im Container, DB-Zugriff, Logs, Upgrade, …) sind über `make help` selbstdokumentiert.

Es gibt **keine Tests, kein PHPStan/Psalm, keinen Linter und keine CI-Lint/Test-Stufe** (CI baut nur das Image und pusht nach GHCR). Änderungen müssen manuell im Dev-Container verifiziert werden.

Die App erwartet LDAP und Authelia (SSO) als externe, bereits laufende Docker-Services/Netzwerke (`ldap`-Netzwerk extern, `OIDC_PROVIDER_URL=http://authelia:9091`) – die liegen nicht in diesem Repo.

## Architektur

**Einstiegspunkt:** `app/html/index.php` → `Bootstrap::dispatchRequest()`. `Bootstrap.php` (`App\`-Namespace, PSR-4 auf `app/Classes/`) ist zugleich DI-Container (erweitert `Hengeb\Router\ServiceContainer`) und registriert Modellbindungen für Routen-Platzhalter (z. B. `{id=>antrag}` lädt automatisch ein `Antrag`-Objekt per Repository).

**Routing** erfolgt deklarativ über PHP-Attribute direkt an Controller-Methoden (Paket `hengeb/router`, Quelle liegt im Repo unter `app/vendor/hengeb/router/src/`):
- `#[Route('METHODE /pfad?query')]`, Platzhalter `{name}`, Regex `{[0-9]+:id}`, Modellbindung `{id=>antrag}`
- Zugriffskontrolle: `#[PublicAccess]`, `#[RequireLogin]`, `#[AllowIf(...)]`
- `#[CheckCsrfToken(false)]` deaktiviert CSRF-Prüfung (für Token-authentifizierte Formular-Routen statt Session-Login)
- `#[RequestValue]` bindet einzelne Request-Parameter als Methodenargumente, `#[Inject]` für Properties

**Zentrale Fehlerbehandlung:** `Controller::handleException()` (statisch, registriert in `Bootstrap`) mappt Exception-Typen auf HTTP-Status + Nutzertext, rendert `errorpage.latte` (HTML) oder JSON, je nach Response-Typ der aufgerufenen Methode. Nicht eingeloggte Nutzer werden bei Zugriffsfehlern automatisch zu `AuthController::login` umgeleitet.

**Datenzugriff:** Repository-Pattern über `Hengeb\Db\Db` (eigener schlanker PDO-Wrapper, kein ORM). Repositories schreiben parametrisierte SQL-Queries direkt; Models sind reine Domänenobjekte ohne eigenen DB-Zugriff, erzeugt über `Model::fromDatabase(...)`.

**Verzeichnisse unter `app/Classes/`:**
- `Controller/` – ein Controller je Formular-/Ressourcenbereich, erben von `Controller.php` (Basis mit `render()`, `redirect()`, `handleException()` u. a.). Wichtige Controller: `AuthController` (OIDC/SSO-Login), `NeuController` (neuen Antrag stellen), `AntragController` (Bewertung/Abstimmung durch die Kommission), `EditController` (nachträgliche Datenänderung durch Antragstellende per Token), `EmailController` (Entscheidungs-Mails versenden), `TemplateController` (E-Mail-Vorlagen pflegen), `OnboardingController` (interne JSON-API für den externen "mitglieder"-Dienst, nur aus internem IP-Bereich erreichbar).
- `Model/` – `Antrag` (Status-Workflow, siehe unten), `FormData` (Bewerbungsdaten, Schema aus `Resources/Private/formData.yml`), `Vote`, `Email` (Mail-Audit-Log), `Template`, `User` (LDAP-Nutzer).
- `Repository/` – je Model ein Repository; `UserRepository` greift nicht auf die DB, sondern per `symfony/ldap` auf den LDAP-Server zu.
- `Service/` – `CurrentUser` (Session-Wrapper um `User`, delegiert per `__call`), `EmailService` (SMTP via PHPMailer; `SMTP_HOST=log` schreibt Mails nur ins Error-Log statt sie zu versenden – praktisch für Dev), `OpenIdConnect` (OIDC-Client-Wrapper), `MaintenanceRunner` (läuft bei jedem Request, gedrosselt auf alle 2h via Lockfile `/tmp/letztewartung`: löscht alte Anträge nach Fristen, verschickt Erinnerungsmails).

**Templates:** Latte (`app/templates/`), Ordner je Controller benannt. `layout.latte` ist das zentrale Seitenlayout und bindet CSS/Branding vom externen Mitgliederportal ein (`https://mitglieder.{DOMAINNAME}/css/style.css`). `partials/components.latte` enthält wiederverwendbare Komponenten (Formularfelder, Dialoge, Info-Boxen), `errorpage.latte`/`message.latte` sind generische Fehler-/Meldungsseiten.

**Integration mit dem externen "mitglieder"-Dienst** (separates Repo, intern `http://mitglieder:8080`, öffentlich `https://mitglieder.{DOMAINNAME}`) – das Haupt-Mitgliederportal des Vereins:
- Einwilligungs-/Kenntnisnahme-Texte werden von dort per HTTP-GET geladen (`NeuController::loadAgreements()`).
- Bei Aufnahme wird ein Aktivierungslink dorthin verschickt (`Antrag::getActivationUrl()` → `/aufnahme?token=...`).
- Der Dienst selbst ruft umgekehrt `OnboardingController` auf, um Antragsdaten per Token abzurufen und den Antrag nach Kontoerstellung auf `STATUS_AKTIVIERT` zu setzen.
- Logout leitet ebenfalls dorthin zurück.

## Domänen-Workflow

1. **Antrag stellen** (`NeuController`): E-Mail-Adresse angeben → signierter Token-Link per Mail (`Hengeb\Token\Token`, HMAC mit `TOKEN_KEY`) → Formular ausfüllen (inkl. versionsgebundener Einwilligungs-/Kenntnisnahme-Consent-Tokens) → `Antrag` mit `STATUS_BEWERTEN` wird angelegt, Kommission wird benachrichtigt.
2. **Bewertung/Abstimmung** (`AntragController`, `Vote`): Kommissionsmitglieder (LDAP/SSO-Login) geben je Antrag ein Votum ab (`Vote::JA`/`NEIN`/`NACHFRAGEN`/`ENTHALTUNG`). Nur das jeweils neueste Votum pro Mitglied zählt. Ein Antrag gilt als "grün" (`Antrag::getGruen()`), wenn ≥3 Ja-Stimmen vorliegen und kein Nein/Nachfragen-Votum existiert. Statuswechsel erfolgt manuell durch die Kommission.
3. **Status-Konstanten** (`Antrag::STATUS_*`): `NEU_BEWERTEN` (-1, nach Änderung erneut zu bewerten) → `BEWERTEN` (0, offen) → `NACHFRAGEN` (1) / `AUFNEHMEN` (2) / `ABLEHNEN` (3) als Entscheidung → `AUF_ANTWORT_WARTEN` (4, nach Nachfrage-Mail, Bearbeitung via `EditController`-Token) bzw. `AUFGENOMMEN` (5, Aktivierungsmail verschickt) / `ABGELEHNT` (6) → `AKTIVIERT` (7, Endzustand, gesetzt durch `OnboardingController`).
4. Entscheidungs-Mails laufen über editierbare Vorlagen (`TemplateController`), jede versandte Mail wird protokolliert (`EmailRepository`).
5. `MaintenanceRunner` räumt automatisiert auf (Fristen: aktiviert 8 Wochen, unaktivierte Aufnahme-Mail 12 Wochen, abgelehnt 60 Wochen) und verschickt Erinnerungen bei überfälligen Bewertungen/Aktivierungen.

## Gotchas

- Konfiguration läuft durchgängig über `getenv()`, kein zentrales Config-Objekt. Neue Env-Variablen müssen sowohl in `env.sample` als auch in `docker-compose.yml` (`app.environment`) ergänzt werden, sonst kommen sie im Container nicht an.
- Dev/Prod-Modus (Tracy) wird nicht über eine eigene `APP_ENV` bestimmt, sondern daran, ob `DOMAINNAME` auf `localhost` endet.
- `bdump(...)`-Aufrufe in Controllern sind Tracy-Debug-Panel-Ausgaben, u. a. um Token-/Mail-Links lokal ohne echten Mailversand zu testen – beim Arbeiten an Token-Flows nicht versehentlich entfernen.

## Bekannte Erweiterungen (knapper Changelog für nicht aus dem Code ersichtliche Entscheidungen)

- **OIDC `access_denied`:** `AuthController::login()` fängt `Jumbojett\OpenIDConnectClientException` gezielt ab und zeigt bei `access_denied` eine verständliche deutsche Fehlermeldung statt der rohen Exception-Message.
- **Bot-Schutz in `NeuController::initEmailAuth()`:** Honeypot-Feld (`website`) plus signiertes Zeitstempel-Token (`formToken`, mind. 3 Sekunden alt) gegen automatisiertes Mailbombing über das Antragsformular. Bei Verdacht auf Bot wird dieselbe "Prüfe dein Postfach"-Antwort wie bei echten Anfragen gezeigt, nur ohne tatsächlichen Mailversand, damit Bots kein unterscheidbares Feedback erhalten. IP-/E-Mail-basiertes Rate-Limiting ist bewusst (noch) nicht umgesetzt.
- **Totes Code-Fragment entfernt:** `Bootstrap::getLdap()` (referenzierte die nie existierende Klasse `App\Service\Ldap`) wurde ersatzlos gestrichen.
- **`make help`:** selbstdokumentierendes Target im `Makefile` ergänzt (parst `## `-Kommentare hinter den Target-Zeilen).
- **Fix in `Controller::handleException()`:** Die Exception-Message wurde bisher für *jede* Exception mit nicht-leerer Message angezeigt, auch im default-Zweig (500, unbehandelte Exceptions) – dadurch konnten technische Interna (z. B. SQL-/Dateipfad-Details aus `Controller::render()` oder `NeuController::handleActionAntrag()`) beim Nutzer landen. Jetzt wird die Exception-Message nur noch für die explizit im `match` behandelten Typen (`InvalidRouteException`, `NotFoundException`, `AccessDeniedException`, `InvalidCsrfTokenException`, `InvalidUserDataException`) durchgereicht; der default-Zweig zeigt immer die generische Meldung, Details landen nur noch im Error-Log. Geprüft: im gesamten `app/Classes/`-Code wird für absichtlich nutzerlesbare Meldungen bereits ausschließlich `InvalidUserDataException`/`AccessDeniedException` verwendet, keine Stelle verließ sich auf das alte Verhalten.
