<h1>Ablehnen</h1>

{if !empty($meldung_speichern)}<p><span class="formmeldung">{$meldung_speichern|escape}</span></p>{/if}
{if !empty($meldungen_laden)}<p><div class="formmeldung">{foreach from=$meldungen_laden item=i}{$i|escape}<br />{/foreach}</div></p>{/if}

<p><a class="btn btn-default" href="/antraege/{$antrag->getID()}/">zurück zum Antrag</a></p>

<p>Bei Bestätigung wird:</p>

<ul>
    <li>Eine Ablehn-Mail versandt (siehe unten).</li>
    <li>Der Antrags-Status auf "Abgelehnt" gesetzt und das Entscheidungsdatum auf heute, den {$heute}
        (und damit implizit der Antrag ins Archiv verschoben).
    </li>
</ul>

{include file="antraege/mailForm.tpl"}
