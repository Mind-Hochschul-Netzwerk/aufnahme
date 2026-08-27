<?php
/**
 * @author Henrik Gebauer <henrik@mind-hochschul-netzwerk.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

declare(strict_types=1);

namespace App\Model;

use Hengeb\Router\Exception\InvalidUserDataException;
use Symfony\Component\HttpFoundation\ParameterBag;

//die eigentlichen Daten des Aufnahmeantrags. Ausgelagert, damit
// bei einer Änderung des Formular leichter anpassbar.
class FormData
{
    private $data = [];
    private static $schema = [];

    const SCHEMA_FILENAME = '/var/www/Resources/Private/formData.yml';

    const COUNTRY_NAMES = [
        'AF' => 'Afghanistan',
        'EG' => 'Ägypten',
        'AX' => 'Ålandinseln',
        'AL' => 'Albanien',
        'DZ' => 'Algerien',
        'AS' => 'Amerikanisch-Samoa',
        'VI' => 'Amerikanische Jungferninseln',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarktis',
        'AG' => 'Antigua und Barbuda',
        'GQ' => 'Äquatorialguinea',
        'AR' => 'Argentinien',
        'AM' => 'Armenien',
        'AW' => 'Aruba',
        'AZ' => 'Aserbaidschan',
        'ET' => 'Äthiopien',
        'AU' => 'Australien',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesch',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgien',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivien',
        'BQ' => 'Bonaire, Sint Eustatius und Saba',
        'BA' => 'Bosnien und Herzegowina',
        'BW' => 'Botsuana',
        'BV' => 'Bouvetinsel',
        'BR' => 'Brasilien',
        'VG' => 'Britische Jungferninseln',
        'IO' => 'Britisches Territorium im Indischen Ozean',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgarien',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'CV' => 'Cabo Verde',
        'CL' => 'Chile',
        'CN' => 'China',
        'CK' => 'Cookinseln',
        'CR' => 'Costa Rica',
        'CI' => "Côte d'Ivoire",
        'CW' => 'Curaçao',
        'DK' => 'Dänemark',
        'CD' => 'Demokratische Republik Kongo',
        'DE' => 'Deutschland',
        'DM' => 'Dominica',
        'DO' => 'Dominikanische Republik',
        'DJ' => 'Dschibuti',
        'EC' => 'Ecuador',
        'SV' => 'El Salvador',
        'ER' => 'Eritrea',
        'EE' => 'Estland',
        'SZ' => 'Eswatini',
        'FK' => 'Falklandinseln',
        'FO' => 'Färöer',
        'FJ' => 'Fidschi',
        'FI' => 'Finnland',
        'FR' => 'Frankreich',
        'GF' => 'Französisch-Guayana',
        'PF' => 'Französisch-Polynesien',
        'TF' => 'Französische Süd- und Antarktisgebiete',
        'GA' => 'Gabun',
        'GM' => 'Gambia',
        'GE' => 'Georgien',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GD' => 'Grenada',
        'GR' => 'Griechenland',
        'GL' => 'Grönland',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard- und McDonaldinseln',
        'HN' => 'Honduras',
        'HK' => 'Hongkong',
        'IN' => 'Indien',
        'ID' => 'Indonesien',
        'IQ' => 'Irak',
        'IR' => 'Iran',
        'IE' => 'Irland',
        'IS' => 'Island',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italien',
        'JM' => 'Jamaika',
        'JP' => 'Japan',
        'YE' => 'Jemen',
        'JE' => 'Jersey',
        'JO' => 'Jordanien',
        'KY' => 'Kaimaninseln',
        'KH' => 'Kambodscha',
        'CM' => 'Kamerun',
        'CA' => 'Kanada',
        'KZ' => 'Kasachstan',
        'QA' => 'Katar',
        'KE' => 'Kenia',
        'KG' => 'Kirgisistan',
        'KI' => 'Kiribati',
        'CC' => 'Kokosinseln (Keelinginseln)',
        'CO' => 'Kolumbien',
        'KM' => 'Komoren',
        'CG' => 'Kongo',
        'HR' => 'Kroatien',
        'CU' => 'Kuba',
        'KW' => 'Kuwait',
        'LA' => 'Laos',
        'LS' => 'Lesotho',
        'LV' => 'Lettland',
        'LB' => 'Libanon',
        'LR' => 'Liberia',
        'LY' => 'Libyen',
        'LI' => 'Liechtenstein',
        'LT' => 'Litauen',
        'LU' => 'Luxemburg',
        'MO' => 'Macau',
        'MG' => 'Madagaskar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Malediven',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MA' => 'Marokko',
        'MH' => 'Marshallinseln',
        'MQ' => 'Martinique',
        'MR' => 'Mauretanien',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexiko',
        'FM' => 'Mikronesien',
        'MD' => 'Moldau',
        'MC' => 'Monaco',
        'MN' => 'Mongolei',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MZ' => 'Mosambik',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NC' => 'Neukaledonien',
        'NZ' => 'Neuseeland',
        'NI' => 'Nicaragua',
        'NL' => 'Niederlande',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'KP' => 'Nordkorea',
        'MK' => 'Nordmazedonien',
        'NF' => 'Norfolkinsel',
        'NO' => 'Norwegen',
        'MP' => 'Nördliche Marianen',
        'OM' => 'Oman',
        'AT' => 'Österreich',
        'PK' => 'Pakistan',
        'PS' => 'Palästina',
        'PW' => 'Palau',
        'PA' => 'Panama',
        'PG' => 'Papua-Neuguinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippinen',
        'PN' => 'Pitcairninseln',
        'PL' => 'Polen',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'RE' => 'Réunion',
        'RW' => 'Ruanda',
        'RO' => 'Rumänien',
        'RU' => 'Russische Föderation',
        'BL' => 'Saint-Barthélemy',
        'KN' => 'Saint Kitts und Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint-Martin (franz. Teil)',
        'PM' => 'Saint Pierre und Miquelon',
        'VC' => 'Saint Vincent und die Grenadinen',
        'SB' => 'Salomonen',
        'ZM' => 'Sambia',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'São Tomé und Príncipe',
        'SA' => 'Saudi-Arabien',
        'SE' => 'Schweden',
        'CH' => 'Schweiz',
        'SN' => 'Senegal',
        'RS' => 'Serbien',
        'SC' => 'Seychellen',
        'SL' => 'Sierra Leone',
        'ZW' => 'Simbabwe',
        'SG' => 'Singapur',
        'SX' => 'Sint Maarten (niederl. Teil)',
        'SK' => 'Slowakei',
        'SI' => 'Slowenien',
        'SO' => 'Somalia',
        'ES' => 'Spanien',
        'LK' => 'Sri Lanka',
        'ZA' => 'Südafrika',
        'SD' => 'Sudan',
        'GS' => 'Südgeorgien und die Südlichen Sandwichinseln',
        'KR' => 'Südkorea',
        'SS' => 'Südsudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard und Jan Mayen',
        'SY' => 'Syrien',
        'TJ' => 'Tadschikistan',
        'TW' => 'Taiwan',
        'TZ' => 'Tansania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad und Tobago',
        'TD' => 'Tschad',
        'CZ' => 'Tschechien',
        'TN' => 'Tunesien',
        'TR' => 'Türkei',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks- und Caicosinseln',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'HU' => 'Ungarn',
        'UY' => 'Uruguay',
        'US' => 'USA',
        'UM' => 'US-Amerikanische Kleinere Inselbesitzungen',
        'UZ' => 'Usbekistan',
        'VU' => 'Vanuatu',
        'VA' => 'Vatikanstadt',
        'VE' => 'Venezuela',
        'AE' => 'Vereinigte Arabische Emirate',
        'GB' => 'Vereinigtes Königreich',
        'VN' => 'Vietnam',
        'WF' => 'Wallis und Futuna',
        'EH' => 'Westsahara',
        'CF' => 'Zentralafrikanische Republik',
        'CY' => 'Zypern',
    ];

    public function __construct(string $jsonData = '[]') {
        $data = json_decode($jsonData, true);

        foreach (static::getSchema() as $name=>$type) {
            switch ($type) {
                case 'mail':
                case 'text':
                    $this->data[$name] = '';
                    if (!empty($data[$name])) {
                        $this->data[$name] = (string)$data[$name];
                    }
                    break;
                case 'bool':
                    $this->data[$name] = false;
                    if (!empty($data[$name])) {
                        $this->data[$name] = (bool)$data[$name];
                    }
                    break;
                case "datetime":
                    if (empty($data[$name])) {
                        $this->data[$name] = null;
                        break;
                    }
                    $this->data[$name] = new \DateTime($data[$name]);
                    break;
            }
        }
    }

    public function json() {
        $data = $this->data;
        foreach ($data as $k=>$v) {
            if (gettype($v) === 'object') {
                $data[$k] = $v->format('c');
            }
        }
        return json_encode($data);
    }

    public function __toString() {
        return $this->json();
    }

    public static function getSchema(): array
    {
        if (!static::$schema) {
            $lines = array_filter(array_map('trim', file(self::SCHEMA_FILENAME)));
            $pairs = array_map(fn($line) => explode(': ', $line), $lines);
            static::$schema = array_combine(array_column($pairs, 0), array_column($pairs, 1));
        }

        return static::$schema;
    }

    public function getName()
    {
        return $this->data['mhn_vorname'] . ' ' . $this->data['mhn_nachname'];
    }

    public function getVorname()
    {
        return $this->data['mhn_vorname'];
    }

    public function getEMail()
    {
        return $this->data['user_email'];
    }

    public function get($key)
    {
        if (!isset(static::$schema[$key])) {
            throw new \OutOfRangeException('key invalid: ' . $key);
        }
        return $this->data[$key];
    }

    public function set($key, $value)
    {
        if (!isset(static::$schema[$key])) {
            throw new \OutOfRangeException('key invalid: ' . $key);
        }
        $this->data[$key] = $value;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * parse and validate user input for parseBirthdayInput
     * If only two digits of the year are given 1900 or 2000 will be added, assuming the person is at least 18 years old.
     */
    public static function parseBirthdayInput(string $input): ?\DateTime {
        $input = str_replace(' ', '', $input);
        // DD.MM.YYYY, DD.MM.YY, D.M.YY, ...
        if (preg_match('/^(\d\d?)\.(\d\d?)\.(\d{2}|\d{4})$/', $input, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            if ($year < (date('Y') % 100) - 18) {
                $year += 2000;
            } elseif ($year < 100) {
                $year += 1900;
            }
        // YYYY-MM-DD
        } elseif (preg_match('/^(\d{4})-(\d\d)-(\d\d)$/', $input, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];
        } else {
            return null;
        }
        try {
            $date = new \DateTime(sprintf("%04d-%02d-%02d", $year, $month, $day));
        } catch (\Exception $e) {
            return null;
        }
        $age = (int) $date->diff(new \DateTime())->format('%R%y');
        if ($age < 10 || $age > 120) {
            return null;
        }
        return $date;
    }

    /**
     * Update by $_POST data
     * does NOT update user_email, kenntnisnahme_datenverarbeitung, kenntnisnahme_datenverarbeitung_text, einwilligung_datenverarbeitung, einwilligung_datenverarbeitung_text, mhn_geburtstag
     * @return bool data is valid
     */
    public function updateFromForm(ParameterBag $submittedData): bool
    {
        $dataIsValid = true;

        foreach (static::getSchema() as $key=>$type) {
            // nicht im Formular änderbar bzw. hier nicht verarbeitet:
            if (in_array($key, [
                'user_email',
                'kenntnisnahme_datenverarbeitung',
                'kenntnisnahme_datenverarbeitung_text',
                'einwilligung_datenverarbeitung',
                'einwilligung_datenverarbeitung_text',
                'mhn_geburtstag',
            ], true)) {
                continue;
            }

            // leere Checkboxen werden nicht gesendet
            if ($type === 'bool') {
                $this->set($key, $submittedData->getBoolean($key));
                continue;
            }

            if (!$submittedData->has($key)) {
                throw new InvalidUserDataException('`' . $key . '` is missing in request body');
            }

            $this->set($key, trim($submittedData->get($key)));
        }

        return $dataIsValid;
    }
}
