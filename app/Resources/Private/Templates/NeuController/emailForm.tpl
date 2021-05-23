{if !empty($tokenInvalid)}
    <p class="formmeldung">Der verwendete Link ist ungültig (geworden).</p>
{/if}

<h1>Mitgliedsantrag</h1>

<p>Wir freuen uns über dein Interesse, dich aktiv im Mind-Hochschul-Netzwerk (MHN) einzubringen. Das MHN lebt vom ehrenamtlichen Einsatz und der Beteiligung jedes Einzelnen. Aus diesem Grund möchten wir uns vor deiner Aufnahme ein erstes Bild von dir machen und bitten dich, das folgende Formular vollständig und umfassend auszufüllen. Die erhobenen Daten sind nur für MHN-interne Zwecke bestimmt, werden vertraulich behandelt und nicht an Dritte weitergegeben.</p>

<p>Nachdem du dein Formular abgeschickt hast, entscheidet unsere Aufnahmekommission über deinen Beitritt. In der Regel erhältst du innerhalb einiger Wochen eine Rückmeldung über die Entscheidung.</p>

<p>Nach deiner Aufnahme wirst du durch die zuständige Ansprechperson begrüßt und für die internen Bereiche freigeschaltet. Dadurch ermöglichen wir dir, gut in unserem Kreis anzukommen, dich über Angebote anderer zu informieren und dich, deine Ideen und Erfahrungen mit einzubringen.</p>

<p>Noch Fragen? <a href="https://www.mind-hochschul-netzwerk.de/mod/book/view.php?id=253&chapterid=3">Nimm Kontakt mit uns auf.</a></p>

<h2>Start</h2>

<p>Bitte gib die E-Mail-Adresse an, unter der wir dich erreichen können. Wir schicken dir eine automatische E-Mail, um die E-Mail-Adresse zu bestätigen und mit dem Antrag fortfahren zu können. Die E-Mail-Adresse wird noch nicht auf dem Server gespeichert, sondern erst, wenn du den Antrag abschickst.</p>

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
    {include file="antraege/daten.tpl"}
    {include file="datenschutz/form.tpl"}
</div>
</div>
