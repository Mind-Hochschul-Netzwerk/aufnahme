<?php $this->extends('Layout/layout', ['title' => 'Antrag bearbeiten', 'navId' => 'antrag']); ?>

<p><strong>Achtung:</strong> Hier kannst du deinen Mitgliedsantrag <strong>einmalig</strong> bearbeiten. Sobald du deine Änderungen speicherst, wird der Link ungültig!</p>

<h2>Dein Mitgliedsantrag</h2>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <?=$this->render('AntragController/daten')?>
    <p>Bitte beachte, dass du deinen Antrag nur einmal bearbeiten kannst. Wenn du deine Daten speicherst, wird der Bearbeitungslink ungültig.</p>
    <input type="hidden" name="action" value="save" />
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
