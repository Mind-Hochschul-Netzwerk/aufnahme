{if !empty($tokenInvalid)}
    <p class="formmeldung">Der verwendete Link ist ungültig (geworden).</p>
{/if}

{if !$isEmbedded}<h1>Mitglied werden</h1>{/if}

{include file="NeuController/about.tpl"}

<h2>Start</h2>

<p>Bitte gib die E-Mail-Adresse an, unter der wir dich erreichen können. Wir schicken dir eine
automatische E-Mail, um die E-Mail-Adresse zu bestätigen und mit dem Antrag fortfahren zu können. Die
E-Mail-Adresse wird noch nicht auf dem Server gespeichert, sondern erst, wenn du den Antrag abschickst.</p>

{if !empty($emailUsed)}
    <p class="formmeldung">Fehler: Ein Antrag mit dieser E-Mail-Adresse wurde bereits gestellt.</p>
{/if}

<form action="{$self}" method="post">
    {include file="antraege/daten-zeile.tpl" name="email" label="E-Mail-Adresse" type="email" required=1}

    <input type="hidden" name="actionEmail" value="1" />

    <div class="form-group row"'>
        <div class='col-sm-4'></div>
        <div class='col-sm-8'><p><input class="btn btn-success" type="submit" value="E-Mail-Adresse bestätigen" /></p></div>
    </div>
</form>

<div style="position: relative; border: solid #999 1px;">
<div style="position: absolute; width:100%; height:100%; z-index: 1; background-color: #fff; opacity: 0.4;"></div>
<div style="position: absolute; width:100%; height:100%; z-index: 2; background-color: #ccc; opacity: 0.2;"></div>
<div style="padding: 0em 1em;">
    <h2>Mitgliedsantrag</h2>
    {include file="antraege/daten.tpl"}
    {include file="datenschutz/form.tpl"}
</div>
</div>
