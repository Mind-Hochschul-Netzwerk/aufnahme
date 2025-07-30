<?php $this->extends('Layout/layout', ['title' => $introTemplate->getSubject(), 'navId' => 'antrag']); ?>

<?=$introTemplate->getFinalTextMarkdown()->raw?>

<?=$this->if($datenschutzInfo, '<p class="formmeldung">Bitte bestätige die Datenschutzregelungen.</p>')?>

<p>Vielen Dank für die Bestätigung deiner E-Mail-Adresse. Du kannst jetzt mit dem Ausfüllen des Antrags fortfahren.</p>

<h2>Mitgliedsantrag</h2>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <?=$this->render('AntragController/daten')?>
    <?=$this->render('datenschutz/form')?>

    <input type="hidden" name="actionAntrag" value="1" />
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
