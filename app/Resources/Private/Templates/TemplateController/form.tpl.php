<?php $this->extends('Layout/layout', ['title' => 'Vorlage bearbeiten: ' . $template->getLabel(), 'navId' => 'templates']); ?>

<p><a class="btn btn-default" href="/templates/">zurück zur Übersicht</a></p>

<form method="post">
    <?=$_csrfToken()->inputHidden()?>
    <input type="hidden" name="templateName" value="<?=$template->getName()?>">

    <?php if ($template->getHints()): ?>
        <div class="row">
            <div class='col-sm-2'>Erläuterung:</div>
            <div class="col-sm-10"><pre><?=$template->getHints()?></pre></div>
        </div>
    <?php endif; ?>

    <div class="row">
        <label for='input-subject' class='col-sm-2 col-form-label'>Betreff / Überschrift</label>
        <div class="col-sm-10"><?=$template->getSubject()->input(name: 'subject')?></div>
    </div>

    <div class="row">
        <label for='input-text' class='col-sm-2 col-form-label'>Inhalt</label>
        <div class="col-sm-10"><?=$template->getText()->textarea(name: 'text', rows: 20)?></div>
    </div>

    <div class='row'>
        <div class='col-sm-offset-2 col-sm-10'>
            <input class="btn btn-success" type="submit" value="Speichern" />
        </div>
    </div>
</form>
