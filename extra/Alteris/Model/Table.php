<?php
/**
 * Uniwersalny Model Danych (table).
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */

namespace Alteris\Model;

abstract class Table
{
    /** @var array model danych w postaci tablicy prostej wczytanej z init */
    protected $table = [];

    protected $field = [];

    protected $index = [];

    protected $unique = [];

    /** @var int Hierarchia (0 - bez hierarchi,1 - tylko ordering, 2 - zagłębienia 1, itp */
    protected $hierarchy = 0;

    /** konstruktor  */
    public function __construct($domain_id = false)
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

    public function setHierarchy($deep) {
        $this->hierarchy = $deep;
        if ($deep = 1) {
            $this->addField('ordering', 'type:Integer;UNSIGNED');
        }
        else if ($deep > 1) {
            $this->addField('ordering', 'type:Integer;UNSIGNED');
            $this->addField('parent', 'type:Integer;UNSIGNED');
            $this->addField('hierarchy', 'type:Varchar;255');
        }
    }

    abstract public function init();



    public function getFields() {
        return $this->field;
    }

    /**
     * @return \Alteris\Model\Record
     */
    public function objRecord() {
        return new \Alteris\Model\Record($this);
    }


    /**
     * Dodanie nowego rekordu.
     *
     *
     */
    public function newRecord()
    {
        $obj = $this->objRecord();
        $obj->setNew();
        $obj->defFields($this->field);

        return $obj;
    }

    /**
     * Ładuje rekord.
     *
     * @param int   $id
     *
     * @return object
     */
    public function getRecord($id)
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
            throw new Exception('Nie ma takiego rekordu');
        }
    }

    /**
     * Ładuje rekord na podstawie przekazanych wartości pól.
     *
     *
     * @return false|Alteris\Model\Record
     */
    public function initRecord($fields)
    {
        $modelClass = $this->data['record'];
        $obj = new $modelClass($this);
        if ($obj->load($fields)) {
            return $obj;
        }

        return false;
    }

    /**
     * Funkcja zmienia atrybuty a tablica opisująca pole sql.
     *
     * @param array $attribs
     *
     * @return array
     */
    private function parseFieldAttribs($attribs)
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
     * Odczyt poziomu hierarchy.
     */
    public function getHierarchy()
    {
        return $this->hierarchy;
    }

    /**
     * @param array $fields
     * @param bool $isNew
     * @return integer
     * @throws Exception
     */
    public function saveRecord($fields, $isNew = false) {
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
        }
        else {
            if (!array_key_exists('id', $fields)) {
                throw new Exception('Brak pola id');
            }
            $id = $fields['id'];
            unset($fields['id']);
            $connect->update($this->table['name'], $fields, ['id' => $id]);
            $error = $connect->errorCode();
            if ($error) {
                throw new Exception($connect->error());
            }
            $id = $fields['id'];
        }

        return $id;
    }



}
