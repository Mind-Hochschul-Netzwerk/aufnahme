# AK-Tool

Dies ist das Tool der Aufnahmekommission des MHN.

## Starten

[php-base](https://github.com/Mind-Hochschul-Netzwerk/php-base) muss bereits gebaut sein.

### Target "dev" (Entwicklung)

    $ composer install -d app
    $ make rebuild
    $ make dev

Der Login ist dann im Browser unter [https://aufnahme.docker.localhost/](https://aufnahme.docker.localhost/) erreichbar. Die Sicherheitswarnung wegen des Zertifikates kann weggeklickt werden.

* Benutzername: `webteam`
* Passwort: `webteam1`

### Target "prod" (Production)

    $ make prod
