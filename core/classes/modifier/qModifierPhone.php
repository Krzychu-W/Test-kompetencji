<?php

class qModifierPhone
{
    private static $ab = array(50 => '', 51 => '', 53 => '', 57 => '', 60 => '', 66 => '', 69 => '', 72 => '', 73 => '', 78 => '', 79 => '', 88 => '');
    private static $mobile_def = array(3 => '', 6 => '', 'prefix' => 'Kom.:');
    private static $home_def = array(2 => '', 5 => '', 7 => '', 'prefix' => 'Tel.:');
    private static $fax_def = array(2 => '', 5 => '', 7 => '', 'prefix' => 'Fax.:');

    public static function parse($str, $mod, &$return = false)
    {
        $num = preg_replace('/[^0-9]/', '', $str);
        if (9 == strlen($num)) {
            if (isset(self::$ab[substr($num, 0, 2)])) {
                $def = self::$mobile_def;
                if (false !== $return) {
                    $return = 'mobile';
                }
            } else {
                $def = self::$home_def;
                if (false !== $return) {
                    $return = 'home';
                }
            }
            for ($i = 0; $i < 9; ++$i) {
                if (isset($def[$i])) {
                    $new_num[] = ' ';
                }
                $new_num[] = $num[$i];
            }

            return implode('', $new_num);
        }

        return $str;
    }
}
