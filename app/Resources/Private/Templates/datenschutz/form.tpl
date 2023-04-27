<div class="datenschutz-info">
    <h4>{$kenntnisnahmeTemplate->getSubject()|escape}</h4>
    {$kenntnisnahmeTemplate->getFinalTextMarkdown()}
</div>
<p><label for="kenntnisnahme_datenverarbeitung"><input name="kenntnisnahme_datenverarbeitung" id="kenntnisnahme_datenverarbeitung" type="checkbox" required> Ja, ich nehme zur Kenntnis, dass meine personenbezogenen Daten wie obenstehend verarbeitet und gespeichert werden.</label></p>

<div class="datenschutz-info">
    <h4>{$einwilligungTemplate->getSubject()|escape}</h4>
    {$einwilligungTemplate->getFinalTextMarkdown()}
</div>
<p><label for="einwilligung_datenverarbeitung"><input name="einwilligung_datenverarbeitung" id="einwilligung_datenverarbeitung" type="checkbox" required> Ja, ich willige ein, dass meine personenbezogenen Daten wie obenstehend verarbeitet und gespeichert werden dürfen.</label></p>
