# AK-Tool

Dies ist das Tool der Aufnahmekommission des MHN.

## Starten

[php-base](https://github.com/Mind-Hochschul-Netzwerk/php-base) muss bereits gebaut sein.

### Target "dev" (Entwicklung)

    $ composer install -d app
    $ make quick-image
    $ make dev

Der Login ist dann im Browser unter [https://aufnahme.docker.localhost/](https://aufnahme.docker.localhost/) erreichbar. Die Sicherheitswarnung wegen des Zertifikates kann weggeklickt werden.

* Benutzername: `webteam`
* Passwort: `webteam1`

### Target "prod" (Production)

    $ make prod

## Automatische Updates

Falls Änderungen ein Update an der Datenbank erforderlich machen, kann ein Update-Skript in `update.d` abgelegt werden, das die nötigen Änderungen vornimmt und dann beim Start des Containers geladen wird. Möglich sind PHP-Skripte (Endung .php) und SQL-Dateien (Endung .sql). Schlägt ein SQL-Query fehl, werden die nachfolgenden Queries in der Datei nicht mehr ausgeführt. Nachfolgende Update-Skripte werden aber trotzdem geladen.

