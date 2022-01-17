<?php

namespace Alteris\Model;

/**
 * Podstawa rekordu danych.
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class Record implements Serializable
{
    /** @var StormModelTable odnośnik do modelu */
    protected $table = false;

    /** @var array() Tabela z polami */
    protected $fields = [];

    /** @var bool Czy to jest nowy rekord */
    protected $isNew = true;

    /** czy rekord jest zapisany w obecnej postaci */
    protected $isSaved = true;


    /**
     * lista błędów validatora.
     *
     * @var bool|array
     */
    protected $validateError = false;




    /**
     * @param StormModelTable $model
     */
    public function __construct(&$table)
    {
        // incjowanie modelu
        $this->table = $table;
        $this->fields = [];
    }

    /**
     * Ładuje rekord o określonym id i języku.
     *
     * @todo Określić sposób działanie w przypadki braku rekordu o określonym języku ? DEFAULT LANG?
     *
     * @param int   $id
     *
     * @return bool sukces lub porażka
     */
    public function load($id)
    {
        $fields = $this->table->loadItem($id);
        if (false === $fields) {
            $this->fields = [];
            return false;
        }
        $this->isNew = false;
        $columns = $this->model->driver()->fieldSource();
        foreach ($this->table->getFields() as $key => $attribs) {

            if ($this->multilang) {

            } else {
                $is = false;
                if (is_array($columns[$key])) {
                    $this->fields[$key] = [];
                    foreach ($columns[$key] as $sub) {
                        if (isset($fields[$key.'_'.$sub])) {
                            foreach ($fields[$key.'_'.$sub] as $ord => $value) {
                                $this->fields[$key][$ord][$sub] = $value;
                            }
                            $is = true;
                        }
                    }
                } elseif (isset($fields[$columns[$key]])) {
                    $this->fields[$key] = $this->jsonDecode($attribs, $fields[$columns[$key]]);
                    $is = true;
                }
                if (!$is) {
                    $this->fields[$key] = array();
                    $class = $this->className($attribs['type']);
                    $obj = new $class($this, $key, 0, $attribs);
                    $obj->setNew();
                }
            }
        }

        $this->temporaryFields += array_diff_key($fields, $this->fields); // TODO: implement in StormModelRecordMultilang
        $this->domains_array = [];
        foreach ($this->selectRowsDomain() as $row) {
            $this->domains_array[] = $row->domain_id;
        }

        return true;
    }

    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * Wypełnia rekord domyślnymi wartościami.
     *

     */
    public function setNew()
    {
        $this->fields = [];
        $this->isNew = true;


        foreach ($this->model->getData('field') as $key => $attribs) {
            $this->fields[$key] = array();
            $class = $this->className($attribs['type']);
            $obj = new $class($this, $key, 0, $attribs);
            $obj->setNew();
        }

    }

    /**
     * Zapisuje model za pomocą drivera.
     *
     * @return mixed id zapisanego rekordu lub false
     */
    public function save()
    {
        $id = $this->table->saveItem($this->getValues(), $this->isNew, $this->lang);
        if ($id > 0) {
            $this->id = $id;
        }

        if ($id > 0) {
            $this->isSaved = true;
            $this->isNew = false;

        }
        return $id;
    }



    /**
     * Usunięcie rekodu.
     *
     * @todo Metoda nieobsłużona
     */
    public function delete()
    {
        return $this->model->delete($this->id);
    }

    /**
     * Zwracaj pojedyńczą wartość w postaci objektu.
     *
     * @param string $field
     * @param int    $ord
     *
     * @return mixed
     */
    public function getField($field, $ord = 0)
    {
        if ($this->model->hasDataField($field)) {
            $defField = $this->model->getData('field', $field);
            $class = $this->className($defField['type']);
            $obj = new $class($this, $field, $ord, $defField);

            return $obj;
        }

        return false;
    }

    /**
     * W przypadku podania nazwy pola wartośc w postaci tablicy objektów
     *   w przesiwnym przypadku zwraza standardowy objekt ze wszystkimi polami.
     *
     * @param false|string $field
     *
     * @return mixed Objekt lub array
     */
    public function getFields($field = false)
    {
        if ($field) {
            $result = [];
            if ($this->multilang) {
                $xLang = $this->lang;
                $pos = strrpos($field, '_');
                if (false !== $pos) {
                    $pLang = substr($field, $pos + 1);
                    if (in_array($pLang, $this->langList)) {
                        $xLang = $pLang;
                        $field = substr($field, 0, $pos);
                    }
                }
                if ($this->model->hasDataField($field)) {
                    $defField = $this->model->getData('field', $field);
                    $class = $this->className($defField['type']);
                    if ($defField['lang']) {
                        foreach ($this->fields[$xLang][$field] as $ord => $value) {
                            $result[$ord] = new $class($this, $field, $ord, $defField);
                        }
                    } else {
                        foreach ($this->fields['und'][$field] as $ord => $value) {
                            $result[$ord] = new $class($this, $field, $ord, $defField);
                        }
                    }
                }
            } else {
                if ($this->model->hasDataField($field)) {
                    $defField = $this->model->getData('field', $field);
                    $class = $this->className($defField['type']);
                    foreach ($this->fields[$field] as $ord => $value) {
                        $result[$ord] = new $class($this, $field, $ord, $defField);
                    }
                }
            }

            return $result;
        }
        $result = new stdClass();
        foreach ($this->model->getData('field') as $field => $value) {
            $result->$field = $this->getFields($field);
        }

        return $result;
    }

    /**
     * Ustawienie danych w rekordzie.
     *
     * @param string $field
     * @param int    $ord
     * @param mixed  $value
     */
    public function setRef($field, $ord, $value)
    {
        if ($this->model->hasDataField($field)) {
            if ($this->multilang) {
                $defField = $this->model->getData('field', $field);
                if ($defField['lang']) {
                    $this->fields[$this->lang][$field][$ord] = $value;
                } else {
                    $this->fields['und'][$field][$ord] = $value;
                }
            } else {
                $this->fields[$field][$ord] = $value;
            }
        }
    }

    /**
     * Pobranie danych z rekordu.
     *
     * @param string $field
     * @param int    $ord
     *
     * @return mixed
     */
    public function getRef($field, $ord)
    {
        if ($this->model->hasDataField($field)) {
            if ($this->multilang) {
                $defField = $this->model->getData('field', $field);
                if ($defField['lang']) {
                    if (isset($this->fields[$this->lang][$field][$ord])) {
                        return $this->fields[$this->lang][$field][$ord];
                    }
                } else {
                    if (isset($this->fields['und'][$field][$ord])) {
                        return $this->fields['und'][$field][$ord];
                    }
                }
            } else {
                if (isset($this->fields[$field][$ord])) {
                    return $this->fields[$field][$ord];
                }
            }
        }
    }

    /**
     * Szybki dostęp do danych prostych
     *   występuje w wariancie r i f.
     *
     * @param mixed $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        $obj = $this->getField($field);
        if ($obj) {
            return $obj->value;
        }else if ($field == 'level') {
            return $this->getField('hierarchy_deep')->value;
        }

        return null;
    }

    /**
     * Szybki dostęp do danych prostych
     *   występuje w wariancie r i f.
     *
     * @param mixed $field
     * @param mixed $value
     */
    public function __set($field, $value)
    {
        $obj = $this->getField($field);
        if ($obj) {
            $obj->value = $value;
        }
    }

    /**
     * Czy jest pole.
     *
     * @param type $field
     *
     * @return bool
     */
    public function __isset($field)
    {
        $obj = $this->getField($field);
        if ($obj) {
            return true;
        }

        return false;
    }



    public function hasField($key)
    {
        return $this->model->hasDataField($key);
    }

    /**
     * Sprawdza czy w multipolu istnieje określona wartość.
     *
     * @param string $field
     * @param mixed  $value
     * @param bool   $strict
     *
     * @return bool true w przypadku istnienia i false w przypadku braku
     */
    public function hasValue($field, $value, $strict = false)
    {
        if ($this->model->hasDataField($field)) {
            $defField = $this->model->getData('field', $field);
            $class = $this->className($defField['type']);
            foreach ($this->fields[$field] as $ord => $vx) {
                $obj = new $class($this, $field, $ord, $defField);
                if ($strict) {
                    if ($obj->value === $value) {
                        return true;
                    }
                } else {
                    if ($obj->value == $value) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Dodaje wartość do multipola, jeżeli nie istnieje.
     *
     * @param string $field
     * @param mixed  $value
     * @param bool   $strict
     *
     * @return bool true w wypadku rzeczywistego dodania i false w przypadku dubla
     */
    public function addValue($field, $value, $strict = false)
    {
        if ($this->model->hasDataField($field)) {
            $defField = $this->model->getData('field', $field);
            $class = $this->className($defField['type']);
            foreach ($this->fields[$field] as $ord => $oldValue) {
                $obj = new $class($this, $field, $ord, $defField);
                if ($strict) {
                    if ($obj->value === $value) {
                        return false;
                    }
                } else {
                    if ($obj->value == $value) {
                        return false;
                    }
                }
            }
            $newObj = $this->addField($field);
            $newObj->value = $value;

            return true;
        }

        return false;
    }

    /**
     * Usuwa wpis o określonej wartość z multipola.
     *
     * @param string $field
     * @param mixed  $value
     * @param bool   $strict
     *
     * @return bool true w przypadku usunięcie i false w przypadku braku
     */
    public function delValue($field, $value, $strict = false)
    {
        $deleted = false;
        if ($this->model->hasDataField($field)) {
            $defField = $this->model->getData('field', $field);
            $class = $this->className($defField['type']);
            foreach ($this->fields[$field] as $ord => $vx) {
                $obj = new $class($this, $field, $ord, $defField);
                if ($strict) {
                    if ($obj->value === $value) {
                        $this->delField($field, $ord);
                        $deleted = true;
                    }
                } else {
                    if ($obj->value == $value) {
                        $this->delField($field, $ord);
                        $deleted = true;
                    }
                }
            }
        }

        return $deleted;
    }

    /**
     * Czyści wszystkie watości z danego pola.
     *
     * @param string $field - nazwa pola
     */
    public function clearValues($field)
    {
        if ($this->model->hasDataField($field)) {
            $this->fields[$field] = [];
        }
    }

    /**
     * Metoda pobiera wartośc pól lub konkretnego pola.
     *
     * @param mixed $field -  false lub nazwa pola
     *
     * @return mixed - objekt z tablicami wartośc lub tablica wrtość konkretnego pola
     */
    public function getValues($field = false)
    {
        if ($field) {
            $result = array();
            if ($this->model->hasDataField($field)) {
                $defField = $this->model->getData('field', $field);
                $class = $this->className($defField['type']);
                if ($this->multilang) {
                    if ($defField['lang']) {
                        foreach ($this->fields[$this->lang][$field] as $ord => $value) {
                            $obj = new $class($this, $field, $ord, $defField);
                            $result[$ord] = $obj->valueToSave();
                        }
                    } else {
                        foreach ($this->fields['und'][$field] as $ord => $value) {
                            $obj = new $class($this, $field, $ord, $defField);
                            $result[$ord] = $obj->valueToSave();
                        }
                    }
                } else {
                    foreach ($this->fields[$field] as $ord => $value) {
                        $obj = new $class($this, $field, $ord, $defField);
                        $result[$ord] = $obj->valueToSave();
                    }
                }
            }

            return $result;
        }
        $result = new stdClass();
        foreach ($this->model->getData('field') as $field => $value) {
            $result->$field = $this->getValues($field);
        }

        return $result;
    }

    /**
     * Ustawienie wartości podanych pól z tablicy.
     *
     * @param array $fields
     */
    public function setValues($fields)
    {
        foreach ($fields as $field => $values) {
            if ($this->model->hasDataField($field)) {
                if (!is_array($values)) {
                    $values = [$values];
                }
                $this->clearValues($field);
                foreach ($values as $value) {
                    $this->addValue($field, $value);
                }
            }
        }
    }

    /**
     * Metoda zwraca język objektu.
     *
     * @return string|bool
     */
    public function getLang()
    {
        return $this->lang;
    }



    public function getTable()
    {
        return $this->table;
    }


    /**
     * zwraca nazwę klasy pola modelu na podstawie nazwy skróconej.
     *
     * @param type $class
     *
     * @return type
     */
    private function className($class)
    {
        return 'StormModelField'.$class;
    }

    /**
     * @param $parentId
     * @param $order
     *
     * @return $this
     *
     * @throws Exception when child depth would exceed highest possible depth
     */
    public function setOrdering($parentId, $order)
    {
        $hierarchy = str_pad($order, 5, '0', STR_PAD_LEFT);
        $parentHierarchy = '';
        $deep = 0;
        $maxDeep = $this->model->getHierarchy();
        if ($parentId > 0) {
            $parent = $this->model->getRecord($parentId);
            if ($parent) {
                $deep = $parent->hierarchy_deep;
            }
            if ($deep == $maxDeep) {
                throw new Exception("Cannot assign child to parent:{$parentId}. Maximum table depth exceeded.");
            }
            for ($i = 1; $i <= $maxDeep; ++$i) {
                $this->{'hierarchy_p'.$i} = $i <= $deep ? $parent->{'hierarchy_p'.$i} : 0;
            }
            $parentHierarchy = $parent->hierarchy.'-';
        }
        $this->hierarchy_deep = $deep + 1;
        $this->{'hierarchy_p'.$this->hierarchy_deep} = $this->id;
        $this->hierarchy = $parentHierarchy.$hierarchy;
        $this->hierarchy_ordering = $order;

        return $this;
        //TODO: delete when code above works as it should
    /*echo '<pre>';dump('end', $this->getValues());
        if ($parent == 0) {
            $deep = 1;
            $maxDeep = $this->model->getHierarchy();
            $fields = array();
            $fields['hierarchy_p1'] = $this->id;
            for ($ii = 2; $ii <= $maxDeep; $ii++) {
                $f = 'hierarchy_p'.$ii;
                $fields[$f] = 0;
            }
            $fields['hierarchy_deep'] = $deep;
            $fields['hierarchy'] = strPad0($order, 5);
            $fields['hierarchy_ordering'] = $order;
        }
        else {
            $rec = $this->model->getRecord($parent);
            if ($rec) {
                $deep = $rec->hierarchy_deep;
                $maxDeep = $this->model->getHierarchy();
                $fields = array();
                for ($ii = 1; $ii <= $maxDeep; $ii++) {
                    $f = 'hierarchy_p'.$ii;
                    if ($ii <= $deep) {
                        $fields[$f] = $rec->$f;
                    }
                    else if ($ii == $deep + 1) {
                        $fields[$f] = $this->id;
                    }
                    else {
                        $fields[$f] = 0;
                    }
                }
                $fields['hierarchy_deep'] = $deep + 1;
                $fields['hierarchy'] = $rec->hierarchy.'-'.strPad0($order, 5);
                $fields['hierarchy_ordering'] = $order;
            }
            else {
                // awaria nie ma parentu - wymaga spłaszczenia
                $deep = 1;
                $maxDeep = $this->model->getHierarchy();
                $fields = array();
                $fields['hierarchy_p1'] = $this->id;
                for ($ii = 2; $ii <= $maxDeep; $ii++) {
                    $f = 'hierarchy_p'.$ii;
                    $fields[$f] = 0;
                }
                $fields['hierarchy_deep'] = $deep;
                $fields['hierarchy'] = strPad0($order, 5);
                $fields['hierarchy_ordering'] = $order;
            }
        }
    foreach ($fields as $field => $value) {
      $this->__set($field, $value);
    }*/
    }

    /**
     * Zamiena kolejność pól w polach wielokrotnych.
     *
     * @param string $field    - nazwa pola
     * @param array  $newOrder tablica z nowym porządkiem
     *
     * @return bool - czy udało sią zamienić
     */
    public function ordValues($field, $newOrder)
    {
        if ($this->model->hasDataField($field)) {
            $oldValues = $this->fields[$field];
            $newValues = [];
            $error = false;
            foreach ($newOrder as $ord) {
                if (isset($oldValues[$ord])) {
                    $newValues[] = $oldValues[$ord];
                    unset($oldValues[$ord]);
                } else {
                    $error = true;
                    break;
                }
            }
            if (count($oldValues) > 0) {
                return false;
            }
            $this->fields[$field] = $newValues;

            return true;
        }

        return false;
    }

    /**
     * @param string $field - nazwa pola
     * @param int    $ord   - któr pole przesunąć do góry
     *
     * @return bool - czy się powiodło
     */
    public function topValue($field, $ord)
    {
        if ($this->model->hasDataField($field)) {
            $oldOrder = [];
            foreach ($this->fields[$field] as $ord => $value) {
                $oldOrder[] = $ord;
            }
            if (in_array($ord, $oldOrder)) {
                $newOrder[] = $ord;
                foreach ($oldOrder as $v) {
                    if ($v != $ord) {
                        $newOrder[] = $v;
                    }
                }

                return $this->ordValues($field, $newOrder);
            }
        }

        return false;
    }

    /**
     * Zwraca ilość wpisanych wartości do pola wielokrotnego.
     *
     * @param string $field - nazwa pola
     *
     * @return int|false
     */
    public function countValues($field)
    {
        if ($field) {
            return count($this->fields[$field]);
        }

        return false;
    }

    public function getChildren()
    {
        if (false == $this->isNew && $this->isSaved && $this->model->getHierarchy() > 1) {
            if (empty($this->children)) {
                $items = $this->model->select(['id'])->condition('hierarchy_p'.$this->hierarchy_deep, $this->id)->condition('hierarchy_deep', $this->hierarchy_deep, '>')->rows();

                $items = array_map(function ($row) {
                    return $row->id;
                }, $items);

                $this->children = $items;
            } else {
                $items = $this->children;
            }

            return $items;
        }

        return [];
    }

    public function validate()
    {
        $obj = $this->model->getValidateObject();
        $this->validateError = $obj->validate($this->getValues());
        if (0 == count($this->validateError)) {
            return true;
        }

        return false;
    }

    public function getValidateError()
    {
        return $this->validateError;
    }
}
