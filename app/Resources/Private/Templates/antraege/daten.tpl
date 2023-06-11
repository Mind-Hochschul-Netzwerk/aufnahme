{if !empty($invalidBirthday)}
    <p class="formmeldung">Fehler: Bitte überprüfe das angegebene Geburtsdatum.</p>
{/if}

<p>* Pflichtfeld</p>

<h3>Persönliche Daten</h3>

{include file='antraege/daten-zeile.tpl' name='mhn_titel' label='Titel' value=$werte.mhn_titel}
{include file='antraege/daten-zeile.tpl' name='mhn_vorname' label='Vorname*' value=$werte.mhn_vorname required=1}
{include file='antraege/daten-zeile.tpl' name='mhn_nachname' label='Nachname*' value=$werte.mhn_nachname required=1}

{if empty($werte.mhn_geburtstag)}
    {include file='antraege/daten-zeile.tpl' name='mhn_geburtstag' label='Geburtstag (TT.MM.JJJJ)*' value='' type="date" required=1}
{else}
    {include file='antraege/daten-zeile.tpl' name='mhn_geburtstag' label='Geburtstag (TT.MM.JJJJ)*' value=$werte.mhn_geburtstag->format('Y-m-d') type="date" required=1}
{/if}

<div class="form-group row"'>
    <label for='input-mhn_mensa_nr' class='col-sm-4 col-form-label'>ggf. Mitgliedsnummer bei <a href="https://www.mensa.de">Mensa e.V.</a></label>
    <div class="col-sm-8">
            <input type="text" id="input-mhn_mensa_nr" name="mhn_mensa_nr" size="24" maxlength="5" value="{$werte.mhn_mensa_nr|escape}" class="form-control" style="width: 100px; display: inline-block;" />
    </div>
</div>

<h3>Kontaktdaten</h3>

{include file='antraege/daten-zeile.tpl' name='user_email' label='E-Mail-Adresse*' value=$werte.user_email type="email" disabled=1}
{include file='antraege/daten-zeile.tpl' name='mhn_telefon' label='Telefonnummer' value=$werte.mhn_telefon type="tel"}

{include file='antraege/daten-zeile.tpl' name='mhn_ws_strasse' label='Straße, Hausnummer*' value=$werte.mhn_ws_strasse required=1}

{include file='antraege/daten-zeile.tpl' name='mhn_ws_zusatz' label='evtl. Adresszusatz' value=$werte.mhn_ws_zusatz}

<div class="form-group row"'>
    <label for='input-mhn_ws_plz' class='col-sm-4 col-form-label'>PLZ, Ort*</label>
    {include file="antraege/inputElement.tpl" name="mhn_ws_plz" value=$werte.mhn_ws_plz width=4 required=1}
    {include file="antraege/inputElement.tpl" name="mhn_ws_ort" value=$werte.mhn_ws_ort width=4 required=1}
</div>

{include file='antraege/daten-zeile.tpl' name='mhn_ws_land' label='Land*' value=$werte.mhn_ws_land required=1}

<details {if $werte.mhn_zws_strasse || $werte.mhn_zws_zusatz || $werte.mhn_zws_plz || $werte.mhn_zws_ort || $werte.mhn_zws_land}open{/if}>
    <summary>Zweitwohnsitz</summary>

    {include file='antraege/daten-zeile.tpl' name='mhn_zws_strasse' label='Straße, Hausnummer*' value=$werte.mhn_zws_strasse}
    {include file='antraege/daten-zeile.tpl' name='mhn_zws_zusatz' label='evtl. Adresszusatz' value=$werte.mhn_zws_zusatz}

    <div class="form-group row"'>
        <label for='input-mhn_zws_plz' class='col-sm-4 col-form-label'>PLZ, Ort</label>
        {include file="antraege/inputElement.tpl" name="mhn_zws_plz" value=$werte.mhn_zws_plz width=4}
        {include file="antraege/inputElement.tpl" name="mhn_zws_ort" value=$werte.mhn_zws_ort width=4}
    </div>

    {include file='antraege/daten-zeile.tpl' name='mhn_zws_land' label='Land' value=$werte.mhn_zws_land}
