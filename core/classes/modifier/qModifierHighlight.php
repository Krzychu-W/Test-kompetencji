<?php

class qModifierHighlight
{
    public static function parse($text, $param)
    {
        $words = str_replace(':', '|', $param);

        if (strlen($text) < 1 || empty($words)) {
            return $text;
        }

        return preg_replace("/($words)/i", '<strong>$0</strong>', $text);
    }
}
