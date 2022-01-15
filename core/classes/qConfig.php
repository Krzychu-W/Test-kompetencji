<?php

/**
 * Przecowywanie konfiguracji
 */
class qConfig {
    /**
     * @var array - przechwuje konfiguracje
     */
    public static $config = [];
    
    /**
     * @var array - przechwuje konfiguracje
     */
    public static $system = [];

    /**
     * Inicjacja konfiguracj z pliku konfiguracyjnego.
     *
     * @param $file - path do pliku z kofiguracją
     */
    public static function add($file)
    {
        if (file_exists($file)) {
            foreach (require $file as $key => $value) {
                self::set($key, $value);
            }
        }
    }

    /**
     * Ustawienie zmienneje konfig.
     *
     * @param string $key
     * @param $value
     */
    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }

    /**
     * Ustawienie zmiennej domyślnej. Jeżeli znienna istnieje to nie zostanie nadpisana.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function def($key, $value)
    {
        if (!isset(self::$config[$key])) {
            self::$config[$key] = $value;
        }
    }

    /**
     * Pobiera zmenną. Jężeli nie najdzie, zwraca wartość domyślną.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        return $default;
    }

    /**
     * Pobiera zmenną. Jężeli nie najdzie, zwraca wartość domyślną.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function item($key, $default = null)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        return $default;
    }

    /**
     * Sprawdza czy istnieje klucz.
     *
     * @param $key
     *
     * @return bool
     */
    public static function has($key)
    {
        if (isset(self::$config[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Usuwa klucza.
     *
     * @param $key
     */
    public static function del($key)
    {
        if (isset(self::$config[$key])) {
            unset(self::$config[$key]);
        }
    }

    /**
     * Pobiera wszystkie zmienne.
     *
     * @return array
     */
    public static function items()
    {
        return self::$config;
    }

    //zwraca klucze
    public static function keys()
    {
        $res = array();
        foreach (self::$config as $key => $value) {
            $res[] = $key;
        }

        return $res;
    }

    
    /**
     * Ustawienie zmienneje systemową.
     *
     * @param string $key
     * @param $value
     */
    public static function setSys($key, $value)
    {
        self::$system[$key] = $value;
    }


    /**
     * Pobiera zmenną. systemowa.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function getSys($key, $default = null)
    {
        if (isset(self::$system[$key])) {
            return self::$system[$key];
        }

        return $default;
    }
}

