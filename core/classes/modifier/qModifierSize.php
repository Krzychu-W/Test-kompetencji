<?php

class qModifierSize
{
    public static function parse($size, $mod)
    {
        $size = trim($size);
        if (is_numeric($size)) {
            if ($size >= 1181116006) {
                $size = number_format($size / 1073741824, 2).' GB';
            } elseif ($size >= 1153434) {
                $size = number_format($size / 1048576, 2).' MB';
            } elseif ($size >= 1126) {
                $size = number_format($size / 1024, 2).' KB';
            } elseif ($size > 1) {
                $size = $size.' bajtów';
            } elseif (1 == $size) {
                $size = $size.' bajt';
            } else {
                $size = '0 bajtów';
            }

            return str_replace('.', ',', $size);
        }
    }
}
