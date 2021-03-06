<?php
namespace MHN\Aufnahme;

//die eigentlichen Daten des Aufnahmeantrags. Ausgelagert, damit
// bei einer Änderung des Formular leichter anpassbar.
class Daten
{
    /** @var string */
    const TABLE_NAME = 'daten';

    //die Schlüssel derjenigen Datenfelder, die in der Wiki-Datenbank stehen und bei der Aufnahme abgefragt werden:
    // werden auch bei Daten-Formularen genauso eingesetzt(!)
    const KEYS = [
        'mhn_vorname',
        'mhn_nachname',
        'user_email',
        'mhn_titel',
        'mhn_geschlecht',
        'mhn_ws_strasse',
        'mhn_ws_hausnr',
        'mhn_ws_zusatz',
        'mhn_ws_plz',
        'mhn_ws_ort',
        'mhn_ws_land',
        'mhn_zws_strasse',
        'mhn_zws_hausnr',
        'mhn_zws_zusatz',
        'mhn_zws_plz',
        'mhn_zws_ort',
        'mhn_zws_land',
        'mhn_geburtstag',
        'mhn_telefon',
        'mhn_mobil',
        'mhn_mensa',
        'mhn_mensa_nr',
        'mhn_beschaeftigung',
        'mhn_studienort',
        'mhn_studienfach',
        'mhn_unityp',
        'mhn_schwerpunkt',
        'mhn_nebenfach',
        'mhn_semester',
        'mhn_abschluss',
        'mhn_zweitstudium',
        'mhn_hochschulaktivitaet',
        'mhn_stipendien',
        'mhn_ausland',
        'mhn_praktika',
        'mhn_beruf',
        'mhn_auskunft_studiengang',
        'mhn_auskunft_stipendien',
        'mhn_auskunft_ausland',
        'mhn_auskunft_praktika',
        'mhn_auskunft_beruf',
        'mhn_mentoring',
        'mhn_aufgabe_orte',
        'mhn_aufgabe_vortrag',
        'mhn_aufgabe_koord',
        'mhn_aufgabe_computer',
        'mhn_aufgabe_texte_schreiben',
        'mhn_aufgabe_ansprechpartner',
        'mhn_aufgabe_hilfe',
        'mhn_sprachen',
        'mhn_hobbies',
        'mhn_interessen',
        'mhn_homepage',
        'mhn_bemerkungen',
        'mhn_aufmerksam',
        'kenntnisnahme_datenverarbeitung',
        'kenntnisnahme_datenverarbeitung_text',
        'einwilligung_datenverarbeitung',
        'einwilligung_datenverarbeitung_text',
    ];

    // Leider kann man offenbar den Fall "checkbox vorhanden, aber nicht gecheckt"
    // und "checkbox nicht vorhanden" nicht unterscheiden.
    // Um interne Konsistenzpruefungen zu erlauben, daher hier die Checkboxes ... alles richtig dreckig ...
    const KEYS_CHECKBOX = [
        'mhn_aufgabe_orte',
        'mhn_aufgabe_vortrag',
        'mhn_aufgabe_koord',
        'mhn_aufgabe_computer',
        'mhn_aufgabe_texte_schreiben',
        'mhn_aufgabe_ansprechpartner',
        'mhn_aufgabe_hilfe',
    ];

    /** @var Sql */
    private $sql = null;

    public $antrag_id;

