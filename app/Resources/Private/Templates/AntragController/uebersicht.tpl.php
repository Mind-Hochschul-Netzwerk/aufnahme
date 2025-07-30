<?php $this->extends('Layout/layout', ['title' => 'Offene Anträge', 'navId' => 'antraege']); ?>

<?php if ($this->check($nichtvonmirgevotet)): ?>
    <p><em>Hinweis:</em> Es werden im Augenblick nur offene Anträge gezeigt, bei denen du entweder noch kein Votum abgegeben hast,
    oder die den Status "neu bewerten" haben und dein Votum älter ist als diese Statusänderung.</p>
    <p><a href="/antraege/">Alle offene Anträge zeigen</a></p>
<?php else: ?>
    <p><a href="/antraege/nichtvonmirgevotet/">Nur Anträge anzeigen, für die ich noch abstimmen muss</a></p>
<?php endif; ?>

<p>Farben: rot für Anträge, die älter sind als 4 Wochen, gelb für Anträge älter als 2.<br />
Der Status wird grün eingefärbt, falls es 3 oder mehr "Ja"- bei keinen "Nein"- und "Nachfragen"-Voten gibt.<br/>
Beim Status "Auf Antwort warten" ist die Farbe grau, egal wie alt er ist.</p>

<div class="table-responsive"><table class="table">
    <tr><th>Nr.</th><th>Antragssteller</th><th>Antragsdatum</th><th>Status</th><th>Statusdatum</th>
        <?php foreach ($userNames_gevotet as $i): ?>
            <th><?=$realNames->$i?></th>
        <?php endforeach; ?>
        <th>Bemerkung</th>
    </tr>
    <?php foreach ($antraege as $index=>$a): ?>
        <tr>
            <td><a href="/antraege/<?=$a->getId()?>/"><?=$a->getId()?></a></td>
            <td class="UA<?=$a->getDringlichkeit()?>"><?=$a->getValue()->getName()?></td>
            <td class="UA<?=$a->getDringlichkeit()?>"><?=$a->getDatumAntrag()?></td>
            <td<?=empty($a->getValue()->getGruen()) ? '' : ' class="gruen"'?>><?=$a->getStatusReadable()?></td>
            <td><?=$a->getDatumStatusaenderung()?></td>
            <?php foreach ($userNames_gevotet as $userName): ?>
                <td class="<?=$a->getLatestVoteColorByUserName($userName->getValue())?>"><?=$a->getLatestVoteReadableByUserName($userName->getValue())?></td>
            <?php endforeach; ?>
            <td><?=$a->getBemerkung()?></td>
        </tr>
    <?php endforeach; ?>
</table></div>
