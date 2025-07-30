<?=$this->if($invalidBirthday, '<p class="formmeldung">Fehler: Bitte überprüfe das angegebene Geburtsdatum.</p>')?>

<p>* Pflichtfeld</p>

<h3>Persönliche Daten</h3>

<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_titel',
    'label' => 'Titel',
    'value' => $werte->mhn_titel,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_vorname',
    'label' => 'Vorname*',
    'value' => $werte->mhn_vorname,
    'required' => true,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_nachname',
    'label' => 'Nachname*',
    'value' => $werte->mhn_nachname,
    'required' => true
])?>

<?php if ($werte->mhn_geburtstag): ?>
    <?=$this->render('AntragController/daten-zeile', [
        'name' => 'mhn_geburtstag',
        'label' => 'Geburtstag (TT.MM.JJJJ)*',
        'value' => $werte->mhn_geburtstag->format('Y-m-d'),
        'type' => 'date',
        'required' => true,
    ])?>
<?php else: ?>
    <?=$this->render('AntragController/daten-zeile', [
        'name' => 'mhn_geburtstag',
        'label' => 'Geburtstag (TT.MM.JJJJ)*',
        'value' => '',
        'type' => 'date',
        'required' => true,
    ])?>
<?php endif; ?>

<div class="form-group row">
    <label for='input-mhn_mensa_nr' class='col-sm-4 col-form-label'>ggf. Mitgliedsnummer bei <a href="https://www.mensa.de">Mensa e.V.</a></label>
    <div class="col-sm-8">
        <?=$werte->mhn_mensa_nr->input(maxlength: 5, style: "width: 100px; display: inline-block;")?>
    </div>
</div>

<h3>Kontaktdaten</h3>

<?=$this->render('AntragController/daten-zeile', [
    'name' => 'user_email',
    'label' => 'E-Mail-Adresse*',
    'value' => $werte->user_email,
    'type' => 'email',
    'disabled' => true,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_telefon',
    'label' => 'Telefonnummer',
    'value' => $werte->mhn_telefon,
    'type' => 'tel',
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_ws_strasse',
    'label' => 'Straße, Hausnummer*',
    'value' => $werte->mhn_ws_strasse,
    'required' => true,
])?>

<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_ws_zusatz',
    'label' => 'evtl. Adresszusatz',
    'value' => $werte->mhn_ws_zusatz,
])?>
<div class="form-group row">
    <label for='input-mhn_ws_plz' class='col-sm-4 col-form-label'>PLZ, Ort*</label>
    <div class='col-sm-2'><?=$werte->mhn_ws_plz->input(required: true, placeholder: 'PLZ')?></div>
    <div class='col-sm-6'><?=$werte->mhn_ws_ort->input(required: true, placeholder: 'Ort*')?></div>
</div>

<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_ws_land',
    'label' => 'Land*',
    'value' => $werte->mhn_ws_land,
    'required' => true,
])?>

<details
    <?php if ($werte->mhn_zws_strasse != '' || $werte->mhn_zws_zusatz != '' || $werte->mhn_zws_plz != '' || $werte->mhn_zws_ort != '' || $werte->mhn_zws_land != ''): ?>
        open
    <?php endif; ?>
>
    <summary>Zweitwohnsitz</summary>

    <?=$this->render('AntragController/daten-zeile', [
        'name' => 'mhn_zws_strasse',
        'label' => 'Straße, Hausnummer*',
        'value' => $werte->mhn_zws_strasse,
    ])?>
    <?=$this->render('AntragController/daten-zeile', [
        'name' => 'mhn_zws_zusatz',
        'label' => 'evtl. Adresszusatz',
        'value' => $werte->mhn_zws_zusatz,
    ])?>
    <div class="form-group row">
        <label for='input-mhn_ws_plz' class='col-sm-4 col-form-label'>PLZ, Ort</label>
        <div class='col-sm-2'><?=$werte->mhn_zws_plz->input(placeholder: 'PLZ')?></div>
        <div class='col-sm-6'><?=$werte->mhn_zws_ort->input(placeholder: 'Ort')?></div>
    </div>
    <?=$this->render('AntragController/daten-zeile', [
        'name' => 'mhn_zws_land',
        'label' => 'Land',
        'value' => $werte->mhn_zws_land,
    ])?>
</details>

<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_homepage',
    'label' => 'Homepage',
    'value' => $werte->mhn_homepage,
])?>

<h3>Ausbildung, Beruf und Interessen</h3>

<p>Wir leben von der fachlichen Vielseitigkeit und dem wissenschaftlichen Interesse der Mitglieder. Bitte erzähl uns etwas über deinen Werdegang.</p>

