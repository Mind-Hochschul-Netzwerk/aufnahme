<h3>Basisdaten</h3>

<p>Die Daten in diesem Abschnitt sind für den Aufnahmeantrag erforderlich.</p>

<div class="form-group row"'>
    <label for='input-mhn_vorname' class='col-sm-4 col-form-label'>Vor- und Nachname</label>
    {include file="antraege/inputElement.tpl" name="mhn_vorname" value=$werte.mhn_vorname width=4 required=1}
    {include file="antraege/inputElement.tpl" name="mhn_nachname" value=$werte.mhn_nachname width=4 required=1}
</div>

{include file='antraege/daten-zeile.tpl' name='user_email' label='E-Mail-Adresse' value=$werte.user_email type="email" disabled=1}
{include file='antraege/daten-zeile.tpl' name='mhn_geburtstag' label='Geburtsdatum (TT.MM.JJJJ)' value=$werte.mhn_geburtstag type="date" required=1}

<div class="form-group row"'>
    <label for='input-mhn_ws_strasse' class='col-sm-4 col-form-label'>Straße / Hausnummer</label>
    {include file="antraege/inputElement.tpl" name="mhn_ws_strasse" value=$werte.mhn_ws_strasse width=4 required=1}
    {include file="antraege/inputElement.tpl" name="mhn_ws_hausnr" value=$werte.mhn_ws_hausnr width=4 required=1}
</div>

{include file='antraege/daten-zeile.tpl' name='mhn_ws_zusatz' label='evtl. Adresszusatz' value=$werte.mhn_ws_zusatz}

<div class="form-group row"'>
    <label for='input-mhn_ws_plz' class='col-sm-4 col-form-label'>PLZ / Ort</label>
    {include file="antraege/inputElement.tpl" name="mhn_ws_plz" value=$werte.mhn_ws_plz width=4 required=1}
    {include file="antraege/inputElement.tpl" name="mhn_ws_ort" value=$werte.mhn_ws_ort width=4 required=1}
</div>

{include file='antraege/daten-zeile.tpl' name='mhn_ws_land' label='Land' value=$werte.mhn_ws_land required=1}

<h3>Zusatzdaten</h3>

<p>Die weiteren Angaben sind Zusatzdaten, deren Angabe freiwillig ist.</p>

<h4>Allgemeine Daten</h4>

<div class="form-group row"'>
    <label for='input-mhn_geschlecht' class='col-sm-4 col-form-label'>Geschlecht</label>
    <div class='col-sm-8'><select id='input-mhn_geschlecht' name='mhn_geschlecht' class='form-control'>
            <option value="" {if $werte.mhn_geschlecht eq ''}selected{/if}></option>
            <option value="w" {if $werte.mhn_geschlecht eq 'w'}selected{/if}/>weiblich</option>
            <option value="m" {if $werte.mhn_geschlecht eq 'm'}selected{/if}/>männlich</option>
            <option value="d" {if $werte.mhn_geschlecht eq 'd'}selected{/if}/>divers</option>
        </select>
    </div>
</div>

{include file='antraege/daten-zeile.tpl' name='mhn_titel' label='akad. Titel, falls vorhanden' value=$werte.mhn_titel}

<div class="form-group row"'>
    <label for='input-mhn_beschaeftigung' class='col-sm-4 col-form-label'>derzeitige Beschäftigung</label>
    <div class='col-sm-8'><select id='input-mhn_beschaeftigung' name='mhn_beschaeftigung' class='form-control'>
            <option value="Hochschulstudent" selected="selected">Hochschulstudent</option>
            <option value="Schueler" {if $werte.mhn_beschaeftigung eq "Schueler"}selected="selected"{/if}>Schüler</option>
            <option value="Doktorand" {if $werte.mhn_beschaeftigung eq "Doktorand"}selected="selected"{/if}>Doktorand</option>
            <option value="Berufstaetig" {if $werte.mhn_beschaeftigung eq "Berufstaetig"}selected="selected"{/if}>Berufstätig</option>
            <option value="Sonstiges" {if $werte.mhn_beschaeftigung eq "Sonstiges"}selected="selected"{/if}>Sonstiges</option>
        </select>
    </div>
</div>

<h4>Zweitwohnsitz (z.B. Adresse der Eltern)</h4>

<div class="form-group row"'>
    <label for='input-mhn_zws_strasse' class='col-sm-4 col-form-label'>Straße / Hausnummer</label>
    {include file="antraege/inputElement.tpl" name="mhn_zws_strasse" value=$werte.mhn_zws_strasse width=4}
    {include file="antraege/inputElement.tpl" name="mhn_zws_hausnr" value=$werte.mhn_zws_hausnr width=4}
</div>

{include file='antraege/daten-zeile.tpl' name='mhn_zws_zusatz' label='evtl. Adresszusatz' value=$werte.mhn_zws_zusatz}

<div class="form-group row"'>
    <label for='input-mhn_zws_plz' class='col-sm-4 col-form-label'>PLZ / Ort</label>
    {include file="antraege/inputElement.tpl" name="mhn_zws_plz" value=$werte.mhn_zws_plz width=4}
    {include file="antraege/inputElement.tpl" name="mhn_zws_ort" value=$werte.mhn_zws_ort width=4}
