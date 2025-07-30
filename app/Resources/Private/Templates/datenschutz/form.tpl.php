<div class="datenschutz-info">
    <h4><?=$kenntnisnahmeTemplate->getSubject()?></h4>
    <?=$kenntnisnahmeTemplate->getFinalTextMarkdown()->raw?>
</div>
<p><label for="kenntnisnahme_datenverarbeitung"><input name="kenntnisnahme_datenverarbeitung" id="kenntnisnahme_datenverarbeitung" type="checkbox" required> Ja, ich nehme zur Kenntnis, dass meine personenbezogenen Daten wie obenstehend verarbeitet und gespeichert werden.</label></p>

<div class="datenschutz-info">
    <h4><?=$einwilligungTemplate->getSubject()?></h4>
    <?=$einwilligungTemplate->getFinalTextMarkdown()->raw?>
</div>
<p><label for="einwilligung_datenverarbeitung"><input name="einwilligung_datenverarbeitung" id="einwilligung_datenverarbeitung" type="checkbox" required> Ja, ich willige ein, dass meine personenbezogenen Daten wie obenstehend verarbeitet und gespeichert werden dürfen.</label></p>
