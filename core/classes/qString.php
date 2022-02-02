<?php

class qString {
    
    /**
     * Kontroluje, czy string rozpoczyna się sekwencją
     * @param string $str
     * @param string $begin
     * @return boolean
     */
    public static function strBegin($str, $begin) {
        return (substr($str, 0, strlen($begin)) === $begin);
    }
    
    /**
     * Kontroluje, czy string kończy się sekwencją
     * @param string $str
     * @param string $end
     * @return boolean
     */
    public static function strEnd($str, $end) {
        return (substr($str, strlen($str) - strlen($end), strlen($end))  === $end);
    }
    
    
    public static function cutBegin($str, $begin) {
        return substr($str, strlen($begin));
    }
    
    public static function strPadLeft($input, $lenght, $pad = ' ') {
        return self::str_pad_unicode($input, $lenght, $pad, STR_PAD_LEFT);
    }

    public static function strPadRight($input, $lenght, $pad = ' ') {
        return self::str_pad_unicode($input, $lenght, $pad, STR_PAD_RIGHT);
    }

    public static function strPadCenter($input, $lenght, $pad = ' ') {
        return self::str_pad_unicode($input, $lenght, $pad, STR_PAD_BOTH);
    }

    public static function strPad0($input,$lenght) {
        return self::str_pad_unicode($input, $lenght, '0', STR_PAD_LEFT);
    }
    
    public static function str_pad_unicode($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
        $str_len = mb_strlen($str);
        $pad_str_len = mb_strlen($pad_str);
        if (!$str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
            $str_len = 1;
        }
        if (!$pad_len || !$pad_str_len || $pad_len <= $str_len) {
            return $str;
        }
        $result = null;
        $repeat = ceil($str_len - $pad_str_len + $pad_len);
        if ($dir == STR_PAD_RIGHT) {
            $result = $str . str_repeat($pad_str, $repeat);
            $result = mb_substr($result, 0, $pad_len);
        }
        else if ($dir == STR_PAD_LEFT) {
            $result = str_repeat($pad_str, $repeat) . $str;
            $result = mb_substr($result, -$pad_len);
        }
        else if ($dir == STR_PAD_BOTH) {
            $length = ($pad_len - $str_len) / 2;
            $repeat = ceil($length / $pad_str_len);
            $result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length)).$str.mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
        }
        return $result;
    }
    
    function substrReplace($source, $start, $end, $replacement) {
        $pattern = '/' . preg_quote($start, '/') . '(.*?)' . preg_quote($end, '/') . '/s';
        $result = preg_replace($pattern, $start . $replacement . $end, $source);

        return $result;
    }
    
    /**
    * Funkcja działa podobnie jak explode, dodatkowo uzupełna tablicję
    * o minimalną wymagana ilośc elementów.
    *
    * @param string $delimiter
    * @param string $string
    * @param int $count
    * @param mix $default
    *
    * @return array
    */
    public static function explodeList($delimiter, $string, $count, $default = false) {
        $res = explode($delimiter, $string);
        while (count($res) < $count) {
            $res[] = $default;
        }
        return $res;
    }

    
}