    //alle relevanten (=bei der Aufnahme abgefragten) Datenbank-Felder der Wiki-Datenbank
    public $mhn_vorname = '';
    public $mhn_nachname = '';
    public $user_email = '';
    public $mhn_titel = '';
    public $mhn_geschlecht = '';
    public $mhn_ws_strasse = '';
    public $mhn_ws_hausnr = '';
    public $mhn_ws_zusatz = '';
    public $mhn_ws_plz = '';
    public $mhn_ws_ort = '';
    public $mhn_ws_land = '';
    public $mhn_zws_strasse = '';
    public $mhn_zws_hausnr = '';
    public $mhn_zws_zusatz = '';
    public $mhn_zws_plz = '';
    public $mhn_zws_ort = '';
    public $mhn_zws_land = '';
    public $mhn_geburtstag = '';
    public $mhn_telefon = '';
    public $mhn_mobil = ''; 
    public $mhn_mensa = '';
    public $mhn_mensa_nr = '';
    public $mhn_beschaeftigung = '';
    public $mhn_studienort = '';
    public $mhn_studienfach = '';
    public $mhn_unityp = '';
    public $mhn_schwerpunkt = '';
    public $mhn_nebenfach = '';
    public $mhn_semester = '';
    public $mhn_abschluss = '';
    public $mhn_zweitstudium = '';
    public $mhn_hochschulaktivitaet = '';
    public $mhn_stipendien = '';
    public $mhn_ausland = '';
    public $mhn_praktika = '';
    public $mhn_beruf = '';
    public $mhn_auskunft_studiengang = '';
    public $mhn_auskunft_stipendien = '';
    public $mhn_auskunft_ausland = '';
    public $mhn_auskunft_praktika = '';
    public $mhn_auskunft_beruf = '';
    public $mhn_mentoring = '';
    public $mhn_aufgabe_orte = 'n';
    public $mhn_aufgabe_vortrag = 'n';
    public $mhn_aufgabe_koord = 'n';
    public $mhn_aufgabe_graphisch = 'n';
    public $mhn_aufgabe_computer = 'n';
    public $mhn_aufgabe_texte_schreiben = 'n';
    public $mhn_aufgabe_texte_lesen = 'n';
    public $mhn_aufgabe_vermittlung = 'n';
    public $mhn_aufgabe_ansprechpartner = 'n';
    public $mhn_aufgabe_hilfe = 'n';
    public $mhn_aufgabe_sonstiges = 'n';
    public $mhn_aufgabe_sonstiges_besch = 'n';
    public $mhn_sprachen = '';
    public $mhn_hobbies = '';
    public $mhn_interessen = '';
    public $mhn_homepage = '';
    public $mhn_bemerkungen = '';
    public $mhn_aufmerksam = '';

    public $kenntnisnahme_datenverarbeitung = 0;
    public $kenntnisnahme_datenverarbeitung_text = '';
    public $einwilligung_datenverarbeitung = 0;
    public $einwilligung_datenverarbeitung_text = '';

    /**
     * Instanziiert das Objekt
     */
    public function __construct()
    {
        $this->sql = Sql::getInstance();
    }

    public function getAntragId()
    {
        return $this->antrag_id;
    }

    public function getName()
    {
        return $this->mhn_vorname . ' ' . $this->mhn_nachname;
    }

    public function getVorname()
    {
        return $this->mhn_vorname;
    }

    public function getEMail()
    {
        return $this->user_email;
    }

    //(static) Konstruktor:
    public static function datenByAntragId($id)
    {
        assert(is_numeric($id));
        $dbzeile = Sql::queryToArraySingle(Sql::getInstance()->select(self::TABLE_NAME, '*', 'antrag_id=' . $id));
        if ($dbzeile === null) {
            return null;
        } else {
            return Daten::datenFromDbArray($dbzeile);
        }
    }

    public static function findDatenByEmail(string $email): ?Daten
    {
        $sql = Sql::getInstance();
        $dbzeile = $sql->queryToArraySingle($sql->select(self::TABLE_NAME, '*', 'user_email="' . $sql->escape($email) . '"'));
        if ($dbzeile === null) {
            return null;
        } else {
            return Daten::datenFromDbArray($dbzeile);
        }
    }

    //füllt ein neues Daten-Objekt und gibt es zurück.
    public static function datenFromDbArray(array $array): Daten
    {
        $d = new Daten();
        if (isset($array['antrag_id'])) {
            $d->antrag_id = $array['antrag_id'];
        }
        foreach (self::KEYS as $k) {
            if (!isset($array[$k])) {
                var_dump($array);
                exit;
                throw new \LogicException('missing key: ' . $k, 1614465092);
            }
            $d->$k = $array[$k];
        }
        return $d;
    }

    //fügt die Daten der Datenbank hinzu, mit antrag_id.
    //gibt boolean zurück, ob erfolgreich
    public function addThisDaten($antrag_id): bool
    {
        $array = ['antrag_id' => $antrag_id];
        foreach (self::KEYS as $k) {
            $array[$k] = $this->$k;
        }
        $res = $this->sql->insert(self::TABLE_NAME, $array);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function save(): bool
    {
        $array = [];
        foreach (self::KEYS as $k) {
            $array[$k] = $this->$k;
        }
        $res = $this->sql->update(self::TABLE_NAME, $array, 'antrag_id=' . $this->antrag_id);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    //gibt alle Datenbankfelder als ass. Array zurück.
    public function toArray(): array
    {
        $array = [];
        foreach (self::KEYS as $k) {
            $array[$k] = $this->$k;
        }
        return $array;
    }
}
