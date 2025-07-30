<?php $this->extends('Layout/layout', ['title' => $antrag->getValue()->getName(), 'navId' => 'antraege']); ?>

<p>Immer nur einen der Punkte auf einmal verändern (also immer den entsprechenden Speichern-Knopf des jeweiligen Abschnitts betätigen!).</p>

<?php if ($this->check($meldung)): ?>
    <p><span class="formmeldung"><?=$meldung?></span></p>
<?php endif; ?>

<h2>Status des Antrags</h2>

<p><strong>Hinweis:</strong> Der Status und das entsprechende Datum werden bei Aktionen (siehe 3. und die Beschreibungen
auf den Seiten dort) automatisch gesetzt. Ansonsten gibt es keine automatischen Änderungen am Status.</p>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <input type="hidden" name="formular" value="grunddaten" />

    <div class="row">
        <div class="col-sm-2">Antragsdatum</div>
        <div class="col-sm-10"><?=$antrag->getDatumAntrag()?></div>
    </div>

    <div class='form-group row '>
        <label for='input-status' class='col-sm-2 col-form-label'>Status</label>
        <div class='col-sm-10'>
            <select id='input-status' name='status' class='form-control' title='Status'>
                <?php foreach ($statuscodes as $i=>$s): ?>
                    <option value="<?=$i?>" <?php if ($antrag->getStatus()->getValue() == $i): ?>selected="selected"<?php endif; ?>><?=$s?></option>
                <?php endforeach; ?>
            </select>
            (zuletzt geändert am <?=$antrag->getDatumStatusaenderung()?> von <?=$antrag->getStatusaenderungUsername()?>)
        </div>
    </div>

    <div class='form-group row '>
        <label for='input-datum_nachfrage' class='col-sm-2 col-form-label'>Nachfragedatum</label>
        <div class='col-sm-10'><?=$antrag->getDatumNachfrage()->input(name: 'datum_nachfrage')?></div>
    </div>

    <div class='form-group row '>
        <label for='input-datum_antwort' class='col-sm-2 col-form-label'>Antwortdatum</label>
        <div class='col-sm-10'><?=$antrag->getDatumAntwort()->input(name: 'datum_antwort')?></div>
    </div>
    <div class='form-group row '>
        <label for='input-datum_entscheidung' class='col-sm-2 col-form-label'>Entscheidungsdatum</label>
        <div class='col-sm-10'><?=$antrag->getDatumEntscheidung()->input(name: 'datum_entscheidung')?></div>
    </div>

    <div class='form-group row '>
        <label for='input-datum_entscheidung' class='col-sm-2 col-form-label'>Bemerkung</label>
        <div class='col-sm-10'><?=$antrag->getBemerkung()->textarea(name: 'bemerkung')?></div>
    </div>

    <p><input class="btn btn-success" type="submit" name="sub" value="Speichern" /></p>
</form>

<h2>Voten</h2>

<p>(chronologisch sortiert, neueste zuerst)</p>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <input type="hidden" name="formular" value="votes"/>

    <table class="table">
        <tr><th>Wer</th><th>Votum</th><th>Datum</th><th>Bemerkung</th><th>Nachfrage</th></tr>
        <?php foreach ($antrag->getVotes() as $i): ?>
            <tr>
                <td><?=$i->getRealName()?></td>
                <td><?=$i->getValueReadable()?></td>
                <td><?=$i->getTime()->format("d.m.Y")?></td><td><?=nl2br($i->getBemerkung())?></td>
                <td><?=nl2br($i->getNachfrage())?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><?=$currentUser->getRealName()?></td>
            <td>
                <select class="form-control" name="votum">
                    <option value="0">Nein</option>
                    <option value="1">Ja</option>
                    <option value="2">Nachfragen</option>
                    <option value="3" selected="selected">Enthaltung</option>
                </select>
            </td>
            <td><?=$heute?></td>
            <td><textarea class="form-control" rows="8" name="bemerkung"><?=$this->default($bemerkung)?></textarea></td>
            <td><textarea class="form-control" rows="8" name="nachfrage"><?=$this->default($nachfrage)?></textarea></td>
        </tr>
    </table>
    <input class="btn btn-success" type="submit" value="Votum hinzufügen" /> (du wirst wieder auf die Übersichtsseite weitergeleitet).
</form>

<h2>Kommentare</h2>

<pre>
<?=$antrag->getKommentare()?>
</pre>

<p>Kommentar hinzufügen: </p>

<form action="/antraege/<?=$antrag->getId()?>/kommentare" method="post">
    <?=$_csrfToken()->inputHidden()?>
    <textarea class="form-control" name="kommentar" rows="5"></textarea><br />
    <input class="btn btn-success" type="submit" value="Kommentar hinzufügen" />
    <a class="btn btn-default" href="/antraege/<?=$antrag->getId()?>/kommentare">Kommentare editieren</a>
</form>

<h2 id="aktionen">Aktionen</h2>

<p>
    <a class="btn btn-success" href="/antraege/<?=$antrag->getId()?>/aufnehmen">Aufnehmen</a>
    <a class="btn btn-default" href="/antraege/<?=$antrag->getId()?>/nachfragen">Nachfragen</a>
    <a class="btn btn-danger" href="/antraege/<?=$antrag->getId()?>/ablehnen">Ablehnen</a>
</p>

<p>Bereits via Aktionen versandte Mails:</p>

<ul>
    <?php foreach ($mails as $mail): ?>
        <li>Von <?=$mail->userName?> am <?=$mail->time->format("d.m.Y")?> zwecks <?=$mail->grund?>; <a href="/mails/<?=$antrag->getId()?>/<?=$mail->time->getTimestamp()?>">zum Mailtext</a></li>
    <?php endforeach; ?>
    <?=$this->unless($mails, '<li>keine</li>')?>
</ul>

<h2>5. Daten aus dem Antrag einsehen</h2>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <input type="hidden" name="formular" value="daten" />
    <?=$this->render('AntragController/daten')?>
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
