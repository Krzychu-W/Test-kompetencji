<?php
/**
 * Uniwersalny Model Danych (table).
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */

namespace Alteris\Model;

abstract class Table
{
    /**
     * Definicja tabeli
     *
     * @var array
     */
    protected array $table = [];

    /**
     * Definicja pól
     *
     * @var array
     */
    protected array $field = [];

    /**
     * Definicja indeksów
     *
     * @var array
     */
    protected array $index = [];

    /**
     * Definicja indeksów unikatowych
     *
     * @var array
     */
    protected array $unique = [];

    /** konstruktor  */
    public function __construct()
    {
        // inicjowanie modelu
        $this->init();
    }

    public function setTable($name, $attribs) {
        $this->table = [
            'name' => $name
        ];
        $this->addField('id', 'AUTO_INCREMENT');
    }

    public function addField($name, $attribs) {
        $this->field[$name] = $this->parseFieldAttribs($attribs);
    }

    public function addIndex($name, $fields) {
        $this->index[$name] = $fields;
    }

    public function addUnique($name, $fields) {
        $this->unique[$name] = $fields;
    }

    abstract public function init();

    public function getFields() {
        return $this->field;
    }

    /**
     * Model standardowy rekordy, z reguły nadpisywany w potomku
     *
     * @return \Alteris\Model\Record
     */
    protected function objRecord() {
        return new \Alteris\Model\Record($this);
    }


    /**
     * Utworzenie nowego, pustego rekordu.
     *
     * @return object
     */
    public function newRecord()
    {
        $obj = $this->objRecord();
        $obj->defFields($this->field);
        $obj->setNew();

        return $obj;
    }

    /**
     * Utworzenie rekordu z SQL na podstawie ID.
     *
     * @param mixed $id
     *
     * @return object
     */
    public function getRecord($id):object
    {
        $obj = $this->objRecord();
        $obj->defFields($this->field);
        $sql = "SELECT * FROM `{$this->table['name']}` WHERE id='{$id}'";
        $row = \qDb::connect()->select($sql)->row();

        if ($row) {
            $obj->setValues($row);
            return $obj;
        }
        else {
            throw new \Exception('Nie ma takiego rekordu');
        }
    }

    /**
     * Utworzenie rekordu z bazy danych na wiersza z sql lub tablicy asocjacyjnej
     *
     * @param array|object $row
     *
     * @return object
     */
    public function rowRecord($row):object
    {
        $obj = $this->objRecord();
        $obj->defFields($this->field);
        if (is_object($row)) {
            $row = (array)$row;
        }
        $obj->setValues($row);

        return $obj;
    }

    /**
     * Inicjacja atrybutów pól
     *
     * @param string $attribs
     *
     * @return array
     */
    private function parseFieldAttribs(string $attribs):array
    {
        $result = [
            'type' => 'Integer',
            'size' => '',
            'extra' => '',
            'null' => 'NO',
            'attribs' => '',
            'default' => null,
        ];
        foreach(explode(';', $attribs) as $attr) {
            list($n, $v) = \qString::explodeList(':', $attr, 2, '');
            if ($n === 'type')
            {
                $result['type'] = $v;
            }
            else if ($n === 'size')
            {
                $result['size'] = $v;
            }
            else if ($n === 'null')
            {
                $result['null'] = $v;
            }
            else if ($n === 'attribs')
            {
                $result['attribs'] = $v;
            }
            else if ($n === 'default')
            {
                $result['default'] = $v;
            }
            else {
                $result['extra'] = $n;
            }
        }
        return $result;
    }

    /**
     * Funkcja zapisująca dane do SQL
     *
     * @param array $fields - tablica asocjacyjna
     * @param bool $isNew - czy to nowy rekord
     * @return integer - ID zapisanego rekordu, 0 w przypadku niepowodzenia
     * @throws \Exception
     */
    public function saveRecord($fields, $isNew = false)
    {
        $connect = \qDb::connect();
        if ($isNew) {
            if (array_key_exists('id', $fields)) {
                unset($fields['id']);
            }
            $connect->insert($this->table['name'], $fields);
            $error = $connect->errorCode();
            if ($error) {
                throw new \Exception($connect->error());
            }
            $id = $connect->lastInsertId();
        } else {
            if (!array_key_exists('id', $fields)) {
                throw new \Exception('Brak pola id');
            }
            $id = (integer)$fields['id'];
            unset($fields['id']);
            $connect->update($this->table['name'], $fields, ['id' => $id]);
            $error = $connect->errorCode();
            if ($error) {
                throw new \Exception($connect->error());
            }
        }
        return $id;
    }

    /**
     * Usunięcie rekordu
     *
     * @param mixed $id
     * @return bool
     */
    public function delete($id):bool
    {

        return \qDb::connect()->delete($this->table['name'], ['id' => $id]);
    }


}
