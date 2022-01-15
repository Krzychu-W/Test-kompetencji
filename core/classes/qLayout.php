<?php

class qLayout {
    // nazwa skórki
    public static $theme = 'default';

    // nazwa domyślnego Layoutu
    public static $layoutTpl = 'layout';

    // css ładowane w pierwszej kolejności - zazwyczaj ładowane w skórce
    public static $css = array();

    // js ładowane w pierwszej kolejności - zazwyczaj ładowane w skórce
    public static $js = array();

    // head skrypty - skrypry dołaczone tuż przed </head>
    public static $scriptHead = array();

    // start skrypty - skrypry dołaczone tuż po <body>
    public static $scriptBodyStart = array();

    // stop skrypty - skrypry dołaczone tuż przed </body>
    public static $scriptBodyStop = array();

    // zmienne do qAnt.setting
    public static $setting = array();

    // title - tytuł strony - Tutaj można ustwamić domyślny
    public static $title = 'Moja strona';
    
    // meta Title
    public static $metaTitle = false;

    // rozszerzenie tytułu
    public static $extTitle = '';

    // d omyślny description
    public static $description = '';

    // zastępcy description
    public static $secondDescription = false;

    // metatagi
    public static $meta = array();

    // linki
    public static $link = array();

    // pola
    public static $fields = array();

    protected $moduleName = '';
    
    protected static $script = [];

    /**
     * Funkcja ustawia plik css do wczytania przez Layout w pierwszej kolejności.
     *
     * @param string $file - nazwa pliku lub false - wtedy zwraca listę plików
     */
    public static function css($file = false) {
        if ($file) {
            if (!in_array($file, self::$css)) {
                self::$css[] = $file;
            }
        } 
        else {
            return self::$css;
        }
    }

    /**
     * Funkcja zwraca sformatowane css
     * Enter description here ...
     */
    public static function cssPrint()
    {
        return '<?php echo qLayout::cssPrintRender(); ?>';
    }

    public static function cssPrintRender() {
        $qLibrery = new qLibrery();
        $result = '';
        foreach (self::$css as $file) {
            $href = $qLibrery->hrefCssFile($file);
            if ($href) {
                $result .= '<link rel="Stylesheet" href="'.$href.'" />'."\n";
            }
        }
        return $result;
    }

    /**
     * Funkcja ustawia plik js do wczytania przez Layout w pierwszej kolejności.
     *
     * @param string $file - nazwa pliku lub false - wtedy zwraca listę plików
     */
    public static function js($file = false) {
        if ($file) {
            self::$js[] = $file;
        } 
        else {
            return self::$js;
        }
    }
    
    /**
     * Funkcja ustawia plik js do wczytania przez Layout w pierwszej kolejności.
     *
     * @param string $file - nazwa pliku lub false - wtedy zwraca listę plików
     */
    public static function addScript($script) {
        self::$script[] = $script;
    }

    /**
     * Dodanie wartości do settingu js.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function setting($key, $value)
    {
        //self::$setting[$key] = utf8_encode($value);
        self::$setting[$key] = $value;
    }

    /**
     * Funkcja zwraca sformatowane js.
     */
    public static function developPrint()
    {
        return '<?php echo qDevelop::comment(); ?>';
    }
    
    public static function jsPrint()
    {
        return '<?php echo qLayout::jsPrintRender(); ?>';
    }

    /**
     * Funkcja zwraca sformatowane js.
     */
    public static function jsPrintRender() {
        $result = '';
        $qLibrery = new qLibrery();
        foreach (self::$js as $file) {
            $href = $qLibrery->hrefJsFile($file);
            if ($href) {
                $result .= '<script src="'.$href.'"></script>'."\n";
            }
        }
        if (!isset(self::$setting['args'])) {
            self::$setting['args'] = qCtrl::allArgs();
        }
        if (!isset(self::$setting['lang'])) {
            self::$setting['lang'] = qCtrl::lang();
        }
        if (!isset(self::$setting['cLang'])) {
            self::$setting['cLang'] = qCtrl::cLang();
        }
        if (!isset(self::$setting['host'])) {
            self::$setting['host'] = qConfig::get('url.base');
        }
        if (!isset(self::$setting['volume'])) {
            self::$setting['volume'] = qConfig::get('volume');
        }
        $result .= $qLibrery->contentScript(self::$setting, self::$script);
        return $result;
    }

