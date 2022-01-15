<?php

class qJson
{
    protected static $content;

    



    
    public static function setJson($json) {
        self::$content = [
          'type' => self::FORMAT_JSON,
          'buffor' => $json,
        ];
    }
    
    public static function hasJson() {
        if (self::$content !== null && self::$content['type'] == self::FORMAT_JSON) {
            return true;
        }
        return false;
    }
    
    public static function output($message) {
        $err = new qAjaxJson();
        $err->alert($message);
        $result = $err->encode();
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: text/html');
        echo $result;
        exit;
    }
    
    
}
