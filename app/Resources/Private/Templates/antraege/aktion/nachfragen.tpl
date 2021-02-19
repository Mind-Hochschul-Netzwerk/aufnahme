<h1>Nachfragen</h1>

{if !empty($meldung_speichern)}<p><span class="formmeldung">{$meldung_speichern|escape}</span></p>{/if}
{if !empty($meldungen_laden)}<p><div class="formmeldung">{foreach from=$meldungen_laden item=i}{$i|escape}<br />{/foreach}</div></p>{/if}

<p><a href="/antraege/{$antrag->getID()}/">zurück zum Antrag</a><br/>Bei Bestätigung wird:</p>
<ul>
    <li>Eine Nachfrage-E-Mail versandt (siehe unten).</li>
    <li>Der Antrags-Status auf "Auf Antwort warten" und das Nachfragedatum auf heute, den {$heute} gesetzt.</li>
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
            <input type="text" name="betreff" value="Nachfrage zum MHN-Aufnahmeantrag" size="70"/>
        </td></tr>
    </table>

<textarea rows="30" cols="100" name="mailtext">
Hallo {$antrag->getVorname()|escape},

wir haben deinen Antrag zur Aufnahme bei MHN erhalten. Jedoch haben
sich bei der Bearbeitung noch Punkte für eine Nachfrage ergeben:

{foreach from=$antrag->getVotes() item=v}{if $v->getValue() === MHN\Aufnahme\Domain\Model\Vote::NACHFRAGEN}{$v->getNachfrage()|escape}

{/if}{/foreach}

Viele Grüße

</textarea><br />

    <input type="hidden" name="formular" value="nachfrage" />
    <input type="submit" value="Bestätigen" />
</form>
