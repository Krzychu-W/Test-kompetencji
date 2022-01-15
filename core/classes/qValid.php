<?php

class qValid
{
    public static $lastError = '';

    private $msgError = array();

    public function strError()
    {
        $result = '';
        foreach ($this->msgError as $item) {
            if ('' != $result) {
                $result .= '<br />';
            }
            $result .= $item;
        }

        return $result;
    }

    public function addError($str)
    {
        $this->lastError[] = $str;
    }

    public function clsError($str)
    {
        $this->lastError = array();
    }

    public static function isName($aValue)
    {
        $result = String::name($aValue);
        if ($result) {
            self::$lastError = '';
        } else {
            self::$lastError = 'nieprawidłowe znaki w nazwie, dopuszczalne są tylko duże i małe litery (łacznie z polskimi), cyfry, myślnik i podkreślenie';
        }

        return $result;
    }

    public static function isFieldName($aValue)
    {
        self::$lastError = '';
        if (String::fieldName($aValue)) {
            return true;
        }

        return false;
    }

    public static function isEmailAddress($emailaddres)
    {
        if (!filter_var($emailaddres, FILTER_VALIDATE_EMAIL)) {
            self::$lastError = 'Adres e-mail nie jest poprawny';

            return false;
        }
        self::$lastError = '';

        return true;
    }

    public static function onlyElement($str, $elements)
    {
        for ($i = 0, $len = mb_strlen($str, 'utf8'); $i < $len; ++$i) {
            $char = mb_substr($str, $i, 1, 'utf8');
            if (false === mb_strpos($elements, $char, 0, 'utf8')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Funkcja sprawdza czy string jest w formacie json.
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isJson($string)
    {
        json_decode($string);

        return JSON_ERROR_NONE == json_last_error();
    }
}
