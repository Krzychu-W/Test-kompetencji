<?php


class qModifier
{
    public static function parse($str, $kod, &$return = null)
    {
        $modifiers = array();
        $modifiers = explode('|', $kod);
        foreach ($modifiers as $modifier) {
            $mod = explode(':', $modifier);
            $class = ucfirst($mod[0]);
            unset($mod[0]);
            $obj = 'qModifier'.$class;
            //qLog::dump(array($obj, 'parse'), array($str, implode(':', $mod), $return));
            $str = call_user_func_array(array($obj, 'parse'), array($str, implode(':', $mod), $return));
        }

        return $str;
    }
}
