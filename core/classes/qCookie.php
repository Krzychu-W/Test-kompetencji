<?php

class qCookie
{
    public static $items = array();

    public static function set($key, $value, $time = 0)
    {
        if (0 != $time) {
            $time = time() + $time;
        }
        setcookie($key, $value, $time, '/');
        self::$items[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        if (isset(self::$items[$key])) {
            return self::$items[$key];
        }
        if (isset($_COOKIE[$key])) {
            self::$items[$key] = $_COOKIE[$key];

            return $_COOKIE[$key];
        }

        return $default;
    }

    public static function has($key)
    {
        if (isset(self::$items[$key])) {
            return true;
        }
        if (isset($_COOKIE[$key])) {
            return true;
        }

        return false;
    }

    public static function del($key)
    {
        setcookie($key, '', time() - 3600, '/');
        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
        }
        if (isset(self::$items[$key])) {
            unset(self::$items[$key]);
        }
    }

    public static function kill()
    {
        foreach ($_COOKIE as $key => $value) {
            self::del($key);
        }
        self::$items = array();
    }

    public static function items()
    {
        $result = array();
        foreach ($_COOKIE as $key => $value) {
            $result[$key] = $value;
        }
        foreach (self::$items as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }
}
