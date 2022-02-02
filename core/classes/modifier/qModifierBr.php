<?php

class qModifierBr
{
    public static function parse($str, $param)
    {
        return str_replace("\n", '<br />', $str);
    }
}
