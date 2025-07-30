<?php $this->extends('Layout/html'); ?>

<?php if (!$this->check($isEmbedded)): ?>
    <?=$this->render('Layout/navigation')?>
<?php endif; ?>

<div class="main"><div class="container-fluid">
    <?=($this->check($title) && !$this->check($isEmbedded)) ? ("<h1>$title</h1>") : ''?>

    <?=$_contents->raw?>
</div></div>
