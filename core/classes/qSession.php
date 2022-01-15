<?php

/**
 * Klasa statyczna Session przechowująca zmienne sesyjne w pliku Storm
 *
 * @author Krzysztof Wałek
 */
class qSession
{
    /**
     * session ID
     * @var string
     */
    public static $session;
    
    /**
     * timstamp ostatnirgo zapisu
     * @var integer 
     */
    public static $lastActive;

    /**
     *
     * @var object SessionStormRecord 
     */
    public static $record;

    /**
     * Wymaga zapisu
     * @var boolean
     */
    public static $save = false;
    
    private static $autoKill = false;

        /**
     * Sesja bazodanowa.
     *
     * @param string $ses ID sesji (=false)
     */
    public static function init($ses = false, $addValues = []) {
        if (false === $ses) {
            self::$session = session_id();
        } else {
            self::$session = $ses;
        }
    }

    /**
     * Zapisuje dane.
     *
     * @param string $key   Klucz
     * @param mixed  $value Wartość
     *
     * @return bool Status
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }


 
    /**
     * Odczytuje wartości klucza.
     *
     * @param string $key     Klucz
     * @param mixed  $default Wartość domyślna
     *
     * @return mixed Wartość klucza lub domyślna
     */
    public static function get($key, $default = null) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * Sprawdza, czy klucz istnieje.
     *
     * @param string $key Klucz
     *
     * @return bool Czy znalazł klucz
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Usuwa klucz.
     *
     * @param string $key Klucz
     *
     * @return bool Status
     */
    public static function del($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }

        return true;
    }

    /**
     * Usuwa bieżącą sesję
     */
    public static function kill() {

    }





    /**
     * Odczytuje wartości klucza i go usuwa.
     *
     * @param string $key     Klucz
     * @param mixed  $default Wartość domyślna
     *
     * @return mixed Wartość klucza lub domyślna
     */
    public static function getAndDel($key, $default = null) {
        
        if (self::has($key)) {
            $result = self::get($key);
            self::del($key);
            return $result;
        }

        return $default;
    }

    /**
     * Zwraca ID sesji.
     *
     * @return mixed ID sesji
     */
    public static function id() {
        return self::$session;
    }


}