    /**
     * Funkcja ustawia i zwraca metatag
     * Celem ustawienia matatagu należy podać nazwę oraz tablicę z zawartością.
     *
     * @param string $name   - nazwa metatagu
     * @param array  $values - tablica z wartościami - jeżleli drugi parametr jest pusty, funkcja zwróci wartośc matatagu
     */
    public static function meta($name = false, $values = false)
    {
        if (false === $name) {
            // zwraca obecną zawartość
            return self::$meta;
        } elseif (false === $values) {
            // zwraca zawartość konkretnego metatagu w postaci tablict
            if (isset(self::$meta[$name])) {
                return self::$meta[$name];
            } else {
                return [];
            }
        } else {
            // ustawia nową wartość
            self::$meta[$name] = $values;
        }
    }

    /**
     * Funkcja zwraca sformatowane metatagi.
     */
    public static function metaPrint()
    {
        $result = '';
        // ustalenie descriptiona
        // 1. description z meta
        // 2. descriprion z akcji
        // 3. descripttion zastępczy
        if ((!isset(self::$meta['description']) || '' == self::$meta['description'])) {
            if (self::$description) {
                // description z akcji
                self::$meta['description'] = self::$description;
            } elseif (self::$secondDescription) {
                // description zastępczy
                self::$meta['description'] = self::$secondDescription;
            }
        }
        if ((!isset(self::$meta['description']) || '' == self::$meta['description']) && self::$description) {
            self::$meta['description'] = self::$description;
        }
        foreach (self::$meta as $key => $values) {
            if ('title' !== $key && 'home_title' !== $key) {
                $result .= '<meta name="'.$key.'" content="'.qModifier::parse($values, 'html').'"'." />\n";
            }
        }

        return $result;
    }

    /**
     * Funkcja ustawia i zwraca metatag
     * Celem ustawienia matatagu należy podać nazwę oraz tablicę z zawartością.
     *
     * @param string $name   - nazwa metatagu
     * @param array  $values - tablica z wartościami - jeżleli drugi parametr jest pusty, funkcja zwróci wartośc matatagu
     */
    public static function link($rel = false, $type = false, $href = false, $updateRel = false)
    {
        if (false === $rel) {
            // zwraca obecną zawartość
            return self::$link;
        } else {
            $value = array(
                'rel' => $rel,
                'type' => $type,
                'href' => $href,
              );
            $add = true;
            foreach (self::$link as $key => $link) {
                if ($updateRel) {
                    if ($link['rel'] == $rel) {
                        self::$link[$key] = $value;

                        return;
                    }
                }
                if ($link['rel'] == $value['rel'] && $link['type'] == $value['type'] && $link['href'] == $value['href']) {
                    $add = false;
                }
            }
            if ($add) {
                self::$link[] = $value;
            }
        }
    }

    /**
     * Funkcja zwraca sformatowane metatagi.
     */
    public static function linkPrint()
    {
        $result = '';
        foreach (self::$link as $link) {
            $result .= '<link rel="'.$link['rel'].'"';
            if ($link['type']) {
                $result .= ' type="'.$link['type'].'"';
            }
            if ($link['href']) {
                $result .= ' href="'.$link['href'].'"';
            }
            $result .= " />\n";
        }

        return $result;
    }

    /**
     * Funkcja ustawia i zwraca tytuł strony.
     *
     * @var string
     */
    public static function title($title = false)
    {
        if ($title) {
            self::$title = $title;
        } else {
            if (false !== $title) {
                self::$title = '';
            } else {
                if ((isset(self::$meta['home_title']) && '' != self::$meta['home_title'])) {
                    return self::$meta['home_title'];
                }
                if ((isset(self::$meta['title']) && '' != self::$meta['title'])) {
                    return self::$meta['title'];
                }

                return self::$title;
            }
        }
    }

    /**
     * Funkcja ustawia i zwraca tytuł strony.
     *
     * @var string
     */
    public static function metaTitle()
    {
        if (self::$metaTitle === false) {
            $title = self::title();
        } else {
            $title = self::$metaTitle;
        }
        return $title.self::$extTitle;
    }
    
