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

        // \qLog::dump($this->getValues());
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

}
