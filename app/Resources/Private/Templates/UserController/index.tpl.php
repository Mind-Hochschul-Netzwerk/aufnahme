<?php $this->extends('Layout/layout', ['title' => 'MHN-Mitglieder mit Zugriff auf das Aufnahmetool', 'navId' => 'users']); ?>

<div class="table">
    <table class="table" >
        <tr><th>Benutzername</th><th>Name</th></tr>
        <?php foreach ($webusers as $w): ?>
            <td><?=$w->getUsername()?></td><td><?=$w->getRealname()?></td></tr>
        <?php endforeach; ?>
    </table>
</div>
<p>Für Änderungen wendet euch bitte an die Mitgliederbetreuung.</p>
