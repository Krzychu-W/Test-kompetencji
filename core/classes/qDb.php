<?php

/**
 * Moduł połączenia z bazą danych
 *
 * @author Krzysztof Wałek
 */
class qDb
{
    /** @var array lista połączeń */
    public static $connect = array();

    /** @var string połaczenie domyślne (nazwa połaczenia) */
    public static $default = '';

    /**
     * Połacznie z bazą danych.
     *
     * @param $connectName - naza własna połaczenia
     * @param $driver      - nazwa divera (mysql, ...)
     * @param $host        - host (z portem po dwukropku) NIEPRZETESTOWANE
     * @param $name        - nazwa bazy danych
     * @param $user        - user
     * @param $pass        - hasło
     *
     * @return bool|Connect - zwraca połacznie lub false
     */
    public static function init($connectName, $driver, $host, $name, $user, $pass)
    {
        $connect = new qDbConnect($driver, $host, $name, $user, $pass);
        $connect->setConnectName($connectName);
        if ($connect->connected()) {
            if (0 == count(self::$connect)) {
                self::$default = $connectName;
            }
            self::$connect[$connectName] = $connect;

            return $connect;
        }

        return false;
    }

    /**
     * Ustawia nowe połaczenie domyślne.
     *
     * @param $connectName - nazwa połacznia
     *
     * @return string - poprzednie połaczenie
     */
    public static function setDef($connectName)
    {
        $actDef = self::$default;
        self::$default = $connectName;

        return $actDef;
    }

    /**
     * Sprawdza czy jest połączenie.
     *
     * @param $connectName
     *
     * @return bool
     */
    public static function has($connectName)
    {
        return isset(self::$connect[$connectName]);
    }

    /**
     * Pobiera istniejące połaczenie.
     *
     * @param bool|false $connectName
     *
     * @return bool|qDbConnect
     */
    public static function connect($connectName = false)
    {
        if (false === $connectName) {
            return self::$connect[self::$default];
        }
        if (isset(self::$connect[$connectName])) {
            return self::$connect[$connectName];
        }

        return false;
    }

    /**
     * Zlicza zapytania ze wszystkich połaczeń.
     *
     * @return int
     */
    public static function count()
    {
        $count = 0;
        foreach (self::$connect as $item) {
            $count += $item->totalCount();
        }

        return $count;
    }

    /**
     * Zlicza czas zapytań ze wszystkich połaczeń.
     *
     * @return int
     */
    public static function totalTime()
    {
        $count = 0;
        foreach (self::$connect as $item) {
            $count += $item->totalTime();
        }

        return $count;
    }
}

foreach (qConfig::get('sql.connect', []) as $name => $item) {
    if (!isset($item['driver'])) {
        $item['driver'] = 'mysql';
    }
    if ('mysql' == $item['driver']) {
        qDb::init($name, $item['driver'], $item['host'], $item['name'], $item['user'], $item['pass']);
    } else {
        return;
    }
    $db = qDb::connect($name);
    if (false === $db) {
        //die("Database connect error");
        //exit();
        return;
    }
    $db->utf8();
    $db->prefix($item['prefix']);
}
