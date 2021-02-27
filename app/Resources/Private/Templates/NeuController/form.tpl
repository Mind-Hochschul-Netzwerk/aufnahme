<h1>Aufnahmeantrag</h1>

{if !empty($datenschutzInfo)}
    <p class="formmeldung">Bitte bestätige die Datenschutzregelungen.</p>
{/if}

<p>Vielen Dank für die Bestätigung deiner E-Mail-Adresse. Du kannst jetzt mit dem Ausfüllen des Antrags fortfahren.</p>

<form action="{$self}" method="post">
    {include file="antraege/daten.tpl"}

    <div class="datenschutz-info">{include file="datenschutz/kenntnisnahme_text.tpl"}</div>
    <p><label for="kenntnisnahme_datenverarbeitung"><input name="kenntnisnahme_datenverarbeitung" id="kenntnisnahme_datenverarbeitung" type="checkbox" required> Ja, ich nehme zur Kenntnis, dass meine personenbezogenen Daten wie obenstehend verarbeitet und gespeichert werden.</label></p>

    <div class="datenschutz-info">{include file="datenschutz/einwilligung_text.tpl"}</div>
    <p><label for="einwilligung_datenverarbeitung"><input name="einwilligung_datenverarbeitung" id="einwilligung_datenverarbeitung" type="checkbox" required> Ja, ich willige ein, dass meine personenbezogenen Daten wie obenstehend verarbeitet und gespeichert werden dürfen.</label></p>

    <input type="hidden" name="actionAntrag" value="1" />
    <p><input class="btn btn-success" type="submit" value="Daten speichern" /></p>
</form>
