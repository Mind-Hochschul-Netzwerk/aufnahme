<h1 id="formular">Formular</h1>

<form action="{$self}" method="post">

<table border="0" cellpadding="3" cellspacing="0" class="aufnahme">
    <tr><td colspan="2"><h2>Basisdaten</h2></td></tr>

    <tr><td>Name</td><td><input type="text" name="mhn_nachname" size="30" value="{$werte.mhn_nachname|escape}" /></td></tr>
    <tr><td>Vorname</td><td><input type="text" name="mhn_vorname" size="30" value="{$werte.mhn_vorname|escape}" /></td></tr>
    <tr><td>E-Mail-Adresse</td><td><input type="text" name="user_email" size="30" value="{$werte.user_email|escape}"/></td></tr>
    <tr><td>Geburtsdatum (TT.MM.JJJJ)</td><td><input type="text" name="mhn_geburtstag" size="20" value="{$werte.mhn_geburtstag|escape}"/></td></tr>

    <tr><td>Straße/Hausnummer</td><td>
        <input type="text" name="mhn_ws_strasse" size="30" value="{$werte.mhn_ws_strasse|escape}"/>
        <input type="text" name="mhn_ws_hausnr" size="6" value="{$werte.mhn_ws_hausnr|escape}"/>
    </td></tr>
    <tr><td>evtl. Adresszusatz</td><td><input type="text" size="30" name="mhn_ws_zusatz" value="{$werte.mhn_ws_zusatz|escape}"/></td></tr>
    <tr><td>PLZ/Ort</td><td><input type="text" size="5" name="mhn_ws_plz" value="{$werte.mhn_ws_plz|escape}"/>
        <input type="text" size="30" name="mhn_ws_ort" value="{$werte.mhn_ws_ort|escape}"/>
    </td></tr>
    <tr><td>Land</td><td><input type="text" name="mhn_ws_land" size="30" value="{$werte.mhn_ws_land|escape}"/></td></tr>

    <tr><td colspan="2"><h2>Zusatzdaten</h2></td></tr>
    <tr><td colspan="2"><h2>1. Allgemeine Daten</h2></td></tr>

    <tr><td>Geschlecht</td><td>
        <select name="mhn_geschlecht">
            <option value="" {if $werte.mhn_geschlecht eq ''}selected{/if}></option>
            <option value="w" {if $werte.mhn_geschlecht eq 'w'}selected{/if}/>weiblich</option>
            <option value="m" {if $werte.mhn_geschlecht eq 'm'}selected{/if}/>männlich</option>
            <option value="d" {if $werte.mhn_geschlecht eq 'd'}selected{/if}/>divers</option>
    </td></tr>
    <tr><td>akad. Titel, falls vorhanden</td><td><input type="text" name="mhn_titel" size="20" value="{$werte.mhn_titel|escape}" /></td></tr>
    <tr><td>derzeitige Beschäftigung</td><td>
        <select name="mhn_beschaeftigung" size="1">
            <option value="Hochschulstudent" selected="selected">Hochschulstudent</option>
            <option value="Schueler" {if $werte.mhn_beschaeftigung eq "Schueler"}selected="selected"{/if}>Schüler</option>
            <option value="Doktorand" {if $werte.mhn_beschaeftigung eq "Doktorand"}selected="selected"{/if}>Doktorand</option>
            <option value="Berufstaetig" {if $werte.mhn_beschaeftigung eq "Berufstaetig"}selected="selected"{/if}>Berufstätig</option>
            <option value="Sonstiges" {if $werte.mhn_beschaeftigung eq "Sonstiges"}selected="selected"{/if}>Sonstiges</option>
        </select>
    </td></tr>

    <tr><td colspan="2"><h2>2. Zweitwohnsitz (z.B. Adresse der Eltern)</h2></td></tr>
    <tr><td>Straße/Hausnummer</td><td><input type="text" name="mhn_zws_strasse" size="30" value="{$werte.mhn_zws_strasse|escape}" />
        <input type="text" name="mhn_zws_hausnr" size="6" value="{$werte.mhn_zws_hausnr|escape}" />
    </td></tr>
    <tr><td>evtl. Adresszusatz</td><td><input type="text" size="30" name="mhn_zws_zusatz" value="{$werte.mhn_zws_zusatz|escape}"/></td></tr>
    <tr><td>PLZ/Ort</td><td><input type="text" size="5" name="mhn_zws_plz" value="{$werte.mhn_zws_plz|escape}"/>
        <input type="text" size="30" name="mhn_zws_ort" value="{$werte.mhn_zws_ort|escape}"/>
    </td></tr>
    <tr><td>Land</td><td><input type="text" name="mhn_zws_land" size="30" value="{$werte.mhn_zws_land|escape}"/></td></tr>

    <tr><td colspan="2"><h2>3. Kontaktdaten</h2></td></tr>
    <tr><td>Telefonnummer</td><td><input type="text" name="mhn_telefon" size="30" value="{$werte.mhn_telefon|escape}" /></td></tr>
    <tr><td>Mobil</td><td><input type="text" name="mhn_mobil" size="30" value="{$werte.mhn_mobil|escape}"/></td></tr>

    <tr><td colspan="2"><h2>4. Ausbildung und Beruf</h2></td></tr>
    <tr><td>Studienort</td><td><input type="text" size="50" name="mhn_studienort" value="{$werte.mhn_studienort|escape}"/></td></tr>
    <tr><td>Studienfach</td><td><input type="text" size="50" name="mhn_studienfach" value="{$werte.mhn_studienfach|escape}"/></td></tr>
    <tr><td>Hochschule</td><td><input type="text" size="50" name="mhn_unityp" value="{$werte.mhn_unityp|escape}"/></td></tr>
    <tr><td>Schwerpunktfach</td><td><input type="text" size="50" name="mhn_schwerpunkt" value="{$werte.mhn_schwerpunkt|escape}"/></td></tr>
    <tr><td>Nebenfach</td><td><input type="text" size="50" name="mhn_nebenfach" value="{$werte.mhn_nebenfach|escape}"/></td></tr>
    <tr><td>Semester</td><td><input type="text" size="5" name="mhn_semester" value="{$werte.mhn_semester|escape}"/></td></tr>
    <tr><td>Abschluss</td><td><input type="text" size="50" name="mhn_abschluss" value="{$werte.mhn_abschluss|escape}"/></td></tr>
    <tr><td>Zweitstudium</td><td><input type="text" size="50" name="mhn_zweitstudium" value="{$werte.mhn_zweitstudium|escape}"/></td></tr>
    <tr><td>Hochschulaktivitäten (Fachschaftsarbeit etc.)</td><td><input type="text" size="50" name="mhn_hochschulaktivitaet" value="{$werte.mhn_hochschulaktivitaet|escape}"/></td></tr>
    <tr><td>Stipendien</td><td><input type="text" size="50" name="mhn_stipendien" value="{$werte.mhn_stipendien|escape}"/></td></tr>
    <tr><td>Auslandsaufenthalte</td><td><input type="text" size="50" name="mhn_ausland" value="{$werte.mhn_ausland|escape}"/></td></tr>
    <tr><td>Praktika</td><td><input type="text" size="50" name="mhn_praktika" value="{$werte.mhn_praktika|escape}"/></td></tr>
    <tr><td>Beruf</td><td><input type="text" size="50" name="mhn_beruf" value="{$werte.mhn_beruf|escape}"/></td></tr>

    <tr><td colspan="2"><h2>5. Ich gebe Auskunft über</h2></td></tr>
    <tr><td>Studiengang</td><td>
        <input type="radio" name="mhn_auskunft_studiengang" value="j" {if $werte.mhn_auskunft_studiengang eq 'j'}checked="checked"{/if}/>ja
        <input type="radio" name="mhn_auskunft_studiengang" value="n" {if $werte.mhn_auskunft_studiengang eq 'n'}checked="checked"{/if} />nein
    </td></tr>
    <tr><td>Stipendien</td><td>
        <input type="radio" name="mhn_auskunft_stipendien" value="j" {if $werte.mhn_auskunft_stipendien eq 'j'}checked="checked"{/if}/>ja
        <input type="radio" name="mhn_auskunft_stipendien" value="n" {if $werte.mhn_auskunft_stipendien eq 'n'}checked="checked"{/if} />nein
    </td></tr>
    <tr><td>Auslandsaufenthalte</td><td>
        <input type="radio" name="mhn_auskunft_ausland" value="j" {if $werte.mhn_auskunft_ausland eq 'j'}checked="checked"{/if}/>ja
        <input type="radio" name="mhn_auskunft_ausland" value="n" {if $werte.mhn_auskunft_ausland eq 'n'}checked="checked"{/if} />nein
    </td></tr>
    <tr><td>Praktika</td><td>
        <input type="radio" name="mhn_auskunft_praktika" value="j" {if $werte.mhn_auskunft_praktika eq 'j'}checked="checked"{/if}/>ja
        <input type="radio" name="mhn_auskunft_praktika" value="n" {if $werte.mhn_auskunft_praktika eq 'n'}checked="checked"{/if} />nein
    </td></tr>
    <tr><td>Beruf</td><td>
        <input type="radio" name="mhn_auskunft_beruf" value="j" {if $werte.mhn_auskunft_beruf eq 'j'}checked="checked"{/if}/>ja
        <input type="radio" name="mhn_auskunft_beruf" value="n" {if $werte.mhn_auskunft_beruf eq 'n'}checked="checked"{/if} />nein
    </td></tr>
    <tr><td>Ich bin prinzipiell bereit zu beruflichem Mentoring</td><td>
        <input type="radio" name="mhn_mentoring" value="j" {if $werte.mhn_mentoring eq 'j'}checked="checked"{/if}/>ja
        <input type="radio" name="mhn_mentoring" value="n" {if $werte.mhn_mentoring eq 'n'}checked="checked"{/if} />nein
    </td></tr>

    <tr><td colspan="2"><h2>7. Ich könnte bei folgenden Aufgaben helfen</h2></td></tr>

    <tr><td colspan="2">
        <input type="checkbox" name="mhn_aufgabe_orte" value="j" {if $werte.mhn_aufgabe_orte eq 'j'}checked="checked"{/if}/>
        Mithilfe bei der Suche nach Veranstaltungsorte
    </td></tr>
    <tr><td colspan="2">
        <input type="checkbox" name="mhn_aufgabe_vortrag" value="j" {if $werte.mhn_aufgabe_vortrag eq 'j'}checked="checked"{/if}/>
        einen Vortrag, ein Seminar oder einen Workshop anbieten
    </td></tr>
    <tr><td colspan="2">
        <input type="checkbox" name="mhn_aufgabe_koord" value="j" {if $werte.mhn_aufgabe_koord eq 'j'}checked="checked"{/if}/>
        eine Koordinations-Aufgabe, die man per Mail/Tel. von zu Hause erledigen kann
    </td></tr>
    <tr><td colspan="2">
        <input type="checkbox" name="mhn_aufgabe_computer" value="j" {if $werte.mhn_aufgabe_computer eq 'j'}checked="checked"{/if}/>
        eine Aufgabe, in der ich mein Computer-/IT-Wissen einbringen kann
    </td></tr>
    <tr><td colspan="2">
        <input type="checkbox" name="mhn_aufgabe_texte_schreiben" value="j" {if $werte.mhn_aufgabe_texte_schreiben eq 'j'}checked="checked"{/if}/>
        Texte verfassen (z.B. für die Homepage oder den MHN-Newsletter)
    </td></tr>
    <tr><td colspan="2">
        <input type="checkbox" name="mhn_aufgabe_ansprechpartner" value="j" {if $werte.mhn_aufgabe_ansprechpartner eq 'j'}checked="checked"{/if}/>
        Ansprechpartner vor Ort (lokale Treffen organisieren, Kontakt zu MHNlern in der Region halten)
    </td></tr>
    <tr><td colspan="2">
        <input type="checkbox" name="mhn_aufgabe_hilfe" value="j" {if $werte.mhn_aufgabe_hilfe eq 'j'}checked="checked"{/if}/>
        eine kleine, zeitlich begrenzte Aufgabe, wenn ihr dringend Hilfe braucht
    </td></tr>

    <tr><td colspan="2"><h2>8. MHN</h2></td></tr>

    {foreach from=$fragen item=i key=k}
        <tr><td>{$i}</td><td><textarea rows="10" cols="40" name="{$k}">{$fragen_werte[$k]|escape}</textarea></td></tr>
    {/foreach}


    <tr><td colspan="2"><h2>9. Sonstige Daten</h2></td></tr>
    <tr><td>Sprachen</td><td><input type="text" size="80" name="mhn_sprachen" value="{$werte.mhn_sprachen|escape}"/></td></tr>
    <tr><td>Hobbys</td><td><input type="text" size="80" name="mhn_hobbies" value="{$werte.mhn_hobbies|escape}"/></td></tr>
    <tr><td>Interessen</td><td><input type="text" size="80" name="mhn_interessen" value="{$werte.mhn_interessen|escape}"/></td></tr>
    <tr><td>ggf. Homepage</td><td><input type="text" size="40" name="mhn_homepage" value="{$werte.mhn_homepage|escape}"/></td></tr>
    <tr><td>Wie bist du auf MHN aufmerksam geworden?</td>
    <td><input type="text" size="80" name="mhn_aufmerksam" value="{$werte.mhn_aufmerksam|escape}"/></td></tr>

    <tr><td>Mensa Mitglied (wenn ja, bitte Mitglied-Nummer angeben)</td>
        <td>
            <input type="radio" value="j" name="mhn_mensa" {if $werte.mhn_mensa eq 'j'} checked="checked"{/if}/>ja
            <input type="text" name="mhn_mensa_nr" size="24" maxlength="5" value="{$werte.mhn_mensa_nr|escape}"/><br />
            <input type="radio" value="n" name="mhn_mensa" {if $werte.mhn_mensa eq 'n'}checked="checked"{/if}/>nein
        </td>
    </tr>

    <tr><td>Kommentare, Anregungen</td><td><textarea rows="5" cols="30" name="mhn_bemerkungen">{$werte.mhn_bemerkungen|escape}</textarea></td></tr>

    <tr><td colspan="2"><h2>10. Datenschutz</h2></td></tr>

    <tr><td>kenntnisnahme_datenverarbeitung</td><td>{$werte.kenntnisnahme_datenverarbeitung|escape}</td></tr>
    <tr><td>kenntnisnahme_datenverarbeitung_text</td><td>{$werte.kenntnisnahme_datenverarbeitung_text|escape}</td></tr>
    <tr><td>einwilligung_datenverarbeitung</td><td>{$werte.einwilligung_datenverarbeitung|escape}</td></tr>
    <tr><td>einwilligung_datenverarbeitung_text</td><td>{$werte.einwilligung_datenverarbeitung_text|escape}</td></tr>

</table>

<input type="hidden" name="formular" value="speichern_antrag_daten" />
<input type="submit" value="Daten speichern" />

</form>
