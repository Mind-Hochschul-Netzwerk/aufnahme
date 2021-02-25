<h1>Detailseite - {$antrag->getName()|escape}</h1>

<div style="position:fixed; left:10px; top:10px; width:90%; text-align: center; background:#ffffcc; padding: 5px; margin: 5%; margin-top:5px; border: 2px solid black;">
    {$antrag->getName()|escape}
</div>

<p><a href="/antraege/">zurück zur Übersicht.</a><br />
Immer nur einen der Punkte auf einmal verändern (also immer den entsprechenden Speichern-Knopf des jeweiligen Abschnitts betätigen!).</p>

{if !empty($meldung)}<p><span class="formmeldung">{$meldung|escape}</span></p>{/if}

<h2>1. Antrags-Status</h2>

<p><b>Hinweis:</b> Der Status und das entsprechende Datum werden bei Aktionen (siehe 3. und die Beschreibungen 
auf den Seiten dort) automatisch gesetzt.<br /> Ansonsten gibt es keine automatischen Änderungen am Status.</p>

<form action="{$self}" method="post">
    <table border="0" cellpadding="3" cellspacing="0">
        <tr><td>Antragsdatum</td><td>{$antrag->getDatumAntrag()}</td></tr>
        <tr><td>Status</td><td><select size="1" name="status">
            {foreach from=$global_status item=s key=i}
                <option value="{$i}" {if $antrag->getStatus() eq $i}selected="selected"{/if}>{$s|escape}</option>
            {/foreach}
            </select> (zuletzt geändert am {$antrag->getDatumStatusaenderung()} von {$antrag->getStatusaenderungUsername()|escape})</td>
        </tr>
        <tr><td>Nachfragedatum</td><td><input type="text" size="10" value="{$antrag->getDatumNachfrage()}" name="datum_nachfrage" /></td></tr>
        <tr><td>Antwortdatum</td><td><input type="text" size="10" value="{$antrag->getDatumAntwort()}" name="datum_antwort" /></td></tr>
        <tr><td>Entscheidungsdatum</td><td><input type="text" size="10" value="{$antrag->getDatumEntscheidung()}" name="datum_entscheidung" /></td></tr>
        <tr><td>Bemerkung</td><td><textarea name="bemerkung" cols="100" rows="5">{$antrag->getBemerkung()|escape}</textarea></td></tr>
    </table>
    <input type="hidden" name="formular" value="speichern_antrag1" />
    <input type="submit" name="sub" value="Speichern" />
</form>

<h2>2. Voten</h2>

<p>(chronologisch sortiert, neueste zuerst)</p>

<form action="{$self}" method="post">
    <table border="1" cellpadding="3" cellspacing="0">
        <tr><th>Wer</th><th>Votum</th><th>Datum</th><th>Bemerkung</th><th>Nachfrage</th></tr>
        {foreach from=$antrag->getVotes() item=i}
            <tr>
                <td>{$i->getRealName()|escape}</td>
                <td>{$i->getValueReadable()}</td>
                <td>{$i->getTime()|date_format:"%d.%m.%Y"}</td><td>{$i->getBemerkung()|escape|nl2br}</td>
                <td>{$i->getNachfrage()|escape|nl2br}</td>
            </tr>
        {/foreach}
        <tr>
            <td>{$entry_username|escape}</td>
            <td>
                <select size="1" name="votum">
                    <option value="0">Nein</option>
                    <option value="1">Ja</option>
                    <option value="2">Nachfragen</option>
                    <option value="3" selected="selected">Enthaltung</option>
                </select>
            </td>
            <td>{$heute}</td>
            <td><textarea rows="8" cols="40" name="bemerkung">{$bemerkung|default|escape}</textarea></td>
            <td><textarea rows="8" cols="40" name="nachfrage">{$nachfrage|default|escape}</textarea></td>
        </tr>
    </table>
    <input type="hidden" name="formular" value="speichern_antrag_voten"/>
    <input type="submit" value="Votum hinzufügen" /> (du wirst wieder auf die Übersichtsseite weitergeleitet).
</form>

<h2>3. Kommentare</h2>

<p>Bisherige Kommentare:
<pre>
{$antrag->getKommentare()|escape}
</pre>
</p>

<p>Kommentar hinzufügen: </p>

<form action="{$self}" method="post">
    <textarea name="kommentar" rows="5" cols="80"></textarea><br />
    <input type="hidden" name="formular" value="speichern_antrag_kommentare" />
    <input type="submit" name="k_add" value="Kommentar hinzufügen" />
    <input type="submit" name="k_edit" value="Kommentare editieren" />
</form>

<h2 id="aktionen">4. Aktionen</h2>

<p>Die Aktionen müssen auf einer eigenen Seite bestätigt werden.</p>

<ul>
    <li><a href="{$self}aufnehmen/">Aufnehmen</a></li>
    <li><a href="{$self}nachfragen/">Nachfragen</a></li>
    <li><a href="{$self}ablehnen/">Ablehnen</a></li>
</ul>

<p>Bereits via Aktionen versandte Mails (neueste zuerst; es werden nur Mails angezeigt, die nach dem 23.10.2010 abgeschickt wurden):</p>

<ul>
    {foreach from=$mails item=mail}
        <li>Von {$mail.userName|escape} am {$mail.time|date_format:"%d.%m.%Y"} zwecks {$mail.grund|escape}; <a href="/mails/{$antrag->getID()}/{$mail.time}">zum Mailtext</a></li>
    {foreachelse}
        <li>(keine)</li>
    {/foreach}
</ul>

<h2>5. Daten aus dem Antrag einsehen</h2>

{include file="antraege/daten.tpl"}
