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

{include file="antraege/mailForm.tpl"}
