<?php

class qModifierDate
{
    public static function parse($str, $param)
    {
        $date = strtotime($str);
        if ('' == $param) {
            return $str;
        }

        return date($param, $date);
    }
}
