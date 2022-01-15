<?php

class qContentJson
{
    protected static $content;

    public static function setJson($json) {
        self::$content = $json;

    }
    
    public static function hasJson() {
        if (self::$content !== null) {
            return true;
        }
        return false;
    }
    
    public static function output() {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: text/html');
        echo self::$content->encode();
        exit;
    }
    
    
}
