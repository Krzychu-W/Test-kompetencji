<?php

/**
 * Pomiar czasu
 */
class qTimeExec {
    /** @var number całkowity czas wykonania */
    public static $total;

    /** @var array tablica z czasami pośrednymi */
    public static $betweens = array();

    /**
     * Licznik czasów poścrenich (dla developa).
     *
     * @var array
     */
    public static $items = [];

    /**
     * Inicjowanie total time.
     */
    public static function init()
    {
        self::$total = microtime(true);
    }

    /**
     * Zwraca czs wykonania skryptu php.
     *
     * @param bool|false $precision false zwraca wynik w sekundach z częścią ułamkową
     *
     * @return float
     */
    public static function total($precision = false)
    {
        $microtime = microtime(true);
        if (false === $precision) {
            return round(($microtime - self::$total) * 1000);
        }

        return round(($microtime - self::$total), $precision);
    }

    /**
     * Zwaraca między czas wykonania skryptu.
     *
     * @param string $name - nazwa międzuczasy, nazwa mysql jest zarezerwowana dla zapyta modułu Sql
     *
     * @return string
     */
    public static function between($name = '__DEFAULT__')
    {
        $now = microtime(true);
        $diff = 0;
        if (isset(self::$betweens[$name])) {
            $diff = $now - self::$betweens[$name];
        }
        self::$betweens[$name] = $now;

        return number_format($diff, 5);
    }

    public static function addItem($key, $time)
    {
        if (!array_key_exists($key, self::$items)) {
            self::$items[$key] = ['quantity' => 0, 'total' => 0.0];
        }
        self::$items[$key]['quantity'] += 1;
        self::$items[$key]['total'] += $time;
    }

    public static function getItem($key)
    {
        if (!isset(self::$items[$key])) {
            $result = ['quantity' => 0, 'total' => 0.0];
        } else {
            $result = self::$items[$key];
        }

        return ['quantity' => $result['quantity'], 'total' => number_format($result['total'], 5)];
    }
}

