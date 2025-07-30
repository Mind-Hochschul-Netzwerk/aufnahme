<?php $this->extends('Layout/layout', ['title' => $introTemplate->getSubject(), 'navId' => 'antrag']); ?>

<?=$introTemplate->getFinalTextMarkdown()->raw?>

<h2>Start</h2>

<p>Bitte gib die E-Mail-Adresse an, unter der wir dich erreichen können. Wir schicken dir eine
automatische E-Mail, um die E-Mail-Adresse zu bestätigen und mit dem Antrag fortfahren zu können. Die
E-Mail-Adresse wird noch nicht auf dem Server gespeichert, sondern erst, wenn du den Antrag abschickst.</p>

<?=$this->if($emailUsed, '<p class="formmeldung">Fehler: Ein Antrag mit dieser E-Mail-Adresse wurde bereits gestellt.</p>')?>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>

    <?=$this->render('AntragController/daten-zeile', [
        'name' => 'email',
        'value' => $email,
        'label' => 'E-Mail-Adresse',
        'type' => 'email',
        'required' => true,
    ])?>

    <div class="form-group row">
        <div class='col-sm-4'></div>
        <div class='col-sm-8'><p><input class="btn btn-success" type="submit" value="E-Mail-Adresse bestätigen" /></p></div>
    </div>
</form>

<div style="position: relative; border: solid #999 1px;">
<div style="position: absolute; width:100%; height:100%; z-index: 1; background-color: #fff; opacity: 0.4;"></div>
<div style="position: absolute; width:100%; height:100%; z-index: 2; background-color: #ccc; opacity: 0.2;"></div>
<div style="padding: 0em 1em;">
    <h2>Mitgliedsantrag</h2>
    <?=$this->render('AntragController/daten')?>
    <?=$this->render('datenschutz/form')?>
</div>
</div>
