<?php

class qModifierCut
{
    public static function parse($string, $param)
    {
        $param = explode(':', $param);
        $lenght = $param[0];
        if (isset($param[1])) {
            $add = $param[1];
            if ('1' === $add) {
                $add = '…';
            }
        } else {
            $add = '';
        }
        if (mb_strlen($string) <= $lenght) {
            return $string;
        }
        $string = mb_substr($string, 0, $lenght);
        if (' ' == mb_substr($string, -1, 1)) {
            return $string.$add;
        }
        // przecięto w połowie wyrazu;
        $pos = mb_strrpos($string, ' ');
        if (false !== $pos) {
            $string = mb_substr($string, 0, $pos);

            return $string.$add;
        }

        return $string.$add;
    }
}
