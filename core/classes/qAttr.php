<?php

class qAttr implements Countable, Iterator
{
    /** @var array $_data Przechowuje tablice z danymi */
    protected $_data = [];
    /**
     * Konstruktor.
     *
     * @param array $data Tablica itemów
     */
    public function __construct($data = [])
    {

        $this->setItems($data);
    }

    /**
     * Dodaje tablice kluczy w postaci stringa.
     *
     * @param string $items
     */
    public function setItems($items)
    {
        if (is_string($items)) {
            $tab = explode(';', $items);
            $items = array();
            foreach ($tab as $row) {
                $row = trim($row);
                if ('' != $row) {
                    $pos = strpos($row, ':');
                    if (false === $pos) {
                        $items[$row] = true;
                    } else {
                        $items[substr($row, 0, $pos)] = $this->fromSemicolon(substr($row, $pos + 1));
                    }
                }
            }
        }
        foreach ($items as $key => $value) {
            $this->setItem($key, $value);
        }
    }

    /**
     * Zwraca tablicę itemów jako string.
     *
     * @return string Tablica itemów
     */
    public function asStr()
    {
        $res = '';
        foreach ($this->items() as $key => $value) {
            if (true === $value) {
                $res .= $key.';';
            } elseif (false !== $value) {
                $res .= $key.':'.$this->toSemicolon($value).';';
            }
        }

        return $res;
    }

    /**
     * Zwraca tablicę itemów jako string.
     *
     * @return string Tablica itemów
     */
    public function asString()
    {
        return $this->asStr();
    }

    public function toSemicolon($str)
    {
        return str_replace(';', '<semicolon>', $str);
    }

    public function fromSemicolon($str)
    {
        return str_replace('<semicolon>', ';', $str);
    }
    
    /**
     * Metoda wewnętrzna.
     *
     * @internal
     *
     * @param mixed $key
     */
    public function __isset($key)
    {
        if ('_' == substr($key, 0, 1)) {
            return isset($this->$key);
        }

        return $this->hasItem($key);
    }

    /**
     * Metoda wewnętrzna.
     *
     * @internal
     *
     * @param mixed $key
     */
    public function __get($key)
    {
        if ('_' == substr($key, 0, 1)) {
            return $this->$key;
        }

        return $this->item($key);
    }

    /**
     * Metoda wewnętrzna.
     *
     * @internal
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if ('_' == substr($key, 0, 1)) {
            $this->$key = $value;
        }
        $this->setItem($key, $value);
    }

    /**
     * Metoda wewnętrzna.
     *
     * @internal
     *
     * @param mixed $key
     */
    public function __unset($key)
    {
        if ('_' == substr($key, 0, 1)) {
            unset($this->$key);
        }

        return $this->delItem($key);
    }

    /**
     * Sprawdza czy istnieje klucz.
     *
     * @param mixed $key Klucz
     *
     * @return bool Czy klucz istnieje
     */
    public function hasItem($key)
    {
        if (!is_array($this->_data)) {
            return false;
        }

        return array_key_exists($key, $this->_data);
    }

    /**
     * Sprawdza czy zwraca wartość klucza lub wartość domyślną.
     *
     * @param mixed $key     Klucz
     * @param mixed $default Wartość domyślna w przypadku braku klucza (false)
     *
     * @return mixed Wartość klucza lub wartość domyślna
     */
    public function getItem($key, $default = null)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return $default;
        }
    }
    
    public function item($key, $default = null) {
        return $this->getItem($key, $default);
    }

    /**
     * Ustawia wartość klucza.
     *
     * @param mixed $key   Klucz
     * @param mixed $value Wartość klucza
     */
    public function setItem($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Dodaje element do tablicy.
     *
     * @param mixed $key   Klucz
     * @param mixed $value Wartość klucza
     */
    public function addItemArray($key, $value)
    {
        $this->_data[$key][] = $value;
    }

    /**
     * Ustawia wartość klucza, tylko kiedy go nie ma.
     *
     * @param mixed $key   Klucz
     * @param mixed $value Wartość klucza
     */
    public function defItem($key, $value)
    {
        if (!$this->hasItem($key)) {
            $this->setItem($key, $value);
        }
    }

    /**
     * Usuwa wartość klucza.
     *
     * @param mixed $key kucz
     */
    public function delItem($key)
    {
        if ($this->hasItem($key)) {
            unset($this->_data[$key]);
        }
    }



    /**
     * Zwraca wszystkie elementy.
     *
     * @deprecated zmieniono na getItems()
     *
     * @return array Tablica elementów
     */
    public function items()
    {
        return $this->getItems();
    }

    /**
     * Zwraca wszystkie elementy.
     *
     * @return array Tablica elementów
     */
    public function getItems()
    {
        if (is_array($this->_data)) {
            return $this->_data;
        }

        return array();
    }

    /**
     * Zwraca tablice elementów w porządku klucz => wartość.
     *
     * @return array Tablica elementów
     */
    public function keys()
    {
        $res = array();
        foreach ($this->_data as $key => $value) {
            $res[] = $key;
        }

        return $res;
    }

    /**
     * Zlicza elementy.
     *
     * @return int Liczba elementów
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Resetuje wskaźnik.
     */
    public function rewind()
    {
        reset($this->_data);
    }

    /**
     * Zwraca pozycję wskaźnika.
     *
     * @return int Pozycja wskaźnika
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * Zwraca index akualnie zaznaczonego klucza.
     *
     * @return mixed Index klucza
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * Przesuwa wskaźnik o jedną pozycję w przód oraz zwraca jego pozycję.
     *
     * @return int Pozycja wskaźnika
     */
    public function next()
    {
        return next($this->_data);
    }

    /**
     * Sprawdza czy klucz nie jest NULL bądź FALSE.
     *
     * @return bool Poprawność klucza
     */
    public function valid()
    {
        $key = key($this->_data);
        $var = (null !== $key && false !== $key);

        return $var;
    }
}
