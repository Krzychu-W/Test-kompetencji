<?php

class qContent
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_HTML = 'html';
    const FORMAT_LAYOUT = 'layout';
    const FORMAT_CONTENT = 'content';
    const FORMAT_PDF = 'pdf';

    private static $format = self::FORMAT_LAYOUT;

    public static function format($format = false)
    {
        if ($format) {
            return self::$format == $format;
        } else {
            return self::$format;
        }
    }

    public static function setFormat($format)
    {
        self::$format = $format;
    }

    public static function json()
    {
        return self::format(self::FORMAT_JSON);
    }

    public static function xml()
    {
        return self::format(self::FORMAT_XML);
    }

    public static function html()
    {
        return self::format(self::FORMAT_HTML);
    }

    public static function layout()
    {
        return self::format(self::FORMAT_LAYOUT);
    }

    public static function isContent()
    {
        return self::format(self::FORMAT_CONTENT);
    }

    public static function isPdf()
    {
        return self::format(self::FORMAT_PDF);
    }
    
    
    
}