<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_studienfach',
    'label' => 'Studiengang, Ausbildung',
    'value' => $werte->mhn_studienfach,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_beruf',
    'label' => 'Beruf',
    'value' => $werte->mhn_beruf,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_hochschulaktivitaet',
    'label' => 'Ehrenamtliches Engagement',
    'value' => $werte->mhn_hochschulaktivitaet,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_stipendien',
    'label' => 'Stipendien',
    'value' => $werte->mhn_stipendien,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_ausland',
    'label' => 'Auslandsaufenthalte',
    'value' => $werte->mhn_ausland,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_praktika',
    'label' => 'Praktika, Fort- und Weiterbildungen',
    'value' => $werte->mhn_praktika,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_sprachen',
    'label' => 'Sprachen',
    'value' => $werte->mhn_sprachen,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_hobbies',
    'label' => 'Hobbys',
    'value' => $werte->mhn_hobbies,
])?>
<?=$this->render('AntragController/daten-zeile', [
    'name' => 'mhn_interessen',
    'label' => 'Interessen',
    'value' => $werte->mhn_interessen,
])?>

<h3>Kontaktangebote</h3>
<p>Gegenseitiger Austausch und Unterstützung sind integraler Bestandteil des MHN. MHN-Mitglieder dürfen mich kontaktieren zu:</p>
<div class="checkbox"><?=$werte->mhn_auskunft_studiengang->box(label: 'Studiengang, Ausbildung')?></div>
<div class="checkbox"><?=$werte->mhn_auskunft_stipendien->box(label: 'Stipendien')?></div>
<div class="checkbox"><?=$werte->mhn_auskunft_ausland->box(label: 'Auslandsaufenthalte')?></div>
<div class="checkbox"><?=$werte->mhn_auskunft_praktika->box(label: 'Praktika, Fort- und Weiterbildung')?></div>
<div class="checkbox"><?=$werte->mhn_auskunft_beruf->box(label: 'Beruf')?></div>
<div class="checkbox"><?=$werte->mhn_mentoring->box(label: 'Ich bin bereit berufliches Mentoring anzubieten.')?></div>

<h3>In diesen Bereichen möchte ich mich engagieren</h3>

<p>Das MHN lebt vom Engagement seiner Mitglieder, deshalb suchen wir Neumitglieder, die motiviert sind mitzuarbeiten. Dabei ist es auch in Ordnung, wenn Du noch keine Vorerfahrungen hast, voneinander Lernen und gemeinsames Wachstum stehen bei uns im Mittelpunkt!</p>

<div class="checkbox"><?=$werte->mhn_aufgabe_orte->box(label: 'Mithilfe bei der Suche nach Veranstaltungsorte')?></div>
<div class="checkbox"><?=$werte->mhn_aufgabe_vortrag->box(label: 'einen Vortrag, ein Seminar oder einen Workshop anbieten')?></div>
<div class="checkbox"><?=$werte->mhn_aufgabe_koord->box(label: 'eine Koordinations-Aufgabe, die man per Mail/Tel. von zu Hause erledigen kann')?></div>
<div class="checkbox"><?=$werte->mhn_aufgabe_computer->box(label: 'Mitarbeit im IT-Team (IT-Infrastruktur, z.B. Moodle, Mailinglisten, Veranstaltungstool, Mitgliederverwaltung)')?></div>
<div class="checkbox"><?=$werte->mhn_aufgabe_texte_schreiben->box(label: 'Texte verfassen (z.B. für die Homepage oder den MHN-Newsletter)')?></div>
<div class="checkbox"><?=$werte->mhn_aufgabe_ansprechpartner->box(label: 'Ansprechpartner vor Ort (lokale Treffen organisieren, Kontakt zu MHNlern in der Region halten)')?></div>
<div class="checkbox"><?=$werte->mhn_aufgabe_hilfe->box(label: 'eine kleine, zeitlich begrenzte Aufgabe, wenn ihr dringend Hilfe braucht')?></div>

<h3>Du und das MHN</h3>

<div class="form-group row">
    <label for='input-mhn_aufmerksam' class='col-sm-4 col-form-label'>Wie bist du auf das MHN aufmerksam geworden?</label>
    <div class='col-sm-8'><?=$werte->mhn_aufmerksam->textarea()?></div>
</div>

<div class="form-group row">
    <label for='input-mhn_beitragen' class='col-sm-4 col-form-label'>Was möchtest du zu MHN beitragen?</label>
    <div class='col-sm-8'><?=$werte->mhn_beitragen->textarea()?></div>
</div>
<div class="form-group row">
    <label for='input-mhn_interesse' class='col-sm-4 col-form-label'>Was hat Dein Interesse an MHN geweckt?</label>
    <div class='col-sm-8'><?=$werte->mhn_interesse->textarea()?></div>
</div>
<div class="form-group row">
    <label for='input-mhn_vorstellung' class='col-sm-4 col-form-label'>Welche Vorstellung und welche Erwartungen hast Du bislang von MHN?</label>
    <div class='col-sm-8'><?=$werte->mhn_vorstellung->textarea()?></div>
</div>
<div class="form-group row">
    <label for='input-mhn_kennen' class='col-sm-4 col-form-label'>Welche MHN-Mitglieder kennst du persönlich?</label>
    <div class='col-sm-8'><?=$werte->mhn_kennen->textarea()?></div>
</div>
<div class="form-group row">
    <label for='input-mhn_bemerkungen' class='col-sm-4 col-form-label'>Was möchtest du uns sonst noch mitteilen?</label>
    <div class='col-sm-8'><?=$werte->mhn_bemerkungen->textarea()?></div>
</div>
