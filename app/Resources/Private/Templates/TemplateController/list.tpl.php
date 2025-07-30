<?php $this->extends('Layout/layout', ['title' => 'Vorlagen und Texte bearbeiten', 'navId' => 'templates']); ?>

<ul>
<?php foreach($templates as $template): ?>
    <li><a href="/templates/<?=$template->getValue()->getName()?>"><?=$template->getLabel()?></a></li>
<?php endforeach; ?>
</ul>
