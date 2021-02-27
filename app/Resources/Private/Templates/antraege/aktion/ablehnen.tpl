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
        <div class='col-sm-10'><input id='input-betreff' name='betreff' class='form-control' value="Dein MHN-Aufnahmeantrag"></div>
    </div>

    <div class='form-group row '>
        <label for='input-mailtext' class='col-sm-2 col-form-label'>Inhalt</label>
        <div class="col-sm-10">
        <textarea class="form-control" rows="20" id="input-mailtext" name="mailtext">
Hallo {$antrag->getVorname()|escape},

deine Bewerbung hat uns in vielen Punkten ganz gut gefallen. Dennoch usw.usw.

Viele Grüße
</textarea>
        </div>
    </div>

    <input type="hidden" name="formular" value="ablehnen" />

    <div class='row'>
        <div class='col-sm-offset-2 col-sm-10'>
            <input class="btn btn-success" type="submit" value="Bestätigen" />
        </div>
    </div>
</form>
