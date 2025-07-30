<?php $this->extends('Layout/layout', ['title' => 'E-Mail an ' . $antrag->getValue()->getName(), 'navId' => 'antraege']); ?>

<p><a href="/antraege/<?=$antrag->getId()?>/#aktionen">Zurück zum Antrag</a></p>
<p>Mail versandt am <?=$time->format('d.m.Y')?> von <?=$userName?> zwecks <?=$grund?>.</p>
<p><strong>Betreff:</strong> <?=$subject?></p>
<pre>
<?=$text?>
</pre>
