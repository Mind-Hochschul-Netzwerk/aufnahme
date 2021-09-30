<?php
namespace MHN\Aufnahme;

/**
 * Klasse für Datenbankoperationen.
 */
class Sql implements Interfaces\Singleton
{
    use Traits\Singleton;

    /** @var \mysqli */
    private $mysqli = null;

    /** @var bool */
    private $isInTransaction = false;

    /**
     * Instanziiert das Objekt. Wird von getInstance() (aus Interfaces\Singleton) aufgerufen.
     */
    private function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connect();
    }

    /**
     * Gibt den Verbindungsstatus zurück.
     *
     * @return bool
     */
    public function linkOk()
    {
        return $this->mysqli !== null;
    }

    /**
     * Wandelt das Ergebnis einer Abfrage in einen Array um.
     *
     * Nur sinnvoll, falls mehrere Ergebniszeilen vorliegen können.
     *
     * @param \mysqli_result $result
     * @return mixed[]
     */
    public function queryToArray(\mysqli_result $result)
    {
        $array = [];
        while (($row = $result->fetch_assoc())) {
            $array[] = $row;
        }
        return $array;
    }

    /**
     * Wandelt eine EINZELNE Zeile in einen Array um.
     *
     * @param \mysqli_result $result
     * @return mixed[]|null
     */
    public function queryToArraySingle(\mysqli_result $result)
    {
        return $result->fetch_assoc();
    }

    /**
     * Stellt die Verbindung zum MySQL-Server her
     *
     * @throws \RuntimeException Verbindung konnte nicht hergestellt werden
     * @return void
     */
    private function connect()
    {
        try {
            $this->mysqli = new \mysqli(
                getenv('MYSQL_HOST'),
                getenv('MYSQL_USER'),
                getenv('MYSQL_PASSWORD'),
                getenv('MYSQL_DATABASE'),
            );
        } catch (\mysqli_sql_exception $e) {
            throw new \RuntimeException('Could not connect to database: ' . $e->getMessage(), 1490990178);
        }

        $this->mysqli->set_charset('utf8');
    }

    /**
     * Beendet die Verbindung.
     *
     * @return void
     */
    public function close()
    {
        $this->mysqli->close();
        $this->mysqli = null;
    }

    /**
     * Baut aus einem assoziativen Array einen String für die Datenpaare in
     * INSERT- und UPDATE-Queries.
     *
     * Erlaubte Datentypen für die Einträge: bool, int, float, string, null
     *
     * @param mixed[] $data assoziatives Array [spalte => wert, ...]
     *
     * @return string
     *
     * @throws \InvalidArgumentException wenn ein Eintrag einen nicht-unterstützten Typ hat.
     */
    private function generateSetterString(array $data)
    {
        $pairs = [];
        foreach ($data as $column => $value) {
            $entry = $column . ' = ';
            if (is_int($value) || is_float($value)) {
                $entry .= $value;
            } elseif (is_bool($value)) {
                $entry .= $value ? 'TRUE' : 'FALSE';
            } elseif ($value === null) {
                $entry .= 'NULL';
            } elseif (is_string($value)) {
                $entry .= '"' . $this->escape($value) . '"';
            } else {
                throw new \InvalidArgumentException(
                    'Invalid data type for ' . $column . ' (' . gettype($value) . ')',
                    1491081545
                );
            }
            $pairs[] = $entry;
        }
        return implode(', ', $pairs);
    }

    /**
     * Fügt einen Datensatz in eine Tabelle ein.
     *
     * @param string $tableName
     * @param mixed[] $data assoziatives Array [spalte => wert, ...]
     *
     * @return int Insert-ID, falls diese durch AUTO_INCREMENT gesetzt wurde
     */
    public function insert($tableName, array $data)
    {
        $query = 'INSERT INTO ' . $tableName . ' SET ' . $this->generateSetterString($data);
        $this->query($query);
        return $this->mysqli->insert_id;
    }

    /**
     * Ändert Datensätze in der Tabelle.
     *
     * @param string $tableName
     * @param mixed[] $data assoziatives Array [spalte => wert, ...]
     * @param string $where
     *
     * @return void
     */
    public function update($tableName, array $data, $where = '1')
    {
        $query = 'UPDATE ' . $tableName . ' SET ' . $this->generateSetterString($data) . ' WHERE ' . $where;
        return $this->query($query);
    }

    /**
     * Löscht Datensätze in der Tabelle $table, wo die Bedingung $bed zutrifft.
     *
     * @param string $table
     * @param string $bed
     *
     * @return \mysqli_result
     */
    public function delete($table, $bed)
    {
        assert($table != '');
        $query = "DELETE FROM $table WHERE $bed";
        return $this->query($query);
    }

    /**
     * Führt eine SELECT-Query aus.
     *
     * @param string $table
     * @param string $columns
     * @param string $where
     *
     * @return \mysqli_result
     */
    public function select($table, $columns, $where = '1')
    {
        $query = "SELECT $columns FROM $table WHERE $where";
        return $this->query($query);
    }

    /**
     * Sendet einen Query an den MySQL-Server.
     *
     * @param string $query
     *
     * @return \mysqli_result
     *
     * @throws \RuntimeException wenn die Anfrage fehlschlägt.
     */
    public function query($query)
    {
        try {
            $result = $this->mysqli->query($query);
        } catch (\mysqli_sql_exception $e) {
            throw new \RuntimeException(
                "Error executing SQL query.\nMySQL said:\n" . $e->getMessage() . "\nQuery was: $query\n",
                1490990602
            );
        }
        return $result;
    }

    /**
     * Lädt die Datei $file und führt die SQL-Queries darin aus.
     *
     * @return void
     * @throws \RuntimeException wenn ein Query fehlschlägt.
     */
    public function read(string $file)
    {
        $queries = file_get_contents($file);
        try {
            $result = $this->mysqli->multi_query($queries);
            while ($this->mysqli->more_results()) {
                $this->mysqli->next_result();
                $result = $this->mysqli->use_result();
                if ($result !== false) {
                    $result->close();
                }
            }
        } catch (\mysqli_sql_exception $e) {
            throw new \RuntimeException('Datenbank-Fehler: ' . $e->getMessage() . ' (Query: ' . $queries . ')', 1494792465);
        }
    }

    /**
     * String-Escaping
     *
     * @param string $string
     *
     * @return string
     */
    public function escape($string)
    {
        return $this->mysqli->real_escape_string($string);
    }

    /**
     * Locks the table $table for writing.
     *
     * @param string $table
     *
     * @return \mysqli_result
     */
    public function lockTables($table)
    {
        $query = 'LOCK TABLES ' . $table . ' WRITE';
        return $this->query($query);
    }

    /**
     * Unlocks all tables.
     *
     * @return \mysqli_result
     */
    public function unlockTables()
    {
        $query = 'UNLOCK TABLES';
        return $this->query($query);
    }

    /**
     * Prüft, ob gerade eine Transaktion ausgeführt wird.
     *
     * @return bool
     */
    public function checkForTransaction()
    {
        return $this->isInTransaction;
    }

    /**
     * Beginnt eine Transaktion.
     *
     * @throws \LogicException Es wurde schon eine Transaktion gestartet.
     *
     * @return void
     */
    public function startTransaction()
    {
        if ($this->checkForTransaction()) {
            throw new \LogicException('Transactions cannot be nested.', 1490649586);
        }
        $this->query('START TRANSACTION');
        $this->isInTransaction = true;
    }

    /**
     * Wendet die Änderungen einer Transaktion an und beendet die Transaktion.
     *
     * @return void
     *
     * @throws \LogicException Es wurde keine Transaktion gestartet.
     */
    public function commit()
    {
        if (!$this->checkForTransaction()) {
            throw new \LogicException('Not in a transaction.', 1490649757);
        }
        $this->query('COMMIT');
        $this->isInTransaction = false;
    }

    /**
     * Bricht eine Transaktion ab.
     *
     * @return void
     *
     * @throws \LogicException Es wurde keine Transaktion gestartet.
     */
    public function rollback()
    {
        if (!$this->checkForTransaction()) {
            throw new \LogicException('Not in a transaction.', 1490649912);
        }
        $this->query('ROLLBACK');
        $this->isInTransaction = false;
    }
}
