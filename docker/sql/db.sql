DROP TABLE IF EXISTS `antraege`;
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

--
-- Dumping data for table `antraege`
--

LOCK TABLES `antraege` WRITE;
INSERT INTO `antraege` VALUES (1358,0,1464123424,1469491200,0,0,0,1471707341,'webteam','A: Ja; B:ok Falls bis zum 10.08 keine Antwort--> ablehnen','','a:4:{s:13:\"MHN_Beitragen\";s:1:\"-\";s:13:\"MHN_Interesse\";s:13:\"Mensa-Website\";s:10:\"MHN_Kennen\";s:1:\"-\";s:15:\"MHN_Vorstellung\";s:1:\"-\";}');
UNLOCK TABLES;

--
-- Table structure for table `daten`
--

DROP TABLE IF EXISTS `daten`;
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

--
-- Dumping data for table `daten`
--

LOCK TABLES `daten` WRITE;
INSERT INTO `daten` VALUES (1358,'Hilde','Hirsch','hilde.hirsch@mailinator.com','','m','Im Wald','8','','12345','Berlin','Deutschland','','','',NULL,'','','','1990-01-01','08003301000','','j','999','Hochschulstudent','Berlin','BWL','Universität','','','4','','','','','Holland','','','j','n','j','n','n','n','n','','','j','n','j','','n','n','','j','n',NULL,'Deutsch, Englisch','-','-','n',NULL,'','','Mensa-Website',NOW(),'Kenntnisnahme',NOW(),'Einwilligung');
UNLOCK TABLES;

