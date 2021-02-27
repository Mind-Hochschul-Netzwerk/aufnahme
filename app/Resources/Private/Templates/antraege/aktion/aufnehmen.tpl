<h1>Aufnehmen</h1>

{if !empty($meldung_speichern)}<p><span class="formmeldung">{$meldung_speichern|escape}</span></p>{/if}
{if !empty($meldungen_laden)}<p><div class="formmeldung">{foreach from=$meldungen_laden item=i}{$i|escape}<br />{/foreach}</div></p>{/if}

<p><a class="btn btn-default" href="/antraege/{$antrag->getID()}/">zurück zum Antrag</a></p>

<p>Bei Bestätigung wird:</p>

<ul>
    <li>Das Mitglied in die Wiki-Datenbank übernommen.</li>
    <li>Eine Aufnahme-E-Mail versandt (siehe unten). Dabei wird %aktivierungslink% durch einen Link
        ersetzt, mit dem das neue Mitglied, seinen Benutzeraccount initialisieren kann.
    </li>
    <li>Der Antrags-Status auf "Aufgenommen" und das Entscheidungsdatum auf heute, den {$heute} gesetzt.</li>
</ul>

<h2>E-Mail</h2>

<form action="{$self}" method="post">
<div class="row">
        <div class="col-sm-2">Von</div>
        <div class="col-sm-10">{$absende_email_kand|escape}</div>
    </div>

    <div class="row">
        <div class="col-sm-2">An</div>
        <div class="col-sm-10">{$antrag->getEMail()|escape}</div>
    </div>

    {foreach from=$bcc_email_kand item=i}
    <div class="row">
        <div class="col-sm-2">BCC</div>
        <div class="col-sm-10">{$i|escape}</div>
    </div>
    {/foreach}

    <div class='form-group row '>
        <label for='input-betreff' class='col-sm-2 col-form-label'>Betreff</label>
        <div class='col-sm-10'><input id='input-betreff' name='betreff' class='form-control' value="MHN: Aufnahme"></div>
    </div>

    <div class='form-group row '>
    <label for='input-mailtext' class='col-sm-2 col-form-label'>Inhalt</label>
    <div class="col-sm-10">
    <textarea class="form-control" rows="20" id="input-mailtext" name="mailtext">
Hallo {$antrag->daten->mhn_vorname|escape},

wir freuen uns sehr, dich in unserem Hochschul-Netzwerk zu begrüßen!

Damit das MHN richtig Spaß macht, nimm dir bitte mehr als drei Minuten
Zeit für diese E-Mail! Um das Netzwerk kennenzulernen, findest du in
dieser E-Mail wichtige Informationen zu den unterschiedlichen
Möglichkeiten, mit anderen MHNlern in Kontakt zu treten - wenn etwas
nicht wie unten beschrieben funktioniert, bitte noch einmal bei uns melden.

Klicke bitte auf den folgenden Link, um deinen Zugang zu aktivieren:
%aktivierungslink%

Damit bekommst du Zugriff auf die Datenbank, in der du deine eigenen
Daten verwalten und nach anderen Mitgliedern suchen kannst. Bitte
aktualisiere deine Angaben, wenn sich etwas ändert. Deine Daten
sind nur für MHN-Mitglieder sichtbar und können von Externen nicht
eingesehen werden.

Für die Kommunikation im MHN gibt es Mailinglisten zu unterschiedlichen
Themen und ein Wiki. Die Zugangsdaten für das Wiki sind die gleichen wie
für die Mitgliederverwaltung. Zur Anmeldeseite für das Wiki kommst du
über den Knopf "login" oben rechts auf

https://wiki.mind-hochschul-netzwerk.de/wiki/Hauptseite

Damit du einen guten Überblick bekommst, was im MHN so los ist und wie
die Leute hier "ticken", würden wir dir empfehlen, dass du dich auf die
folgenden Mailinglisten einträgst:

Aktive -- Liste für alle, die aktiv am MHN teilnehmen wollen
wer-weiß-was --- Liste für Fragen aller Art
Ortverteiler -- Verteiler für Leute aus deiner Region

Eintragen kannst du dich in der Mitgliederverwaltung unter:
https://mitglieder.mind-hochschul-netzwerk.de/mailinglisten.php

Die einzige Liste, auf der du automatisch bist, ist die, auf der alle
Mitglieder eingetragen sind. Dort werden die wichtigsten Informationen
vom Vorstand und der Newsletter verteilt.

Um andere MHNler auch live kennenzulernen, besuche die regionalen
Treffen, fahre zu den Seminaren, die dich interessieren, und komme
unbedingt zum jährlichen absoluten Höhepunkt des MHN - der MinD-Akademie
(http://MinD-Akademie.de)!

Eigeninitiative ist im MHN der Schlüssel zum Glück. Also: Wenn dir ein
Angebot fehlt, biete es einfach selbst an! Mitmacher(innen) findest du
garantiert.

Wir wünschen dir viel Spaß im MHN und freuen uns darauf, dich bei einem
der nächsten Treffen persönlich kennenzulernen! Wenn du inhaltliche
Fragen hast, melde dich bei
Mitgliederbetreuung@MinD-Hochschul-Netzwerk.de, für technische Fragen
beim Webteam@MinD-Hochschul-Netzwerk.de.

Viele Grüße,
Die MHN-Aufnahmekommission
</textarea>

        </div>
    </div>

    <input type="hidden" name="formular" value="aufnahme" />

    <div class='row'>
        <div class='col-sm-offset-2 col-sm-10'>
            <input class="btn btn-success" type="submit" value="Bestätigen" />
        </div>
    </div>
</form>
