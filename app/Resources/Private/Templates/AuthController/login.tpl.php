<?php $this->extends('Layout/layout', ['title' => 'Bitte anmelden', 'navId' => 'login']); ?>

<?php if ($this->check($message)): ?>
    <p><span class="formmeldung"><?=$message?></span></p>
<?php endif; ?>

<form method="post" action="/login">
    <?=$_csrfToken()->inputHidden()?>
    <?=$redirect->inputHidden()?>

    <p style="text-align:center;border:2px solid #555; background:#eee;padding:2em;">
    <b>Benutzername:</b><br /><?=$login->input(placeholder: "Benutzername", size: 20)?><br />
    <b>Passwort:</b><br /><?=$password->input(type: 'password', placeholder: "Passwort", size: 20)?><br /><br />
    <input type="submit" value="login" id="blogin" />
    </p>
</form>

