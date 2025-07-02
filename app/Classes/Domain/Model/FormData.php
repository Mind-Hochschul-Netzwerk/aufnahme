<?php
namespace App\Domain\Model;

use Symfony\Component\Yaml\Yaml;

//die eigentlichen Daten des Aufnahmeantrags. Ausgelagert, damit
// bei einer Änderung des Formular leichter anpassbar.
class FormData
{
    private $data = [];
    private static $schema = [];

    const SCHEMA_FILENAME = '/var/www/Resources/Private/formData.yml';

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

    public function __toString() {
        $data = $this->data;
        foreach ($data as $k=>$v) {
            if (gettype($v) === 'object') {
                $data[$k] = $v->format('c');
            }
        }
        return json_encode($data);
    }

    public static function getSchema(): array
    {
        if (!static::$schema) {
            static::$schema = Yaml::parse(file_get_contents(self::SCHEMA_FILENAME));
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
     * does NOT update user_email, kenntnisnahme_datenverarbeitung, kenntnisnahme_datenverarbeitung_text, einwilligung_datenverarbeitung, einwilligung_datenverarbeitung_text
     * does NOT update birthday if date is invalid
     * @return data is valid
     */
    public function updateFromForm($smarty): bool
    {
        $dataIsValid = true;
        $postData = $_POST;

        foreach (static::getSchema() as $key=>$type) {
            // leere Checkboxen werden nicht gesendet
            if ($type === 'bool') {
                $postData[$key] = isset($postData[$key]) && $postData[$key] !== "0";
            }

            // nicht im Formular änderbar:
            if (in_array($key, [
                'user_email',
                'kenntnisnahme_datenverarbeitung',
                'kenntnisnahme_datenverarbeitung_text',
                'einwilligung_datenverarbeitung',
                'einwilligung_datenverarbeitung_text'
            ], true)) {
                continue;
            }

            if (!isset($postData[$key])) {
                die('nicht vom Formular gesetzt: ' . $key);
            }

            if ($key === 'mhn_geburtstag' && isset($postData[$key])) {
                $birthday = FormData::parseBirthdayInput($postData[$key]);
                if ($birthday) {
                    $this->set($key, $birthday);
                } else {
                    $smarty->assign('invalidBirthday', true);
                    $dataIsValid = false;
                }
            } elseif (isset($postData[$key])) {
                $this->set($key, $postData[$key]);
            }
        }

        return $dataIsValid;
    }
}
