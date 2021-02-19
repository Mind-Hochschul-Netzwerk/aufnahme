<h1>Kommentare editieren - {$antrag->getName()|escape}</h1>

<p><a href="/antraege/{$antrag->getID()}/">zurück zur Detailseite</a></p>

<form action="{$self}" method="post">
    <textarea rows="30" cols="80" name="kommentare">{$antrag->getKommentare()|escape}</textarea><br />
    <input type="hidden" name="formular" value="kommentare_editieren" />
    <input type="submit" value="Speichern" />
    <p><a href="/antraege/{$antrag->getID()}/">abbrechen / zurück zur Detailseite</a></p>
</form>
