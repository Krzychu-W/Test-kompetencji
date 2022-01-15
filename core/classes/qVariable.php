<?php


/**
 * Class Variable
 * Przechowywuje zmienne w pamięci podczs jednego przebiegu programu.
 *
 * @author Krzysztof Wałek <krzysztof.w@investmag.pl>
 */
class qVariable
{
    /* tablica ze zmiennymi */
    public static $var = array();

    /**
     * ustawia wartość zmiennej.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function set($key, $value)
    {
        self::$var[$key] = $value;
    }

    /**
     * ustawia wartość zmiennej jeżeli jej nie ma.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function def($key, $value)
    {
        if (!isset(self::$var[$key])) {
            self::$var[$key] = $value;
        }
    }

    /**
     * pobiera zmienną.
     *
     * @param string $key
     * @param mixed  $default - wartość dmomyślan jeżeli nie ma klucza
     *
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (isset(self::$var[$key])) {
            return self::$var[$key];
        }

        return $default;
    }

    /**
     * sprawdza, czy istnieje klucz.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function has($key)
    {
        if (isset(self::$var[$key])) {
            return true;
        }

        return false;
    }

    /**
     * usuwa zmienną.
     *
     * @param $key
     */
    public static function del($key)
    {
        if (isset(self::$var[$key])) {
            unset(self::$var[$key]);
        }
    }

    /**
     * Zwraca wszystkie zmienne.
     *
     * @return array
     */
    public static function items()
    {
        return self::$var;
    }

    /**
     * Zwraca klucza.
     *
     * @return array
     */
    public static function keys()
    {
        $res = array();
        foreach (self::$var as $key => $value) {
            $res[] = $key;
        }

        return $res;
    }
    
    public static function uuidV4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          // 32 bits for "time_low"
          mt_rand(0, 0xffff), mt_rand(0, 0xffff),
          // 16 bits for "time_mid"
          mt_rand(0, 0xffff),
          // 16 bits for "time_hi_and_version",
          // four most significant bits holds version number 4
          mt_rand(0, 0x0fff) | 0x4000,
          // 16 bits, 8 bits for "clk_seq_hi_res",
          // 8 bits for "clk_seq_low",
          // two most significant bits holds zero and one for variant DCE1.1
          mt_rand(0, 0x3fff) | 0x8000,
          // 48 bits for "node"
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    } 
    
    public static function random() {
        return sprintf('%04x%04x%04x',mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }
    
}
