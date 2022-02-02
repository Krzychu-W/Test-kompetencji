<?php

class ModifierReplace
{
    /**
     * Function that allows us to generate shortcuts of strings
     * with cut part replaced by certain replacement.
     * Example: contraction('abcdefgh', '...', 6) => 'aaa...'.
     *
     * @param string $string  the string to be shortened
     * @param string $replace the string to be added at the end
     * @param int    $len     length of the resulting string with replacement added
     *
     * @return string
     */
    public static function parse($string, $param)
    {
        $params = explode(':', $param);
        $replace = current($params);

        $len = end($params);
        if (strlen($string) < $len) {
            return $string;
        }
        $replen = strlen($replace);
        $len -= $replen;
        if ($len <= 0) {
            return $string;
        }
        $ret = substr($string, 0, $len);

        return $ret.$replace;
    }
}
