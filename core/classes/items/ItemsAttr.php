<?php

/*
 * Klasa do przechowywania atrybutów dla itemów
 * Rozszerza funkcjonalność klasy Item o atrybuty
 * @author Krzysztof Wałek
 */

class ItemAttr extends qItems
{
    /** @var array $attribs Przechowuje tablice z atrybutami */
    protected $attribs = array();

    /**
     * Ustawia atrybut dla elementu tablicy.
     *
     * @param mixed $key   Klucz
     * @param mixed $attr  Nazwa atrybutu
     * @param mixed $value Wartość atrybutu
     */
    public function setAttr($key, $attr, $value)
    {
        if (!isset($this->attribs[$key])) {
            $this->attribs[$key] = new self();
        }
        $this->attribs[$key]->setItem($attr, $value);
    }

    /**
     * Ustawia wiele atrybutów dla elementu tablicy.
     *
     * @param mixed $key Klcuz
     * @param string @attribs Atrybuty
     */
    public function setAttribs($key, $attribs)
    {
        if (!isset($this->attribs[$key])) {
            $this->attribs[$key] = new self();
        }
        $this->attribs[$key]->setItems($attribs);
    }

    /**
     * Zwraca wartość atrybutu.
     *
     * @param mixed      $key     Klucz
     * @param mixed      $attr    Nazwa atrybutu
     * @param mixed|null $default Wartość defaultowa (=null)
     *
     * @return mixed Wartość atrybutu
     */
    public function attr($key, $attr, $default = null)
    {
        if (!isset($this->attribs[$key])) {
            return $default;
        }

        return $this->attribs[$key]->item($attr, $default);
    }

    /**
     * Zwraca tablicę atrybutów.
     *
     * @return array Tablica atrybutów
     */
    public function attribs()
    {
        return $this->attribs;
    }

    /**
     * Sprawdza czy klucz posiada podany atrybut.
     *
     * @param mixed $key  Klucz
     * @param mixed $attr Nazwa atrybutu
     *
     * @return bool Wynik wyszukiwania
     */
    public function has($key, $attr)
    {
        if (!isset($this->attribs[$key])) {
            return false;
        }

        return $this->attribs[$key]->has($attr);
    }

    /**
     * Definiuje atrybut dla danego klucza.
     *
     * @param mixed $key   Klucz
     * @param mixed $attr  Nazwa atrybutu
     * @param mixed $value Wartość atrybutu
     */
    public function defAttr($key, $attr, $value)
    {
        if (!isset($this->attribs[$key])) {
            $this->attribs[$key] = new self();
        }
        if (!$this->has($key, $attr)) {
            $this->setAttr($key, $attr, $value);
        }
    }

    /**
     * Usuwa podany atrybut z klucza.
     *
     * @param mixed $key  Klucz
     * @param mixed $attr Nazwa atrybutu
     */
    public function delAttr($key, $attr)
    {
        if (isset($this->attribs[$key])) {
            $this->attribs[$key]->delItem($attr);
        }
    }
}