</details>

{include file='antraege/daten-zeile.tpl' name='mhn_homepage' label='Homepage' value=$werte.mhn_homepage}

<h3>Ausbildung, Beruf und Interessen</h3>

<p>Wir leben von der fachlichen Vielseitigkeit und dem wissenschaftlichen Interesse der Mitglieder. Bitte erzähl uns etwas über deinen Werdegang.</p>

{include file='antraege/daten-zeile.tpl' name='mhn_studienfach' label='Studiengang, Ausbildung' value=$werte.mhn_studienfach}
{include file='antraege/daten-zeile.tpl' name='mhn_beruf' label='Beruf' value=$werte.mhn_beruf}
{include file='antraege/daten-zeile.tpl' name='mhn_hochschulaktivitaet' label='Ehrenamtliches Engagement' value=$werte.mhn_hochschulaktivitaet}
{include file='antraege/daten-zeile.tpl' name='mhn_stipendien' label='Stipendien' value=$werte.mhn_stipendien}
{include file='antraege/daten-zeile.tpl' name='mhn_ausland' label='Auslandsaufenthalte' value=$werte.mhn_ausland}
{include file='antraege/daten-zeile.tpl' name='mhn_praktika' label='Praktika, Fort- und Weiterbildungen' value=$werte.mhn_praktika}

{include file='antraege/daten-zeile.tpl' name='mhn_sprachen' label='Sprachen' value=$werte.mhn_sprachen}
{include file='antraege/daten-zeile.tpl' name='mhn_hobbies' label='Hobbys' value=$werte.mhn_hobbies}
{include file='antraege/daten-zeile.tpl' name='mhn_interessen' label='Interessen' value=$werte.mhn_interessen}

<h3>Kontaktangebote</h3>

<p>Gegenseitiger Austausch und Unterstützung sind integraler Bestandteil des MHN. MHN-Mitglieder dürfen mich kontaktieren zu:</p>

{include file='antraege/checkbox-zeile.tpl' name='mhn_auskunft_studiengang' label='Studiengang, Ausbildung' value=$werte.mhn_auskunft_studiengang}
{include file='antraege/checkbox-zeile.tpl' name='mhn_auskunft_stipendien' label='Stipendien' value=$werte.mhn_auskunft_stipendien}
{include file='antraege/checkbox-zeile.tpl' name='mhn_auskunft_ausland' label='Auslandsaufenthalte' value=$werte.mhn_auskunft_ausland}
{include file='antraege/checkbox-zeile.tpl' name='mhn_auskunft_praktika' label='Praktika, Fort- und Weiterbildung' value=$werte.mhn_auskunft_praktika}
{include file='antraege/checkbox-zeile.tpl' name='mhn_auskunft_beruf' label='Beruf' value=$werte.mhn_auskunft_beruf}
{include file='antraege/checkbox-zeile.tpl' name='mhn_mentoring' label='Ich bin bereit berufliches Mentoring anzubieten.' value=$werte.mhn_mentoring}

<h3>In diesen Bereichen möchte ich mich engagieren</h3>

<p>Das MHN lebt vom Engagement seiner Mitglieder, deshalb suchen wir Neumitglieder, die motiviert sind mitzuarbeiten. Dabei ist es auch in Ordnung, wenn Du noch keine Vorerfahrungen hast, voneinander Lernen und gemeinsames Wachstum stehen bei uns im Mittelpunkt!</p>

