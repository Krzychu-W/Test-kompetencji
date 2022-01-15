<?php

class qPath
{
    /**
     * katalog private.
     *
     * @param string | false $module
     *
     * @return string - ściaęka do katalogu
     */
    public static function getPrivate($module = false)
    {
        $path = qConfig::get('path.root').DIRECTORY_SEPARATOR.'private';
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
        if ($module) {
            $path .= DIRECTORY_SEPARATOR.$module;
            if (!is_dir($path)) {
                @mkdir($path, 0755, true);
            }
        }

        return $path;
    }
    
    public static function checkFolder($folder, $access = 0755) {
        $path = qConfig::get('path.root').DS.$folder;
        if (!is_dir($path)) {
            @mkdir($path, $access, true);
        }
        else {
            if (Environment::OS_WIN != Environment::getOS()) {
                $fileChmod = substr(sprintf('%o', fileperms($path)), -3);
                if ($fileChmod != sprintf('%o', $access)) {
                    if (@chmod($path, $access) === false) {
                        // dodać do błądów
                    }
                }
            }
        }
        if ($access === 0755) {
            $file = $path.DS.'.htaccess';
            if (!file_exists($file)) {
                $fp = fopen($file, 'a');
                flock($fp, 2);
                if ($access) {
                    fwrite($fp, "order allow,deny\nAllow from all  from all");
                }
                else {
                    fwrite($fp, "order allow,deny\n  deny from all\n");
                }
                flock($fp, 3);
                fclose($fp);
            }
        }
    }
}