</div>

{include file='antraege/daten-zeile.tpl' name='mhn_zws_land' label='Land' value=$werte.mhn_zws_land}

<h4>Kontaktdaten</h4>

{include file='antraege/daten-zeile.tpl' name='mhn_telefon' label='Telefonnummer' value=$werte.mhn_telefon type="tel"}
{include file='antraege/daten-zeile.tpl' name='mhn_mobil' label='Mobil' value=$werte.mhn_mobil type="tel"}

<h4>Ausbildung und Beruf</h4>

{include file='antraege/daten-zeile.tpl' name='mhn_studienort' label='Studienort' value=$werte.mhn_studienort}
{include file='antraege/daten-zeile.tpl' name='mhn_studienfach' label='Studienfach' value=$werte.mhn_studienfach}
{include file='antraege/daten-zeile.tpl' name='mhn_unityp' label='Hochschule' value=$werte.mhn_unityp}
{include file='antraege/daten-zeile.tpl' name='mhn_schwerpunkt' label='Schwerpunktfach' value=$werte.mhn_schwerpunkt}
{include file='antraege/daten-zeile.tpl' name='mhn_nebenfach' label='Nebenfach' value=$werte.mhn_nebenfach}
{include file='antraege/daten-zeile.tpl' name='mhn_semester' label='Semester' value=$werte.mhn_semester}
{include file='antraege/daten-zeile.tpl' name='mhn_abschluss' label='Abschluss' value=$werte.mhn_abschluss}
{include file='antraege/daten-zeile.tpl' name='mhn_zweitstudium' label='Zweitstudium' value=$werte.mhn_zweitstudium}
{include file='antraege/daten-zeile.tpl' name='mhn_hochschulaktivitaet' label='Hochschulaktivitäten (Fachschaftsarbeit etc.)' value=$werte.mhn_hochschulaktivitaet}
{include file='antraege/daten-zeile.tpl' name='mhn_stipendien' label='Stipendien' value=$werte.mhn_stipendien}
{include file='antraege/daten-zeile.tpl' name='mhn_ausland' label='Auslandsaufenthalte' value=$werte.mhn_ausland}
{include file='antraege/daten-zeile.tpl' name='mhn_praktika' label='Praktika' value=$werte.mhn_praktika}
{include file='antraege/daten-zeile.tpl' name='mhn_beruf' label='Beruf' value=$werte.mhn_beruf}

<h4>Ich gebe Auskunft über</h4>

<div class="form-group row"'>
    <div class='col-sm-4 col-form-label'>Studiengang</div>
    <div class='col-sm-8'>
        <label><input type="radio" name="mhn_auskunft_studiengang" value="j" {if $werte.mhn_auskunft_studiengang eq 'j'}checked="checked"{/if} /> ja</label>
        <label><input type="radio" name="mhn_auskunft_studiengang" value="n" {if $werte.mhn_auskunft_studiengang eq 'n'}checked="checked"{/if} /> nein</label>
    </div>
</div>

<div class="form-group row"'>
    <div class='col-sm-4 col-form-label'>Stipendien</div>
    <div class='col-sm-8'>
        <label><input type="radio" name="mhn_auskunft_stipendien" value="j" {if $werte.mhn_auskunft_stipendien eq 'j'}checked="checked"{/if}/> ja</label>
        <label><input type="radio" name="mhn_auskunft_stipendien" value="n" {if $werte.mhn_auskunft_stipendien eq 'n'}checked="checked"{/if} /> nein</label>
    </div>
</div>

<div class="form-group row"'>
    <div class='col-sm-4 col-form-label'>Auslandsaufenthalte</div>
    <div class='col-sm-8'>
        <label><input type="radio" name="mhn_auskunft_ausland" value="j" {if $werte.mhn_auskunft_ausland eq 'j'}checked="checked"{/if}/> ja</label>
        <label><input type="radio" name="mhn_auskunft_ausland" value="n" {if $werte.mhn_auskunft_ausland eq 'n'}checked="checked"{/if} /> nein</label>
    </div>
</div>

<div class="form-group row"'>
    <div class='col-sm-4 col-form-label'>Praktika</div>
    <div class='col-sm-8'>
        <label><input type="radio" name="mhn_auskunft_praktika" value="j" {if $werte.mhn_auskunft_praktika eq 'j'}checked="checked"{/if}/> ja</label>
        <label><input type="radio" name="mhn_auskunft_praktika" value="n" {if $werte.mhn_auskunft_praktika eq 'n'}checked="checked"{/if} /> nein</label>
    </div>
</div>

<div class="form-group row"'>
    <div class='col-sm-4 col-form-label'>Beruf</div>
    <div class='col-sm-8'>
        <label><input type="radio" name="mhn_auskunft_beruf" value="j" {if $werte.mhn_auskunft_beruf eq 'j'}checked="checked"{/if}/> ja</label>
        <label><input type="radio" name="mhn_auskunft_beruf" value="n" {if $werte.mhn_auskunft_beruf eq 'n'}checked="checked"{/if} /> nein</label>
    </div>
