# AK-Tool

Dies ist das Tool der Aufnahmekommission des MHN.

## Container lokal bauen und starten

## Container lokal bauen und starten

    $ make image
    $ make dev

Der Login ist dann im Browser unter [https://aufnahme.docker.localhost/](https://aufnahme.docker.localhost/) erreichbar. Die Sicherheitswarnung wegen des Zertifikates kann weggeklickt werden.

* Benutzername: `webteam`
* Passwort: `webteam`

## Tests ausführen

    composer install
    vendor/bin/phpunit Tests/

## Automatische Updates

Falls Änderungen ein Update an der Datenbank erforderlich machen, kann ein Update-Skript in `update.d` abgelegt werden, das die nötigen Änderungen vornimmt und dann beim Start des Containers geladen wird. Möglich sind PHP-Skripte (Endung .php) und SQL-Dateien (Endung .sql). Schlägt ein SQL-Query fehl, werden die nachfolgenden Queries in der Datei nicht mehr ausgeführt. Nachfolgende Update-Skripte werden aber trotzdem geladen.

