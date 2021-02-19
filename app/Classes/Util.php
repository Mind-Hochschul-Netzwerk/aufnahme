<?php
namespace MHN\Aufnahme;

/**
 * Diese Klasse ist nur als Namespace zu verstehen, also alles "static".
 */
class Util
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public static function emailIsValid($email)
    {
        $matcher = "/^[a-z0-9_\+-\.]+@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})$/i";
        if (!preg_match($matcher, $email)) {
            return false;
        }
        return true;
    }

    /**
     * @param int $ts
     *
     * @return string
     */
    public static function tsToDatum($ts)
    {
        if ($ts == 0) {
            return '';
        }
        return date('d.m.Y', $ts);
    }

    /**
     * Konvertiert ein formatiertes Datum in einen UNIX-Timestamp.
     * Nimmt ein Datum der Form tt.mm.jjjj entgegen.
     *
     * @param string $datum ein Datum der Form tt.mm.jjjj
     *
     * @return int|false der entsprechende Timestamp oder false bei einem Fehler
     */
    public static function datumToTs($datum)
    {
        if ($datum == '') {
            return 0;
        }
        $res = strptime($datum, '%d.%m.%Y');
        if ($res['unparsed'] != '') {
            return false;
        }
        return strtotime(($res['tm_year'] + 1900) . '-' . ($res['tm_mon'] + 1) . '-' . $res['tm_mday']);
    }
}
