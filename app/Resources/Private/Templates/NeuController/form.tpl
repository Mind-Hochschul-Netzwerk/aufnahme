<h1>Aufnahmeantrag</h1>

{if !empty($datenschutzInfo)}
    <p class="formmeldung">Bitte bestätige die Datenschutzregelungen.</p>
{/if}

{if !empty($invalidBirthday)}
    <p class="formmeldung">Fehler: Bitte überprüfe das angegebene Geburtsdatum.</p>
{/if}

<p>Vielen Dank für die Bestätigung deiner E-Mail-Adresse. Du kannst jetzt mit dem Ausfüllen des Antrags fortfahren.</p>

<form action="{$self}" method="post">
    {include file="antraege/daten.tpl"}
    {include file="datenschutz/form.tpl"}

    <input type="hidden" name="actionAntrag" value="1" />
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
