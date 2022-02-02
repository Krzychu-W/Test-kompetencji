<?php

class qModifierUrl
{
    public static function parse($str, $mod)
    {
        $parsedUrl = parse_url($str);
        if (strlen($parsedUrl['scheme']) > 0) {
            $scheme = $parsedUrl['scheme'];
        } else {
            $scheme = 'http';
        }

        return $scheme.'://'.$parsedUrl['host'].$parsedUrl['path'].$parsedUrl['query'];
    }
}
