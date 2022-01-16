<?php

/**
 * Loader
 */
class qLoader {

    private static $fileCache = [];
 
    /**
     * Sprawdzenie, czy istnieje plik z taką klasą.
     *
     * @param string $class_name
     *
     * @return bool
     */
    public static function isClass($class_name)
    {
        if (self::fileClass($class_name)) {
            return true;
        }
        return false;
    }
    
    public static function fileClass($className, $log = false) {
        
        if (!isset(self::$fileCache[$className])) {
            self::$fileCache[$className] = self::fileCore($className, $log);
        }

        return self::$fileCache[$className];
    }
    
    /**
     * Inkludowanie pliku php z definicją klasy.
     *
     * @param string $class_name - nazwa klasy
     */
    public static function autoload($class_name)
    {
        $tabPath = explode('\\', $class_name);
        if (count($tabPath) > 1 && 'Alteris' == $tabPath[0]) {

            // szukaj w core\alteris
            $pathFile = qConfig::get('path.root');
            if (null === $pathFile) {
                $pathFile = '.'.DIRECTORY_SEPARATOR;
            } elseif ('/' != $pathFile) {
                $pathFile .= DIRECTORY_SEPARATOR;
            }
            $pathFile .= 'extra'.DIRECTORY_SEPARATOR;
            $pathFile .= implode(DIRECTORY_SEPARATOR, $tabPath).'.php';
            if (file_exists($pathFile)) {
                require_once $pathFile;
            } else {
                echo 'no file: '.$pathFile.'<br />';
            }
        }
        $file = self::fileClass($class_name);
        if ($file) {
            require_once $file;
        }
    }
    
    public static function fileCore($className) {
        $q = false;
        $classNameSearch = $className;
        if (substr($className, 0, 1) == 'q') {
            $q = true;
            $classNameSearch = substr($className, 1);
        }
        $tab = preg_split('/(?=[A-Z])/', $classNameSearch, -1, PREG_SPLIT_NO_EMPTY);
        
        $file = $className.'.php';
        $subPath = '';
        if (count($tab) > 1) {
            array_pop($tab);   // usuń ostatni
            $subPath .= DS.strtolower(implode(DS, $tab));
        }
        $subPath .= DS.$file;
        $path = SYSTEM_ROOT_PATH.DS.'core'.DS.'classes'.$subPath;
        if (file_exists($path)) {
            
            return $path;
        }
        return false;
    }
}

