CREATE TABLE `antraege` (
  `antrag_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL,
  `ts_antrag` int(11) NOT NULL,
  `ts_nachfrage` int(11) NOT NULL,
  `ts_antwort` int(11) NOT NULL,
  `ts_entscheidung` int(11) NOT NULL,
  `ts_erinnerung` int(11) NOT NULL,
  `ts_statusaenderung` int(11) NOT NULL DEFAULT '0',
  `statusaenderung_username` varchar(255),
  `bemerkung` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kommentare` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fragen_werte` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`antrag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `daten` (
  `antrag_id` int(11) NOT NULL,
  `mhn_vorname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mhn_nachname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_titel` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_geschlecht` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'u',
  `mhn_ws_strasse` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_ws_hausnr` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_ws_zusatz` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_ws_plz` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_ws_ort` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_ws_land` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_zws_strasse` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_zws_hausnr` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_zws_zusatz` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mhn_entrydate` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_zws_plz` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_zws_ort` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_zws_land` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_geburtstag` date DEFAULT NULL,
  `mhn_telefon` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_mobil` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_mensa` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'u',
  `mhn_mensa_nr` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_beschaeftigung` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_studienort` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_studienfach` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_unityp` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_schwerpunkt` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_nebenfach` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_semester` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_abschluss` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_zweitstudium` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_hochschulaktivitaet` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_stipendien` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_ausland` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_praktika` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_beruf` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_auskunft_studiengang` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_auskunft_stipendien` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_auskunft_ausland` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_auskunft_praktika` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_auskunft_beruf` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_mentoring` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_ma` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_orte` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_vortrag` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_koord` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_graphisch` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_computer` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_texte_schreiben` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_texte_lesen` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_vermittlung` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_ansprechpartner` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_hilfe` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_sonstiges` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_aufgabe_sonstiges_besch` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_sprachen` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_hobbies` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_interessen` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_email_newsletter_cb` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `mhn_email_newsletter` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_homepage` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_bemerkungen` varchar(255) COLLATE utf8mb4_unicode_ci,
  `mhn_aufmerksam` varchar(255) COLLATE utf8mb4_unicode_ci,
  kenntnisnahme_datenverarbeitung datetime DEFAULT NULL,
  kenntnisnahme_datenverarbeitung_text text NOT NULL,
  einwilligung_datenverarbeitung datetime DEFAULT NULL,
  einwilligung_datenverarbeitung_text text NOT NULL,
  PRIMARY KEY (`antrag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mails` (
  `antrag_id` int(11) NOT NULL,
  `grund` enum('aufnahme','nachfrage','ablehnung') NOT NULL,
  `username` varchar(255),
  `ts` int(11) NOT NULL,
  `mailsubject` text NOT NULL,
  `mailtext` text NOT NULL,
  KEY `antrad_id` (`antrag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `voten` (
  `antrag_id` int(11) NOT NULL,
  `username` varchar(255),
  `votum` tinyint(4) NOT NULL,
  `ts` int(11) NOT NULL,
  `bemerkung` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nachfrage` text COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `antrag_id` (`antrag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `antraege` SET
    antrag_id = 1358,
    status = 0,
    ts_antrag = UNIX_TIMESTAMP() - 86400 * 2,
    ts_nachfrage = UNIX_TIMESTAMP() - 86400,
    ts_antwort = 0,
    ts_entscheidung = 0,
    ts_erinnerung = 0,
    ts_statusaenderung = UNIX_TIMESTAMP(),
    statusaenderung_username = 'webteam',
    bemerkung = 'A: Ja; B:ok Falls bis zum 10.08 keine Antwort--> ablehnen',
    kommentare = '',
    fragen_werte = 'a:4:{s:13:\"MHN_Beitragen\";s:1:\"-\";s:13:\"MHN_Interesse\";s:13:\"Mensa-Website\";s:10:\"MHN_Kennen\";s:1:\"-\";s:15:\"MHN_Vorstellung\";s:1:\"-\";}'
;

INSERT INTO `daten` VALUES (
    1358,
    'Hilde',
    'Hirsch',
    'hilde.hirsch@mailinator.com',
    '', -- mhn_titel
    'w', -- geschlecht
    'Im Wald',
    '8',
    '',
    '12345',
    'Berlin',
    'Deutschland',
    '',
    '',
    '',
    NULL,
    '',
    '',
    '',
    '1990-01-01',
    '08003301000',
    '',
    'j',
    '999',
    'Hochschulstudent',
    'Berlin',
    'BWL',
    'Universität',
    '',
    '',
    '4',
    '',
    '',
    '',
    '',
    'Holland',
    '',
    '',
    'j',
    'n',
    'j',
    'n',
    'n',
    'n',
    'n',
    '',
    '',
    'j',
    'n',
    'j',
    '',
    'n',
    'n',
    '',
    'j',
    'n',
    NULL,
    'Deutsch, Englisch',
    '-',
    '-',
    'n',
    NULL,
    '',
    '',
    'Mensa-Website',
    NOW(), -- kenntnisnahme_datenverarbeitung
    'Kenntnisnahme',
    NOW(), -- einwilligung_datenverarbeitung
    'Einwilligung'
);

INSERT INTO `mails` SET
    antrag_id = 1358,
    grund = 'nachfrage',
    username = 'webteam',
    ts = UNIX_TIMESTAMP(),
    mailsubject = 'Nachfrage zum MHN-Aufnahmeantrag',
    mailtext = 'Hallo, danke für Dein Interesse und Deine Bewerbung für das MHN!'
;

INSERT INTO `voten` SET
    antrag_id = 1358,
    username = "webteam",
    votum = 2,
    ts = UNIX_TIMESTAMP() - 86400,
    bemerkung = 'Bemerkung',
    nachfrage = 'Nachfrage'
;
