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
    protected $data = false;



    /** @var int Hierarchia (0 - bez hierarchi,1 - tylko ordering, 2 - zagłębienia 1, itp */
    protected $hierarchy = 0;

    /**
     * Tablca atrybutów validacyjnych.
     *
     * @var array
     */
    protected $validate = false;

    /**
     * Objekt validacyjny.
     *
     * @var type
     */
    protected $validatObj = false;

    /** konstruktor  */
    public function __construct($domain_id = false)
    {
        $this->data = [
            'table' => [],
            'field' => [],
            'record' => 'StormModelRecord',
            'index' => [],
            'unique' => [],
        ];
        // inicjowanie modelu
        $this->init();
    }

    public function setTable($name, $attribs) {
        $this->data['table'] = ['name' => $name];
        $this->addField('id', 'AUTO_INCREMENT');
    }

    public function addField($name, $attribs) {
        $this->data[$name] = $this->parseFieldAttribs($attribs);
    }

    public function addIndex($name, $fields) {
        $this->data['index'][$name] = $fields;
    }

    public function addUnique($name, $fields) {
        $this->data['unique'][$name] = $fields;
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
        return $this->data['field'];
    }


    /**
     * Dodanie nowego rekordu.
     *
     *
     */
    public function newRecord()
    {
        $modelClass = $this->data['record'];
        $obj = new $modelClass($this);
        $obj->setNew();

        return $obj;
    }

    /**
     * Ładuje rekord.
     *
     * @param int   $id
     *
     * @return mixed
     */
    public function getRecord($id)
    {
        $modelClass = $this->data['record'];
        $obj = new $modelClass($this);
        if ($obj->load($id)) {
            return $obj;
        }

        return false;
    }

    /**
     * Ładuje rekord na podstawie przekazanych wartości pól.
     *
     *
     * @return false|StormModelRecord
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



}