DROP TABLE IF EXISTS `mails`;
CREATE TABLE `mails` (
  `antrag_id` int(11) NOT NULL,
  `grund` enum('aufnahme','nachfrage','ablehnung') NOT NULL,
  `username` varchar(255),
  `ts` int(11) NOT NULL,
  `mailsubject` text NOT NULL,
  `mailtext` text NOT NULL,
  KEY `antrad_id` (`antrag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `mails` WRITE;
INSERT INTO `mails` VALUES (1358,'nachfrage','webteam',1467629323,'Nachfrage zum MHN-Aufnahmeantrag','Hallo Tobias,\r\n\r\nzunächst einmal Danke für Dein Interesse und Deine Bewerbung für das\r\nMHN! Es tut uns leid, dass diese Antwort etwas gedauert hat. Wir von der\r\nAufnahmekommission bemühen uns, so gut wie möglich einzuschätzen, ob\r\njemand zum MHN passt, und das dauert manchmal ein bisschen länger.\r\n\r\nBevor wir Deinen Antrag bearbeiten, möchten wir Dich\r\nbitten, die noch fehlenden Informationen zu den im Antrag nicht\r\nausgefüllten Feldern zu ergänzen.\r\n\r\n\r\nWelche Vorstellungen und welche Erwartungen hast Du bislang vom MHN?\r\n--> Was hat Dich angesprochen, so dass Du den Entschluss gefasst\r\nhast, Dich bei uns zu bewerben? Welche Angebote des MHN möchtest Du nutzen?\r\n\r\n\r\nWas möchtest du zu MHN beitragen?\r\n--> Wenn Du an Veranstaltungen teilnehmen möchtest, könntest Du Dir\r\nvorstellen, bei der Organisation vor Ort (Auf- und Abbau) zu helfen,\r\noder im Vorfeld etwas zur Logistik beizutragen? Du hast\r\ngeschrieben, dass du bereit wärst Koordinationsaufgaben zu übernehmen,\r\ndie du von zu Hause per Mail erledigen kannst. Wärst du beispielsweise\r\nbereit die Koordination eines Teams (lokale Ansprechpartner o.ä.)\r\nzu übernehmen?\r\n\r\n\r\nWelche Interessen und Hobbies hast Du?\r\n--> Erzähl uns bitte ein bisschen was über Dich.\r\nWomit verbringst Du Deine Freizeit? Was interessiert Dich? Über was\r\nwürdest Du Dich mit anderen MHNlern unterhalten wollen?\r\n\r\n\r\nWir würden uns freuen, von Dir zu hören.\r\n\r\nViele Grüße,\r\nJana\r\nfür die MHN-Aufnahmekommission\r\n\r\n\r\n'),(1358,'nachfrage',32,1469600245,'Nachfrage zum MHN-Aufnahmeantrag','Hallo Tobias,\r\n\r\n\r\nwir haben die am 04.07.2016 eine Mail geschrieben, da wir noch Fragen zu deinem Aufnahmeantrag beim MHN haben. Wir haben bisher noch nichts von dir gehört. Für den Fall, dass die Mail irgendwie untergegangen ist, ist sie unten nochmal angefügt.\r\n\r\nBitte antworte uns bis zum 10.08. Anderfalls müssen wir davon ausgehen, dass dein Interesse am MHN vorübergehend erloschen ist und werden den aktuellen Antrag ablehnen.\r\n\r\n\r\nWir würden uns freuen, von Dir zu hören.\r\n\r\nViele Grüße,\r\nJana\r\nfür die MHN-Aufnahmekommission\r\n\r\n\r\n\r\n\r\n----------------\r\nMailtext vom 04.07.:\r\n\r\n\r\nHallo Tobias,\r\n\r\nzunächst einmal Danke für Dein Interesse und Deine Bewerbung für das\r\nMHN! Es tut uns leid, dass diese Antwort etwas gedauert hat. Wir von der\r\nAufnahmekommission bemühen uns, so gut wie möglich einzuschätzen, ob\r\njemand zum MHN passt, und das dauert manchmal ein bisschen länger.\r\n\r\nBevor wir Deinen Antrag bearbeiten, möchten wir Dich\r\nbitten, die noch fehlenden Informationen zu den im Antrag nicht\r\nausgefüllten Feldern zu ergänzen.\r\n\r\n\r\nWelche Vorstellungen und welche Erwartungen hast Du bislang vom MHN?\r\n--> Was hat Dich angesprochen, so dass Du den Entschluss gefasst\r\nhast, Dich bei uns zu bewerben? Welche Angebote des MHN möchtest Du nutzen?\r\n\r\n\r\nWas möchtest du zu MHN beitragen?\r\n--> Wenn Du an Veranstaltungen teilnehmen möchtest, könntest Du Dir\r\nvorstellen, bei der Organisation vor Ort (Auf- und Abbau) zu helfen,\r\noder im Vorfeld etwas zur Logistik beizutragen? Du hast\r\ngeschrieben, dass du bereit wärst Koordinationsaufgaben zu übernehmen,\r\ndie du von zu Hause per Mail erledigen kannst. Wärst du beispielsweise\r\nbereit die Koordination eines Teams (lokale Ansprechpartner o.ä.)\r\nzu übernehmen?\r\n\r\n\r\nWelche Interessen und Hobbies hast Du?\r\n--> Erzähl uns bitte ein bisschen was über Dich.\r\nWomit verbringst Du Deine Freizeit? Was interessiert Dich? Über was\r\nwürdest Du Dich mit anderen MHNlern unterhalten wollen?\r\n\r\n\r\nWir würden uns freuen, von Dir zu hören.\r\n\r\nViele Grüße,\r\nJana\r\nfür die MHN-Aufnahmekommission\r\n\r\n\r\n');
UNLOCK TABLES;

--
-- Table structure for table `voten`
--

DROP TABLE IF EXISTS `voten`;
CREATE TABLE `voten` (
  `antrag_id` int(11) NOT NULL,
  `username` varchar(255),
  `votum` tinyint(4) NOT NULL,
  `ts` int(11) NOT NULL,
  `bemerkung` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nachfrage` text COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `antrag_id` (`antrag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `voten` WRITE;
INSERT INTO `voten` VALUES (1358,"webteam",2,1464159731,'An sich ein klassischer \"Spatz\", aber die geringe Mühe beim Ausfüllen drückt eine geringe Wertschätzung aus. Daher: Bitte um ausfüllen der freigelassenen Felder. ','Bitte um ausfüllen der freigelassenen Felder. '),(1358,"webteam",2,1464209068,'Eigentlich ja, aber aus prinzip nochmal nachfragen','Engagement, Vorstellung, Erwartungen, Hobbies und Interessen'),(1358,"webteam",2,1464264375,'dito','dito'),(1358,"webteam",1,1464591976,'2 von 3 Kriterien erfüllt. Die fehlenden Felder kann man auch als \'kein geblubber sondern ein ehrliches weiß ich noch nicht\' sehen. Daher von mir ein knappes ja, da ankreuzbare Aufgabe auch OK sind für mich.',''),(1358,"webteam",2,1465300588,'bisschen lieblos finde ich...','die freien Felder noch ergänzen'),(1358,"webteam",1,1467053858,'schwaches Ja. M, Student und wenigstens grundlegende Mithilfebereitschaft.',''),(1358,"webteam",3,1467101223,'',''),(1358,"webteam",3,1471707080,'Hmm...','');
UNLOCK TABLES;
