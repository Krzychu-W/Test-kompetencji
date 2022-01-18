<?php

namespace Alteris\Model;

/**
 * Podstawa rekordu danych.
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class Record
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
     * Ładuje rekord o określonym id.
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

        $this->temporaryFields += array_diff_key($fields, $this->fields);
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
    }

    /**
     * Zapisuje model za pomocą drivera.
     *
     * @return mixed id zapisanego rekordu lub false
     */
    public function save()
    {
        $id = $this->table->saveRecord($this->getValues(), $this->isNew);
        if ($id > 0) {
            $this->setValue('id', $id);
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

    public function defFields($fields) {
        foreach ($fields as $name => $attribs) {
            $this->fields[$name] = $attribs['default'];
        }
    }

    public function setValue($name, $value) {
        if ($this->hasField($name)) {
            $this->fields[$name] = $value;
            return true;
        }

        return false;
    }

    public function setValues($tab)
    {
        if (is_object($tab)) {
            $tab = (array)$tab;
        }
        foreach ($tab as $name => $value) {
            $this->setValue($name, $value);
        }
    }

    public function hasField($name) {
        return (isset($this->fields[$name]));
    }

    public function getValue($name)
    {
        if ($this->hasField($name)) {
            return $this->fields[$name];
        }

        return null;
    }

    public function getValues()
    {
        return $this->fields;
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
        return $this->getValue($field);
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
        $this->setValue($field, $value);

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
        return $this->hasField($field);
    }

    /**
     * Czyści wszystkie watości z danego pola.
     *
     * @param string $field - nazwa pola
     */
    public function clearValues()
    {
        $this->defFields($this->table->getFields());
    }

    public function getTable()
    {
        return $this->table;
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

}