{include file='antraege/checkbox-zeile.tpl' name='mhn_aufgabe_orte' label='Mithilfe bei der Suche nach Veranstaltungsorte' value=$werte.mhn_aufgabe_orte}
{include file='antraege/checkbox-zeile.tpl' name='mhn_aufgabe_vortrag' label='einen Vortrag, ein Seminar oder einen Workshop anbieten' value=$werte.mhn_aufgabe_vortrag}
{include file='antraege/checkbox-zeile.tpl' name='mhn_aufgabe_koord' label='eine Koordinations-Aufgabe, die man per Mail/Tel. von zu Hause erledigen kann' value=$werte.mhn_aufgabe_koord}
{include file='antraege/checkbox-zeile.tpl' name='mhn_aufgabe_computer' label='Mitarbeit im IT-Team (IT-Infrastruktur, z.B. Moodle, Mailinglisten, Veranstaltungstool, Mitgliederverwaltung)' value=$werte.mhn_aufgabe_computer}
{include file='antraege/checkbox-zeile.tpl' name='mhn_aufgabe_texte_schreiben' label='Texte verfassen (z.B. für die Homepage oder den MHN-Newsletter)' value=$werte.mhn_aufgabe_texte_schreiben}
{include file='antraege/checkbox-zeile.tpl' name='mhn_aufgabe_ansprechpartner' label='Ansprechpartner vor Ort (lokale Treffen organisieren, Kontakt zu MHNlern in der Region halten)' value=$werte.mhn_aufgabe_ansprechpartner}
{include file='antraege/checkbox-zeile.tpl' name='mhn_aufgabe_hilfe' label='eine kleine, zeitlich begrenzte Aufgabe, wenn ihr dringend Hilfe braucht' value=$werte.mhn_aufgabe_hilfe}

<h3>Du und das MHN</h3>

<div class="form-group row"'>
    <label for='input-mhn_aufmerksam' class='col-sm-4 col-form-label'>Wie bist du auf das MHN aufmerksam geworden?</label>
    <div class='col-sm-8'><textarea id='input-mhn_aufmerksam' name='mhn_aufmerksam' class='form-control'>{$werte.mhn_aufmerksam|escape}</textarea></div>
</div>

<div class="form-group row"'>
    <label for='input-mhn_beitragen' class='col-sm-4 col-form-label'>Was möchtest du zu MHN beitragen?</label>
    <div class='col-sm-8'><textarea id='input-mhn_beitragen' name='mhn_beitragen' class='form-control'>{$werte.mhn_beitragen|escape}</textarea></div>
</div>
<div class="form-group row"'>
    <label for='input-mhn_interesse' class='col-sm-4 col-form-label'>Was hat Dein Interesse an MHN geweckt?</label>
    <div class='col-sm-8'><textarea id='input-mhn_interesse' name='mhn_interesse' class='form-control'>{$werte.mhn_interesse|escape}</textarea></div>
</div>
<div class="form-group row"'>
    <label for='input-mhn_vorstellung' class='col-sm-4 col-form-label'>Welche Vorstellung und welche Erwartungen hast Du bislang von MHN?</label>
    <div class='col-sm-8'><textarea id='input-mhn_vorstellung' name='mhn_vorstellung' class='form-control'>{$werte.mhn_vorstellung|escape}</textarea></div>
</div>
<div class="form-group row"'>
    <label for='input-mhn_kennen' class='col-sm-4 col-form-label'>Welche MHN-Mitglieder kennst du persönlich?</label>
    <div class='col-sm-8'><textarea id='input-mhn_kennen' name='mhn_kennen' class='form-control'>{$werte.mhn_kennen|escape}</textarea></div>
</div>

<div class="form-group row"'>
    <label for='input-mhn_bemerkungen' class='col-sm-4 col-form-label'>Was möchtest du uns sonst noch mitteilen?</label>
    <div class='col-sm-8'><textarea id='input-mhn_bemerkungen' name='mhn_bemerkungen' class='form-control'>{$werte.mhn_bemerkungen|escape}</textarea></div>
</div>
