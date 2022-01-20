<?php

namespace Alteris\Model;

/**
 * Podstawa rekordu danych.
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class Record
{
    /**
     * Referencja do tabela modelu
     *
     * @var object
     */
    protected object $table;

    /**
     * Tabela asocjacyjna w woartością rekordu
     *
     * @var array
     */
    protected array $fields = [];

    /** @var bool Czy to jest nowy rekord */

    /**
     * Oznaczenie czy to nowy rekord niemający jeszcze zapisu w SQL
     *
     * @var bool
     */
    protected bool $isNew = false;

    /**
     * Konstruktor, parametrem jest tabela modelu
     *
     * @param object $table
     */
    public function __construct(&$table)
    {
        // inicjowanie modelu
        $this->table = $table;
    }

    /**
     * Odczytuje, czy to jest nowy rekord
     *
     * @return bool
     */
    public function isNew():bool
    {
        return $this->isNew;
    }

    /**
     * Ustawia rekord jako new, przydatne przy klonowaniu
     *
     */
    public function setNew()
    {
        $this->isNew = true;
        $this->setValue('id', null);
    }

    /**
     * Zapisuje model w SQL
     *
     * @return integer id zapisanego rekordu lub 0 w wypadku porażki
     */
    public function save():int
    {
        $id = $this->table->saveRecord($this->getValues(), $this->isNew);
        if ($id > 0) {
            $this->setValue('id', $id);
        }
        if ($id > 0) {
            $this->isNew = false;
        }
        return $id;
    }

    /**
     * Usunięcie rekordu.
     *
     */
    public function delete()
    {
        return $this->table->delete($this->getValue('id'));
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
        return (array_key_exists($name, $this->fields));
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
