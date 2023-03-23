{if empty($isEmbedded)}<h1>Mitglied werden</h1>{/if}

{include file="NeuController/about.tpl"}

{if !empty($datenschutzInfo)}
    <p class="formmeldung">Bitte bestätige die Datenschutzregelungen.</p>
{/if}

<p>Vielen Dank für die Bestätigung deiner E-Mail-Adresse. Du kannst jetzt mit dem Ausfüllen des Antrags fortfahren.</p>

<h2>Mitgliedsantrag</h2>

<form action="{$self}" method="post">
    {include file="antraege/daten.tpl"}
    {include file="datenschutz/form.tpl"}

    <input type="hidden" name="actionAntrag" value="1" />
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
