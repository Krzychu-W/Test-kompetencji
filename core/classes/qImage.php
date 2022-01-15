<?php

class qImage {
    
    function sendByPath($path) {
        $mime = qMimeType::get($path);
        header('Content-Type: '.$mime);
        $file = @fopen($path, 'rb');
        if ($file) {
            while (!feof($file)) {
                echo fread($file, 1024 * 64);
                flush();
                if (connection_status() != 0) {
                    fclose($file);
                }
            }
            fclose($file);
        }
        exit;
    }
    
    function getContentFromTheme($innerPath) {
        $innerPath = str_replace('\\', '/', $innerPath);
        $ex = explode('/', $innerPath);
        if ($ex[0] == '') {
            unset($ex[0]);
        }
        $path = qConfig::get('path.core').DS.'theme'.DS. implode(DS, $ex);
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return '';
    }
}
