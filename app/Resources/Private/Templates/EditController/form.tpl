<h1>Antrag bearbeiten</h1>

{if !empty($invalidBirthday)}
    <p class="formmeldung">Fehler: Bitte überprüfe das angegebene Geburtsdatum.</p>
{/if}

<h2>Mitgliedsantrag</h2>

<form action="{$self}" method="post">
    {include file="antraege/daten.tpl"}
    <p>Bitte beachte, dass du deinen Antrag nur einmal bearbeiten kannst. Wenn du deine Daten speicherst, wird der Bearbeitungslink ungültig.</p>
    <input type="hidden" name="action" value="save" />
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
