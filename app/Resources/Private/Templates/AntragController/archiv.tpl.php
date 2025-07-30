<?php $this->extends('Layout/layout', ['title' => 'Entschiedene Anträge', 'navId' => 'archiv']); ?>

<p>Aus Datenschutzgründen werden die Anträge gelöscht, wenn Sie nicht mehr benötigt werden.</p>

<ul>
    <li>Angenommene Anträge werden acht Wochen lang aufbewahrt, damit ggf. Fehler festgestellt werden können.</li>
    <li>Sie bleiben allerdings bis zu einem Jahr im System, solange das Mitglied seinen Zugang nicht aktiviert hat.</li>
    <li>Abgelehnte Anträge werden wegen der Einspruchsmöglichkeit bei der Mitgliederversammlung 60 Wochen lang aufbewahrt</li>
</ul>

<table class="table">
    <tr><th>Nr.</th><th>Antragssteller</th><th>Entscheidungsdatum</th><th>Status</th><th>Voten</th><th>Bemerkung</th></tr>
    <?php foreach ($antraege as $index=>$a): ?>
        <tr>
            <td><a href="/antraege/<?=$a->getId()?>/"><?=$a->getId()?></a></td>
            <td><?=$a->getValue()->getName()?></td>
            <td><?=$a->getDatumEntscheidung()?></td>
            <td><?=$a->getStatusReadable()?></td>
            <td>
                <?php foreach ($a->getVotes() as $v): ?>
                    <?=$v->getUsername()?>: <?=$v->getValueReadable()?><br />
                <?php endforeach; ?>
            </td>
            <td><?=$a->getBemerkung()?></td>
        </tr>
    <?php endforeach; ?>
</table>
