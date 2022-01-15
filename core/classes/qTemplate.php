<?php

class qTemplate {
    private static $used = array();

    // zliczanie ilości i czasu
    public static $counter = 0;
    public static $counterTime = 0;

    protected $success = false;
    protected $fields = array();

    public function __construct() {
    }

    public function render($tpl, $comment = true) {

        $pathToTpl = $this->pathToTpl($tpl);

        if ($pathToTpl) {
            $tplRender = '';
            $tplContent = file_get_contents($pathToTpl);
            $tplRender .= $this->compile($tplContent, $this->fields);
            return $tplRender;

        }
        else {
            return 'Nie znaleziono pliku <strong>'.$tpl.'</strong>';
        }
    }
    
    public function strRender($content) {
        $tplRender .= $this->compile($content, $this->fields);
        return $tplRender;
    }

    public function hasTemplate($tpl)
    {
        $pathToTpl = $this->pathToTpl($tpl);
        if ($pathToTpl) {
            return true;
        }

        return false;
    }

    private function pathToTpl($tpl) {
        // pliki w core
        $pathToTpl = qConfig::get('path.core').DS.'theme'.DS.$tpl.'.php';
        if (PHP_OS == 'WINNT') {
            $pathToTpl = str_replace('/', '\\', $pathToTpl);
        }
        if (file_exists($pathToTpl)) {
            return $pathToTpl;
        }
        return false;
    }

    /*
    public function getFileContent($tpl)
    {
        $pathToTpl = $this->pathToTpl($tpl);
        if (false === $pathToTpl) {
            return false;
        }
        $block = new TemplateFileParse($pathToTpl, 'module');

        return $block->getFileContent();
    }
    */

    public function __get($key)
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        }

        return '';
    }

    public function get($key, $default)
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        }

        return $default;
    }

    public function __set($key, $aValue)
    {
        $this->fields[$key] = $aValue;
    }

    public function set($key, $aValue)
    {
        $this->fields[$key] = $aValue;
    }

    public static function usedAdd($module, $tpl, $file)
    {
        self::$used[$module.DS.'tpl'.DS.$tpl] = array('tpl' => $tpl, 'module' => $module, 'file' => $file);
        //Logger::write(self::$used[$module.DS.'tpl'.DS.$tpl]);
    }

    public static function used()
    {
        ksort(self::$used);

        return self::$used;
    }

    public static function counterGet()
    {
        return ['count' => self::$counter, 'time' => self::$counterTime];
    }
    
    private function compile($string, $fields) {
        extract($fields, EXTR_SKIP);
        ob_start();
        eval('?>'.$string);
        return ob_get_clean();
    }
}