</div>

<div class="form-group row"'>
    <div class='col-sm-4 col-form-label'>Ich bin prinzipiell bereit zu beruflichem Mentoring</div>
    <div class='col-sm-8'>
        <label><input type="radio" name="mhn_mentoring" value="j" {if $werte.mhn_mentoring eq 'j'}checked="checked"{/if}/> ja</label>
        <label><input type="radio" name="mhn_mentoring" value="n" {if $werte.mhn_mentoring eq 'n'}checked="checked"{/if} /> nein</label>
    </div>
</div>

<h4>Ich könnte bei folgenden Aufgaben helfen</h4>

<div class="checkbox"><label><input type="checkbox" name="mhn_aufgabe_orte" value="j" {if $werte.mhn_aufgabe_orte eq 'j'}checked="checked"{/if}/>
        Mithilfe bei der Suche nach Veranstaltungsorte</label></div>
<div class="checkbox"><label><input type="checkbox" name="mhn_aufgabe_vortrag" value="j" {if $werte.mhn_aufgabe_vortrag eq 'j'}checked="checked"{/if}/>
        einen Vortrag, ein Seminar oder einen Workshop anbieten</label></div>
<div class="checkbox"><label><input type="checkbox" name="mhn_aufgabe_koord" value="j" {if $werte.mhn_aufgabe_koord eq 'j'}checked="checked"{/if}/>
        eine Koordinations-Aufgabe, die man per Mail/Tel. von zu Hause erledigen kann</label></div>
<div class="checkbox"><label><input type="checkbox" name="mhn_aufgabe_computer" value="j" {if $werte.mhn_aufgabe_computer eq 'j'}checked="checked"{/if}/>
        eine Aufgabe, in der ich mein Computer-/IT-Wissen einbringen kann</label></div>
<div class="checkbox"><label><input type="checkbox" name="mhn_aufgabe_texte_schreiben" value="j" {if $werte.mhn_aufgabe_texte_schreiben eq 'j'}checked="checked"{/if}/>
        Texte verfassen (z.B. für die Homepage oder den MHN-Newsletter)</label></div>
<div class="checkbox"><label><input type="checkbox" name="mhn_aufgabe_ansprechpartner" value="j" {if $werte.mhn_aufgabe_ansprechpartner eq 'j'}checked="checked"{/if}/>
        Ansprechpartner vor Ort (lokale Treffen organisieren, Kontakt zu MHNlern in der Region halten)</label></div>
<div class="checkbox"><label><input type="checkbox" name="mhn_aufgabe_hilfe" value="j" {if $werte.mhn_aufgabe_hilfe eq 'j'}checked="checked"{/if}/>
        eine kleine, zeitlich begrenzte Aufgabe, wenn ihr dringend Hilfe braucht</label></div>

<h4>MHN</h4>

{foreach from=$fragen item=i key=k}
    <div class="form-group row"'>
        <label for='input-{$k}' class='col-sm-4 col-form-label'>{$i}</label>
        <div class='col-sm-8'><textarea id='input-{$k}' name='{$k}' class='form-control'>{$fragen_werte[$k]|escape}</textarea></div>
    </div>
{/foreach}

<h4>Sonstige Daten</h4>

{include file='antraege/daten-zeile.tpl' name='mhn_sprachen' label='Sprachen' value=$werte.mhn_sprachen}
{include file='antraege/daten-zeile.tpl' name='mhn_hobbies' label='Hobbys' value=$werte.mhn_hobbies}
{include file='antraege/daten-zeile.tpl' name='mhn_interessen' label='Interessen' value=$werte.mhn_interessen}
{include file='antraege/daten-zeile.tpl' name='mhn_homepage' label='ggf. Homepage' value=$werte.mhn_homepage}
{include file='antraege/daten-zeile.tpl' name='mhn_aufmerksam' label='Wie bist du auf MHN aufmerksam geworden?' value=$werte.mhn_aufmerksam}

<div class="form-group row"'>
    <label for='input-mhn_mensa' class='col-sm-4 col-form-label'>Mensa-Mitglied (wenn ja, bitte Mitglied-Nummer angeben)</label>
    <div class="col-sm-8">
            <label><input type="radio" value="j" name="mhn_mensa" {if $werte.mhn_mensa eq 'j'} checked="checked"{/if}/> ja, Mitgliedsnummer: </label>
            <input type="text" id="input-mhn_mensa_nr" name="mhn_mensa_nr" size="24" maxlength="5" value="{$werte.mhn_mensa_nr|escape}" class="form-control" style="width: 100px; display: inline-block;" />
            <br>
            <label><input type="radio" value="n" name="mhn_mensa" {if $werte.mhn_mensa eq 'n'}checked="checked"{/if}/> nein</label>
    </div>
</div>

<div class="form-group row"'>
    <label for='input-mhn_bemerkungen' class='col-sm-4 col-form-label'>Kommentare, Anregungen</label>
    <div class='col-sm-8'><textarea id='input-mhn_bemerkungen' name='mhn_bemerkungen' class='form-control'>{$werte.mhn_bemerkungen|escape}</textarea></div>
</div>

