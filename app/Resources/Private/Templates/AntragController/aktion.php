<?php $this->extends('Layout/layout', ['title' => ucfirst($aktion) . ' – ' . $antrag->getValue()->getName(), 'navId' => 'antraege']); ?>

<?php if ($this->check($meldung_speichern)): ?><p><span class="formmeldung"><?=$meldung_speichern?></span></p><?php endif; ?>
<?php if ($this->check($meldungen_laden)): ?><p><div class="formmeldung"><?=$meldungen_laden?></div></p><?php endif; ?>

<p><a class="btn btn-default" href="/antraege/<?=$antrag->getID()?>/">zurück zum Antrag</a></p>

<p>Bei Bestätigung wird:</p>

<ul>
    <?php if ($aktion == 'aufnehmen'): ?>
        <li>Eine Aufnahme-E-Mail mit einem Zugangscode versandt (siehe unten).</li>
        <li>Der Antrags-Status auf "Aufgenommen" und das Entscheidungsdatum auf heute, den <?=$heute?> gesetzt.</li>
    <?php elseif ($aktion == 'ablehnen'): ?>
        <li>Eine Ablehn-Mail versandt (siehe unten).</li>
        <li>Der Antrags-Status auf "Abgelehnt" gesetzt und das Entscheidungsdatum auf heute, den <?=$heute?>
            (und damit implizit der Antrag ins Archiv verschoben).
        </li>
    <?php else: ?>
        <li>Eine Nachfrage-E-Mail versandt (siehe unten).</li>
        <li>Der Antrags-Status auf "Auf Antwort warten" und das Nachfragedatum auf heute, den <?=$heute?> gesetzt.</li>
    <?php endif; ?>
</ul>

<h2>E-Mail</h2>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <div class="row">
        <div class="col-sm-2">Von</div>
        <div class="col-sm-10"><?=$absende_email_kand?></div>
    </div>

    <div class="row">
        <div class="col-sm-2">An</div>
        <div class="col-sm-10"><?=$antrag->getEMail()?></div>
    </div>

    <div class='form-group row '>
        <label for='input-betreff' class='col-sm-2 col-form-label'>Betreff</label>
        <div class='col-sm-10'><?=$mailSubject->input(name: 'betreff')?></div>
    </div>

    <div class='form-group row '>
        <label for='input-mailtext' class='col-sm-2 col-form-label'>Inhalt</label>
        <div class="col-sm-10"><?=$mailText->textarea(name: 'mailtext', rows: 20)?></div>
    </div>

    <div class='row'>
        <div class='col-sm-offset-2 col-sm-10'>
            <input class="btn btn-success" type="submit" value="Bestätigen" />
        </div>
    </div>
</form>
