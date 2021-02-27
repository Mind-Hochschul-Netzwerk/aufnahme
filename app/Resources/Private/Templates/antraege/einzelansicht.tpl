<h1>Detailseite - {$antrag->getName()|escape}</h1>

<p>Immer nur einen der Punkte auf einmal verändern (also immer den entsprechenden Speichern-Knopf des jeweiligen Abschnitts betätigen!).</p>

{if !empty($meldung)}<p><span class="formmeldung">{$meldung|escape}</span></p>{/if}

<h2>Status des Antrags</h2>

<p><strong>Hinweis:</strong> Der Status und das entsprechende Datum werden bei Aktionen (siehe 3. und die Beschreibungen
auf den Seiten dort) automatisch gesetzt. Ansonsten gibt es keine automatischen Änderungen am Status.</p>

<form action="{$self}" method="post">
    <div class="row">
        <div class="col-sm-2">Antragsdatum</div>
        <div class="col-sm-10">{$antrag->getDatumAntrag()}</div>
    </div>

    <div class='form-group row '>
        <label for='input-status' class='col-sm-2 col-form-label'>Status</label>
        <div class='col-sm-10'>
            <select id='input-status' name='status' class='form-control' title='Status'>
                {foreach from=$global_status item=s key=i}
                    <option value="{$i}" {if $antrag->getStatus() eq $i}selected="selected"{/if}>{$s|escape}</option>
                {/foreach}
            </select>
             (zuletzt geändert am {$antrag->getDatumStatusaenderung()} von {$antrag->getStatusaenderungUsername()|escape})
        </div>
    </div>

    <div class='form-group row '>
        <label for='input-datum_nachfrage' class='col-sm-2 col-form-label'>Nachfragedatum</label>
        <div class='col-sm-10'><input id='input-datum_nachfrage' name='datum_nachfrage' class='form-control' value="{$antrag->getDatumNachfrage()}"></div>
    </div>

    <div class='form-group row '>
        <label for='input-datum_antwort' class='col-sm-2 col-form-label'>Antwortdatum</label>
        <div class='col-sm-10'><input id='input-datum_antwort' name='datum_antwort' class='form-control' value="{$antrag->getDatumAntwort()}"></div>
    </div>
    <div class='form-group row '>
        <label for='input-datum_entscheidung' class='col-sm-2 col-form-label'>Entscheidungsdatum</label>
        <div class='col-sm-10'><input id='input-datum_entscheidung' name='datum_entscheidung' class='form-control' value="{$antrag->getDatumEntscheidung()}"></div>
    </div>

    <div class='form-group row '>
        <label for='input-datum_entscheidung' class='col-sm-2 col-form-label'>Bemerkung</label>
        <div class='col-sm-10'><textarea id='input-bemerkung' name='bemerkung' class='form-control'>{$antrag->getBemerkung()|escape}</textarea></div>
    </div>

    <input type="hidden" name="formular" value="speichern_antrag1" />

    <p><input class="btn btn-success" type="submit" name="sub" value="Speichern" /></p>
</form>

<h2>Voten</h2>

<p>(chronologisch sortiert, neueste zuerst)</p>

<form action="{$self}" method="post">
    <table class="table">
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
                <select class="form-control" name="votum">
                    <option value="0">Nein</option>
                    <option value="1">Ja</option>
                    <option value="2">Nachfragen</option>
                    <option value="3" selected="selected">Enthaltung</option>
                </select>
            </td>
            <td>{$heute}</td>
            <td><textarea class="form-control" rows="8" name="bemerkung">{$bemerkung|default|escape}</textarea></td>
            <td><textarea class="form-control" rows="8" name="nachfrage">{$nachfrage|default|escape}</textarea></td>
        </tr>
    </table>
    <input type="hidden" name="formular" value="speichern_antrag_voten"/>
    <input class="btn btn-success" type="submit" value="Votum hinzufügen" /> (du wirst wieder auf die Übersichtsseite weitergeleitet).
</form>

<h2>Kommentare</h2>

<pre>
{$antrag->getKommentare()|escape}
</pre>

<p>Kommentar hinzufügen: </p>

<form action="{$self}" method="post">
    <textarea class="form-control" name="kommentar" rows="5"></textarea><br />
    <input type="hidden" name="formular" value="speichern_antrag_kommentare" />
    <input class="btn btn-success" type="submit" name="k_add" value="Kommentar hinzufügen" />
    <input class="btn btn-default" type="submit" name="k_edit" value="Kommentare editieren" />
</form>

<h2 id="aktionen">Aktionen</h2>

<p>
    <a class="btn btn-success" href="{$self}aufnehmen/">Aufnehmen</a>
    <a class="btn btn-default" href="{$self}nachfragen/">Nachfragen</a>
    <a class="btn btn-danger" href="{$self}ablehnen/">Ablehnen</a>
</p>

<p>Bereits via Aktionen versandte Mails:</p>

<ul>
    {foreach from=$mails item=mail}
        <li>Von {$mail.userName|escape} am {$mail.time|date_format:"%d.%m.%Y"} zwecks {$mail.grund|escape}; <a href="/mails/{$antrag->getID()}/{$mail.time}">zum Mailtext</a></li>
    {foreachelse}
        <li>(keine)</li>
    {/foreach}
</ul>

<h2>5. Daten aus dem Antrag einsehen</h2>

<form action="{$self}" method="post">
    {include file="antraege/daten.tpl"}
    <input type="hidden" name="formular" value="speichern_antrag_daten" />
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
