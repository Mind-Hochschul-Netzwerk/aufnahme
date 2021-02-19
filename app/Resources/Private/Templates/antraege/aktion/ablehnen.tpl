<h1>Ablehnen</h1>

{if !empty($meldung_speichern)}<p><span class="formmeldung">{$meldung_speichern|escape}</span></p>{/if}
{if !empty($meldungen_laden)}<p><div class="formmeldung">{foreach from=$meldungen_laden item=i}{$i|escape}<br />{/foreach}</div></p>{/if}

<p><a href="/antraege/{$antrag->getID()}/">zurück zum Antrag</a><br/>Bei Bestätigung wird:</p>

<ul>
    <li>Eine Ablehn-Mail versandt (siehe unten).</li>
    <li>Der Antrags-Status auf "Abgelehnt" gesetzt und das Entscheidungsdatum auf heute, den {$heute}
        (und damit implizit der Antrag ins Archiv verschoben).
    </li>
</ul>

<h2>E-Mail</h2>

<form action="{$self}" method="post">
    <table border="0" cellpadding="3" cellspacing="0">
        <tr><td>Von:</td><td>{$absende_email_kand|escape}</td></tr>
        <tr><td>An:</td><td>{$antrag->getEMail()|escape}</td></tr>
        {foreach from=$bcc_email_kand item=i}
            <tr><td>BCC:</td><td>{$i|escape}</td></tr>
        {/foreach}
        <tr><td>Betreff:</td><td>
            <input type="text" name="betreff" value="Dein MHN-Aufnahmeantrag" size="70"/>
        </td></tr>
    </table>

<textarea rows="30" cols="100" name="mailtext">
Hallo {$antrag->getVorname()|escape},

deine Bewerbung hat uns in vielen Punkten ganz gut gefallen. Dennoch usw.usw.

Viele Grüße

</textarea>
    <br />
    <input type="hidden" name="formular" value="ablehnen" />
    <input type="submit" value="Bestätigen" />
</form>