    /**
     * Funkcja ustawia title w head stony
     * W przypadku jego braku zostanie pobrany tytył z contentu
     *
     * @var unknown_type
     */
    public static function setMetaTitle($title)
    {
        self::$metaTitle = $title;
    }

    public static function setExtTitle($title)
    {
        self::$extTitle = $title;
    }

    public static function description($description)
    {
        self::$description = $description;
    }

    public static function setSecondDescription($description)
    {
        self::$secondDescription = $description;
    }

    /**
     * Funkcja ustawia i zwraca aktualną skórkę.
     *
     * @param unknown_type $theme
     */
    public static function theme($theme = false)
    {
        if ($theme) {
            self::$theme = $theme;
        } else {
            return self::$theme;
        }
    }

    public static function isTheme($theme = false)
    {
        if (!$theme) {
            $theme = self::$theme;
        }
        $path = qConfig::get('path.theme.'.$theme, false);
        if (!$path) {
            $path = qConfig::get('path.theme').DS.$theme;

            return file_exists($path);
        }

        return true;
    }

    /**
     * Funkcja ustawia i zwraca aktualną plik tpl.
     *
     * @param unknown_type $layoutTpl
     */
    public static function tpl($layoutTpl = false)
    {
        if ($layoutTpl) {
            self::$layoutTpl = $layoutTpl;
        } else {
            return self::$layoutTpl;
        }
    }

    public static function render() {
        $layoutTpl = self::$layoutTpl;
        self::setting('token', md5(rand().time()));
        $tempalte = new qTemplate();
        foreach (self::$fields as $key => $value) {
            $tempalte->set($key, $value);
        }
        $result = $tempalte->render($layoutTpl, false);
        $result = $tempalte->strRender($result);
        return $result;
    }

    public static function pathThemeCore() {
        $path = qConfig::get('path.base').DS.'core'.DS.'theme';
        return $path;
    }

    public static function urlThemeCore() {
        $path = qConfig::get('url.base').'/'.'core'.'/'.'theme';
        return $path;
    }
    
    public static function pathThemeVolume() {
        $path = qConfig::get('path.base').DS.'volume'.DS.qConfig::get('volume').DS.'theme';
        return $path;
    }

    public static function urlThemeVolume() {
        $path = qConfig::get('url.base').'/'.'volume'.'/'.qConfig::get('volume').'/'.'theme';
        return $path;
    }

    public static function set($key, $value)
    {
        self::$fields[$key] = $value;
    }

    public static function add($key, $value)
    {
        if (!isset(self::$fields[$key])) {
            self::$fields[$key] = '';
        }
        self::$fields[$key] .= $value;
    }
    
    public static function has($key) {
        return isset(self::$fields[$key]);
    }

    public static function get($key, $default = '')
    {
        if (isset(self::$fields[$key])) {
            return self::$fields[$key];
        }

        return $default;
    }

    public static function getAll() {
        return self::$fields;
    }

    /**
     * @author G Kasperek
     *
     * @param type $module
     * @param type $file
     *
     * @return path too file css
     */
    public static function cssSearch($module, $file)
    {
        $pathToTpl = qConfig::get('path.module');
        $subPath = DS.Module::moduleSubPath($module).DS.'css'.DS.$file;
        $pathToTpl .= $subPath;
        if (file_exists($pathToTpl)) {
            return self::usedAddCss($module, $module.'/css/'.$file, '<strong style="color:#000;">module</strong>'.$subPath);
        }
    }

    public static function classes()
    {
        $module = qCtrl::module();
        $action = qCtrl::action();
        $tab = array_merge([$module, $action], qCtrl::args());
        $tResult = array();
        $len = count($tab);
        for ($i = 1; $i <= $len; ++$i) {
            $item = '';
            for ($ii = 1; $ii <= $i; ++$ii) {
                if ('' != $item) {
                    $item .= '-';
                }
                $item .= $tab[$ii - 1];
            }
            $tResult[] = $item;
        }
        $tResult[] = 'anonymous';
        $tResult[] = 'complex-'.qConfig::get('volume');
        $result = '';
        foreach ($tResult as $ii => $class) {
            if ('' != $result) {
                $result .= ' ';
            }
            $result .= $class;
        }
        return $result;
    }
}
qLayout::meta('author', 'Krzysztof Wałek');
qLayout::meta('generator', 'qAnt/phpBook');
qLayout::meta('robots', 'index, follow');
