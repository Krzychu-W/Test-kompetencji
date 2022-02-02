<?php

class qModifierDecimal
{
    public static function parse($str, $param)
    {
        $fl = (float) $str;
        $param = explode(':', $param);
        $dec = 2;
        $dot = ',';
        $mil = ' ';
        if (isset($param[0])) {
            if ('' !== $param[0]) {
                $dec = $param[0];
            }
        }
        if (isset($param[1])) {
            $dot = $param[1];
        }
        if (isset($param[2])) {
            $mil = $param[2];
        }
        $str = number_format($fl, $dec, $dot, $mil);

        return $str;
    }
}
