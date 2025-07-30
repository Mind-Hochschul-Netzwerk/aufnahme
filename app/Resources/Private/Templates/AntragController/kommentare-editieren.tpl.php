<?php $this->extends('Layout/layout', ['title' => 'Kommentare editieren – ' . $antrag->getValue()->getName(), 'navId' => 'antraege']); ?>

<p><a href="/antraege/<?=$antrag->getID()?>/">zurück zur Detailseite</a></p>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <?=$antrag->getKommentare()->textarea(name: 'kommentare', rows: 30)?><br />
    <input type="submit" value="Speichern" />
    <p><a href="/antraege/<?=$antrag->getID()?>/">abbrechen / zurück zur Detailseite</a></p>
</form>
