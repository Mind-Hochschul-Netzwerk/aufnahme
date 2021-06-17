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

    <div class='form-group row '>
        <label for='input-betreff' class='col-sm-2 col-form-label'>Betreff</label>
        <div class='col-sm-10'><input id='input-betreff' name='betreff' class='form-control' value="MHN: Aufnahme"></div>
    </div>

    <div class='form-group row '>
        <label for='input-mailtext' class='col-sm-2 col-form-label'>Inhalt</label>
        <div class="col-sm-10"><textarea class="form-control" rows="20" id="input-mailtext" name="mailtext">{include file="mails/aufnehmen.tpl"}</textarea></div>
    </div>

    <input type="hidden" name="formular" value="aufnahme" />

    <div class='row'>
        <div class='col-sm-offset-2 col-sm-10'>
            <input class="btn btn-success" type="submit" value="Bestätigen" />
        </div>
    </div>
</form>
