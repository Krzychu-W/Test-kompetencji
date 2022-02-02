<?php

class qCtrl {
    public static $module;
    public static $action;
    public static $subAction;
    public static $args;
    public static $allArgs;
    public static $redir;
    public static $lang;
    public static $page = 1;
    public static $pagelimit = false;
    public static $pagedefaultlimit = false;
    public static $sortName = false;
    public static $sortNameArr = array();
    public static $sortDir = 'asc';
    public static $sortDirArr = array();
    public static $original;
    public static $oryginalNoLang;
    public static $emptyPage = true;
    public static $contentLang;
    public static $contentMix = false;
    public static $destination = false;
    
    public static $method;
    public static $paramGet;
    public static $paramPost;

    /**
     * Inicjuje zmienne kontrolne.
     *
     * @param string|array $cfg Zmienne kontrolne (=array())
     *
     * @return bool
     */
    public static function init($cfg = array())
    {
        if (is_string($cfg)) {
            $cfg = explode('/', $cfg);
        }
        if (0 == strlen(end($cfg))) {
            unset($cfg[sizeof($cfg) - 1]);
        }
        $oryginalNoLang = $cfg;
        if (!empty($oryginalNoLang)) {
            if (2 == strlen($oryginalNoLang[0])) {
                array_shift($oryginalNoLang);
            }
        }

        self::$oryginalNoLang = implode('/', $oryginalNoLang);
        self::$original = implode('/', $cfg);
        self::$module = '';
        self::$action = '';
        self::$redir = '';
        self::$args = [];
        self::$allArgs = [];
        $i = 0;
        $nrArg = 0;
        // wycięcie jezyka kontekstoweg (lang)
        if (isset($cfg[0])) {

            if (!self::$lang) {
                self::$lang = 'pl';
            }

        }

        self::$contentLang = self::$lang;
        // obsluga wyjatków
        if (0 == count($cfg) || (1 == count($cfg) && '' == $cfg[0]) || (2 == count($cfg) && '' == $cfg[0] && '' == $cfg[1])) {
            // frontpage
            $cfg = array('index', 'index');
        } elseif (1 == count($cfg)) {
            // domyslna akcja
            $cfg[] = 'index';
        }
        if (1 == count($cfg)) {
            $cfg[] = 'index';
        // default action
        } elseif (0 == count($cfg)) {
            // frontpage
            $cfg[] = 'index';
            $cfg[] = 'index';
        }
        $page = qConfig::get('page', 'page-');
        $pageLen = strlen($page);
        $sort = qConfig::get('sort', 'sort-');
        $sortLen = strlen($sort);
        $contentLang = qConfig::get('contentLang', 'lang-');
        $contentLangLen = strlen($contentLang);
        foreach ($cfg as $value) {
            if ($i > 1) {
                // wyszukanie znacznika specjalnego page-
                if (substr($value, 0, $pageLen) == $page) {
                    self::$emptyPage = false;
                    $exp = explode('-', substr($value, $pageLen));
                    if (isset($exp[0])) {
                        self::$page = $exp[0];
                    }
                    if (isset($exp[1])) {
                        self::$pagelimit = $exp[1];
                    }
                } elseif (substr($value, 0, $sortLen) == $sort) {
                    // wyszukanie znacznika specjalnego sort-
                    list($sortName, $direction) = explode('-', substr($value, $sortLen));
                    if (isset($sortName)) {
                        self::$sortNameArr = explode('.', $sortName);
                        self::$sortName = current(self::$sortNameArr);
                    }
                    if (isset($direction)) {
                        $sortDirArr = explode('.', $direction);
                        $sortDirArrNew = array();
                        foreach ($sortDirArr as $sortDir) {
                            if ('asc' == strtolower($sortDir)) {
                                $sortDirArrNew[] = 'asc';
                                $sortDir = 'asc';
                            } else {
                                $sortDirArrNew[] = 'desc';
                                $sortDir = 'desc';
                            }
                        }
                        self::$sortDirArr = $sortDirArr;
                        self::$sortDir = $sortDir;
                    }
                    /*$sorts = explode('-', substr($value, $sortLen));
                    foreach($sorts as $s){
                      list($sortName,$direction) = explode(',', $s);
                      self::$sortNameArr[] =
                    }*/
                } elseif (substr($value, 0, $contentLangLen) == $contentLang) {
                    // wyszukanie znacznika specjalnego lang-
                    $cLang = substr($value, $contentLangLen);
                    if ('mix' == $cLang) {
                        self::$contentMix = true;
                    } else {
                        self::$contentLang = substr($value, $contentLangLen);
                    }
                } else {
                    self::$args[$nrArg] = $value;
                    ++$nrArg;
                    self::$allArgs[] = $value;
                }
            } elseif (0 == $i) {
                self::$module = $value;
                self::$allArgs[] = $value;
            } elseif (1 == $i) {
                self::$action = $value;
                self::$allArgs[] = $value;
            }
            ++$i;
        }
        self::$destination = qCtrl::get('destination', false);
        return true;
    }

    /**
     * Zwraca wartosc parametru
     * Parametry liczone sa od liczby 0.
     *
     * @param int    $key     Klucz
     * @param string $default Wartosc defaultowa (='')
     *
     * @return string Wartosc parametru
     */
    public static function arg($key, $default = '')
    {
        if (isset(self::$args[$key])) {
            return self::$args[$key];
        }

        return $default;
    }

    /**
     * Zwraca wartosc parametru parsując do integer
     * Parametry liczone sa od liczby 0.
     *
     * @param int    $key     Klucz
     * @param string $default Wartosc defaultowa == 0
     *
     * @return string Wartosc parametru
     */
    public static function argToInt($key, $default = 0)
    {
        return intval(self::arg($key, $default));
    }

    /**
     * Zwraca parametry.
     *
     * @return array Tablica parametrów
     */
    public static function args()
    {
        return self::$args;
    }
    
    public static function allArgs()
    {
        return self::$allArgs;
    }

    /**
     * Zwraca wartosc parametru w stylu Zend.
     *
     * @param string $name    Nazwa parametru
     * @param string $default Wartosc domyslna parametru (='')
     *
     * @return string Wartosc parametru
     */
    public static function argNext($name, $default = '')
    {
        $next = false;
        foreach (self::$args as $arg) {
            if ($next) {
                return $arg;
            } elseif ($name == $arg) {
                $next = true;
            }
        }

        return $default;
    }

    /**
     * Zwraca wartosc parametru w stylu Zend parsowane do integer.
     *
     * @param string $name    Nazwa parametru
     * @param string $default Wartosc domyslna parametru == 0
     *
     * @return string Wartosc parametru
     */
    public static function argNextToInt($name, $default = 0)
    {
        $next = false;
        foreach (self::$args as $arg) {
            if ($next) {
                return $arg;
            } elseif ($name == $arg) {
                $next = true;
            }
        }

        return $default;
    }

    /**
     * Ustawia wartosc parametru.
     *
     * @param int   $key   Klucz
     * @param mixed $value Wartosc
     */
    public static function setArg($key, $value)
    {
        self::$args[$key] = $value;
    }

    /**
     * Ustawia wiele wartosci parametrów.
     *
     * @param array $args Tablica parametrów (=array())
     */
    public static function setArgs($args = array())
    {
        self::$args = $args;
    }

    /**
     * Zwraca lub ustawia modul.
     *
     * @param mixed $module Modul (='')
     *
     * @return mixed Modul
     */
    public static function module($module = '')
    {
        if ('' == $module) {
            return self::$module;
        }
        self::$module = $module;
    }

    /**
     * Zwraca lub ustawia akcje.
     *
     * @param mixed $action Akcja (='')
     *
     * @return mixed Akcja
     */
    public static function action($action = '')
    {
        if ('' == $action) {
            return self::$action;
        }
        self::$action = $action;
    }

    /**
     * Return or get subaction.
     *
     * @param string $subAction Akcja (='')
     *
     * @author Krzysztof Wałek
     *
     * @return string|void
     */
    public static function subAction($subAction = '')
    {
        if ('' == $subAction) {
            return self::$subAction;
        }
        self::$subAction = $subAction;
    }

    public static function moduleAction($moduleAction = '')
    {
        if ('' == $moduleAction) {
            return self::$module.'/'.self::$action;
        }
        list($module, $action) = explode('/', $moduleAction);
        self::$module = $module;
        self::$action = $action;

        return null;
    }

    public static function ifModule($module)
    {
        return self::$module == $module;
    }

    public static function ifAction($action)
    {
        return self::$action == $action;
    }

    public static function ifModuleAction($moduleAction)
    {
        return self::$module.'/'.self::$action == $moduleAction;
    }

    public static function isHomepage()
    {
        if ('index' === self::$module && 'home' === self::$action) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Zwraca lub ustawia redir.
     *
     * @param mixed $redir Redir (=NULL)
     *
     * @return mixed Redir
     */
    public static function redir($redir = null)
    {
        if (null === $redir) {
            return self::$redir;
        }
        self::$redir = $redir;
    }

    /**
     * Zwraca lub ustawia lang.
     *
     * @param mixed $lang Lang (=NULL)
     *
     * @return mixed Lang
     */
    public static function lang($lang = null)
    {
        if (null === $lang) {
            return self::$lang;
        }
        self::$lang = $lang;
    }

    /**
     * Zwraca lub ustawia contentLang.
     *
     * @param mixed $lang Lang (=NULL)
     *
     * @return mixed Lang
     */
    public static function cLang($lang = null)
    {
        if (null === $lang) {
            return self::$contentLang;
        }
        self::$contentLang = $lang;
    }

    /**
     * Generuje link języka kontentu.
     *
     * @param string $lang
     *
     * @return string
     */
    public static function cLangGenerate($lang)
    {
        return '/'.qConfig::get('contentLang', 'lang-').$lang;
    }

    /**
     * Tryb specjalny und
     * Oznacza, że content ma być pobierany dla wszystich języków.
     *
     * @return string
     */
    public static function cLangMix()
    {
        return self::$contentMix;
    }

    /**
     * Zwraca page.
     *
     * @return mixed Page
     */
    public static function page()
    {
        return self::$page;
    }

    /*
     * Zwraca emptyPage - informacja, czy znaleziono znacznik specjalny page- w adresie
     * @return boolean Page
     */
    public static function emptyPage()
    {
        return self::$emptyPage;
    }

    public static function rawPageLimit()
    {
        return self::$pagelimit;
    }

    /**
     * Zwraca lub ustawia pagelimit.
     *
     * @param mixed $default PageLimit (=false)
     *
     * @return mixed PageLimit
     */
    public static function pageLimit($default = false)
    {
        if (self::$pagelimit) {
            return self::$pagelimit;
        } elseif (self::$pagedefaultlimit) {
            return self::$pagedefaultlimit;
        }

        return $default;
    }

    public static function defLimit($limit = false)
    {
        if (false === $limit) {
            return self::$pagedefaultlimit;
        }
        self::$pagedefaultlimit = $limit;
    }

    /**
     * Zwraca nazwe kolumny po której sortowana jest tabela.
     *
     * @return string sortName
     */
    public static function sortName($array = false)
    {
        if ($array) {
            return self::$sortNameArr;
        } else {
            return self::$sortName;
        }
    }

    /**
     * Zwraca lub ustawia kierunek sortowania.
     *
     * @return string sortDir
     */
    public static function sortDir($array = false)
    {
        if ($array) {
            return self::$sortDirArr;
        } else {
            return self::$sortDir;
        }
    }

    /**
     * Zwraca original.
     *
     * @return mixed Original
     */
    public static function original()
    {
        return self::$original;
    }

    public static function originalNoPage()
    {
        $ex = explode('/', self::$original);
        foreach ($ex as $k => $v) {
            if (substr($v, 0, 5) == qConfig::get('page')) {
                unset($ex[$k]);
            }
        }

        return implode('/', $ex);
    }

    public static function oryginal($lang = true)
    {
        if ($lang) {
            return self::original();
        } else {
            return self::$oryginalNoLang;
        }
    }

    public static function oryginalNoLang()
    {
        return self::$oryginalNoLang;
    }

    public static function oryginalNoPageNoLang()
    {
        $ex = explode('/', self::$oryginalNoLang);
        foreach ($ex as $k => $v) {
            if (substr($v, 0, 5) == qConfig::get('page')) {
                unset($ex[$k]);
            }
        }

        return implode('/', $ex);
    }

    public static function oryginalNoAjax()
    {
        $str = self::$oryginalNoLang;
        if ('ajax/json/' == substr($str, 0, 10)) {
            $str = substr($str, 10);
        }

        return $str;
    }

    /**
     * Zwraca klase aktualnej akcji.
     *
     * @return string Nazwa klasy
     */
    public static function classAction()
    {
        return ucfirst(self::module()).'Action'.ucfirst(self::action());
    }

    /**
     * Przekierowywuje na podany adres.
     *
     * @param string $url    URL qend
     * @param array  $params Parametry
     */
    public static function location($url, $params = [])
    {
        $link = qHref::url($url);
        $get = '';
        foreach ($params as $key => $value) {
            if ('' === $get) {
                $get = '?';
            } else {
                $get .= '&';
            }
            $get .= $key.'='.$value;
        }
        header('Location: '.$link.$get);
        exit;
    }

    /**
     * @author Paweł Rychter
     * Przekierowywuje na podany adres
     *
     * @param string $qend   URL qend
     * @param array  $params Parametry
     */
    public static function locationPost($qend, $params = [])
    {
        if(substr($qend, 0, 7) === 'http://' || substr($qend, 0, 8) === 'https://') {
            $link = $qend;
        } else {
            $link = qHref::url($qend);
        }
        if (count($params) > 0) {
            $html = '<html xmlns="http://www.w3.org/1999/xhtml"><head>';
            $html .= '<script type="text/javascript">function submitOnLoad(){document.forms["postredirect"].submit();}</script>';
            $html .= '</head><body onload="submitOnLoad();"><form name="postredirect" method="post" action="'.$link.'">';
            foreach ($params as $key => $value) {
                $html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"> ';
            }
            $html .= '</form></body></html>';
            echo $html;
            exit;
        } else {
            location($qend);
        }
    }

    /**
     * Przekierowywuje na podany adres dodając destination.
     *
     * @param string $url wewnętry url
     */
    public static function locationAddDestination($url)
    {
        $link = qHref::url($url);
        header('Location: '.$link.self::addDestination());
        exit;
    }

    /**
     * Przekierowywuje na podany adres.
     *
     * @param string $link URL
     */
    public static function header($link)
    {
        header('Location: '.$link);
        exit;
    }

    public static function headerPost($link, $params = array())
    {
        $str = '';
        foreach ($params as $key => $value) {
            if ('' != $str) {
                $str .= '&';
            }
            $str .= $key.'='.$value;
        }
        header('Location: '.$link.'?'.$str);
        exit;
        //header("method: POST\r\n");
        //header("Content-Type: application/x-www-form-urlencoded\r\n");
        //header("Content-Length: ".strlen($str)."\r\n");
        //header($str."\r\n\r\n");
        //header("Connection: close\r\n\r\n");
        //header("Location: ".$link."\r\n");
        header("POST /paygw//UTF/NewPayment HTTP/1.1\r\n");
        header('Host: www.platnosci.pl');
        //header("Content-Type: application/x-www-form-urlencoded\r\n");
        header('Content-Length: '.strlen($str)."\r\n");
        header($str."\r\n\r\n");
        //header("Location: ".$link."\r\n");
        exit;
    }

    public static function currentUrl($sorted = false, $paged = false)
    {
        $sort = qConfig::get('sort', 'sort-');
        $sortLen = strlen($sort);
        $page = qConfig::get('page', 'page-');
        $pageLen = strlen($page);
        $args = array();
        foreach (self::$args as $key => $arg) {
            if ((substr($arg, 0, $sortLen) != $sort) || (substr($arg, 0, $pageLen) != $page)) {
                $args[] = $arg;
            }
        }
        $sort_str = '';
        if (false !== $sorted && strlen(self::sortName()) > 0) {
            $sortName = implode('.', self::sortName(true));
            $sortDir = implode('.', self::sortDir(true));
            $sort_str = $sort.$sortName.'-'.$sortDir.'/';
        }
        $page_str = ''; // ^^^
        if (false !== $paged) {
            $page_str = $page.self::page().'-'.self::pageLimit().'/';
        }
        $args_str = implode('/', $args);
        $args_str .= '/';
        $path = self::module().'/'.self::action();
        if (self::subAction()) {
            $path .= '/'.self::subAction();
        }
        $path .= '/'.$args_str.$sort_str.$page_str;
        $path = implode('/', array_filter(explode('/', $path)));
        $full = qConfig::get('domain.type').'://'.qConfig::get('domain.domain');
        if ('://' === trim($full)) {
            $full = '';
        }
        $url = $full.'/'.$path;

        return $url;
    }

    public static function browserLang()
    {
        return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    public static function actionMethod($module = false, $action = false)
    {
        if (false === $module) {
            $module = self::module();
        }
        if (false === $action) {
            $action = self::action();
        }



        $actionObjName = ucfirst($module).'Action'.ucfirst($action);
        $actionMethodName = 'Action';
        if (qLoader::isClass($actionObjName) === false) {
            $actionObjName = ucfirst($module).'ActionIndex';
            $actionMethodName = $action.'Action';
            if (qLoader::isClass($actionObjName) === false) {
                return false;
            }
            $obj = new $actionObjName();
            if (!method_exists($obj, $actionMethodName)) {
                return false;
            }
        } else {
            $obj = new $actionObjName();
        }
        $act = new stdClass();
        $act->obj = $obj;
        $act->method = $actionMethodName;

        return $act;
    }



    public static function isUrl()
    {
        $args = func_get_args();
        $lp = -2;
        foreach ($args as $str) {
            if ($lp == -2) {
                if (self::$module != $str) {
                    return false;
                }
            } elseif ($lp == -1) {
                if (self::$action != $str) {
                    return false;
                }
            } else {
                if (count(self::$args) > $lp) {
                    if (self::arg($lp) != $str) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            ++$lp;
        }

        return true;
    }

    /**
     * Wycine z oryginalej ścieżki:
     * 'lang' - język kontekstowy
     * 'cLang' - język kontentu
     * 'page' - stronicowanie
     * 'sort' - sortowanie
     * 'ajax' - ajaxa.
     *
     * @return string
     */
    public static function cutOryginal()
    {
        $ex = explode('/', self::$original);
        $ajax = false;
        foreach (func_get_args() as $type) {
            if ('lang' == $type) {
                if (2 == strlen($ex[0])) {
                    unset($ex[0]);
                }
            } elseif ('cLang' == $type) {
                $contentLang = qConfig::get('contentLang', 'lang-');
                $contentLangLen = strlen($contentLang);
                foreach ($ex as $k => $v) {
                    if (substr($v, 0, $contentLangLen) == $contentLang) {
                        unset($ex[$k]);
                    }
                }
            } elseif ('page' == $type) {
                $page = qConfig::get('page', 'page-');
                $pageLen = strlen($page);
                foreach ($ex as $k => $v) {
                    if (substr($v, 0, $pageLen) == $page) {
                        unset($ex[$k]);
                    }
                }
            } elseif ('sort' == $type) {
                $sort = qConfig::get('sort', 'sort-');
                $sortLen = strlen($sort);
                foreach ($ex as $k => $v) {
                    if (substr($v, 0, $sortLen) == $sort) {
                        unset($ex[$k]);
                    }
                }
            } elseif ('ajax' == $type) {
                $ajax = true;
            }
        }
        $path = implode('/', $ex);
        if ($ajax) {
            if ('ajax/json/' == substr($path, 0, 10)) {
                $path = substr($path, 10);
            }
        }

        return $path;
    }

    /**
     * Pobiera lub ustawia nowy destination.
     *
     * @param mixed $destination
     *
     * @return mixed
     */
    public static function destination($destination = null)
    {
        if (null === $destination) {
            return self::$destination;
        }
        self::$destination = $destination;
    }

    /**
     * Zwraca dodatek do adresu z obecnym destination.
     *
     * @return string
     */
    public static function addDestination()
    {
        if (false === self::$destination) {
            return '';
        }

        return '?destination='.self::$destination;
    }

    /**
     * Zwraca destination w postaci tablicy.
     *
     * @return array
     */
    public static function paramsDestination()
    {
        $result = [];
        if (self::$destination) {
            $result['destination'] = self::$destination;
        }

        return $result;
    }
    
    public static function baseUrl() {
        $url  = @(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != 'on') ? 'http://'.$_SERVER["SERVER_NAME"] : 'https://'.$_SERVER["SERVER_NAME"];
        $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
        return $url;
    }
    
    /**
     * Zwraca rodzaj metody.
     *
     * @return string Metoda
     */
    public static function method()
    {
        return self::$method;
    }

    /**
     * Sprawdza czy metoda to POST.
     *
     * @return bool Wynik
     */
    public static function isPost()
    {
        return 'post' == self::$method;
    }

    /**
     * Sprawdza czy metoda to GET.
     *
     * @return bool Wynik
     */
    public static function isGet()
    {
        return 'get' == self::$method;
    }

    /**
     * Pobranie parametów GET.
     *
     * @param mixed $key     false pobiera tablicę ze wszstkimi parametrami GET,
     *                       w innym wypadku pudać string z nazwą klucza
     * @param mixed $default - domyślna wartośc parametry w przypadku braku
     *
     * @return mixed wszystkie parametry lub skazany, w wypadku braku zwarace default
     */
    public static function get($key = false, $default = null)
    {
        if (false === $key) {
            return self::$paramGet->getItems();
        }

        return self::$paramGet->item($key, $default);
    }

    /**
     * Pobranie parametów GET.
     *
     * @param mixed $key     false pobiera tablicę ze wszstkimi parametrami GET,
     *                       w innym wypadku pudać string z nazwą klucza
     * @param mixed $default - domyślna wartośc parametry w przypadku braku
     *
     * @return mixed wszystkie parametry lub skazany, w wypadku braku zwarace default
     */
    public static function post($key = false, $default = null)
    {
        if (false === $key) {
            return self::$paramPost->getItems();
        }

        return self::$paramPost->item($key, $default);
    }

    /**
     * Ustawia parametr.
     *
     * @param mixed $key   Klucz
     * @param mixed $value Wartość
     */
    public static function setItem($key, $value)
    {
        if ('post' == self::$method) {
            self::$paramPost->setItem($key, $value);
        } else {
            self::$paramGet->setItem($key, $value);
        }
    }

    /**
     * Ustawia parametr.
     *
     * @param array $array Tablica parametrów
     */
    public static function setItems($array)
    {
        if ('post' == self::$method) {
            self::$paramPost = new qItems($array);
        } else {
            self::$paramGet = new qItems($array);
        }
    }

    /**
     * @deprecated zamienione na get() lub post()
     */
    public static function item($key, $default = null)
    {
        if ('post' == self::$method) {
            return self::post($key, $default);
        } else {
            return self::get($key, $default);
        }
    }

    /**
     * @deprecated zamienione na get()
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function itemGet($key, $default = null)
    {
        return self::get($key, $default);
    }

    /**
     * Zwraca tablicę.
     *
     * @param mixed $key Klucz
     *
     * @return array Tablica
     */
    public static function itemArray($key)
    {
        if ('post' == self::$method) {
            return self::post($key, array());
        } else {
            return self::get($key, array());
        }
    }

    /**
     * @deprecated zamienione na get() lub post()
     */
    public static function items()
    {
        if ('post' == self::$method) {
            return self::post();
        } else {
            return self::get();
        }
    }

    /**
     * @deprecated zamienione na get() lub post()
     */
    public static function itemsGet()
    {
        return self::get();
    }

    public static function unprepare($tab)
    {
        $res = array();
        foreach ($tab as $key => $value) {
            if (is_array($value)) {
                $res[$key] = self::unprepare($value);
            } else {
                $chars = array("'", '"', '\\\\');
                foreach ($chars as $char) {
                    $value = preg_replace('/(\\\\+)'.$char.'/i', $char, $value);
                }
                //$value = str_replace("\\", "\\\\", $value); // Double slashes? Why?
                $res[$key] = $value;
            }
        }

        return $res;
    }
    
    
    public static function filter_input($type, $variable_name, $filter = FILTER_DEFAULT, $options = NULL ) {
        $checkTypes =[INPUT_GET, INPUT_POST, INPUT_COOKIE];
        if ($options === NULL) {
            $options = FILTER_NULL_ON_FAILURE;
        }
        if (in_array($type, $checkTypes) || filter_has_var($type, $variable_name)) {
            return filter_input($type, $variable_name, $filter, $options);
        } 
        else if ($type == INPUT_SERVER && isset($_SERVER[$variable_name])) {
            return filter_var($_SERVER[$variable_name], $filter, $options);
        } 
        else if ($type == INPUT_ENV && isset($_ENV[$variable_name])) {
            return filter_var($_ENV[$variable_name], $filter, $options);
        } 
        else {
            return NULL;
        }
    }
    
    public static function calledClass(){
        $trace = debug_backtrace();
        $count = count($trace)-1;
        for ($i = 2; $i < $count; $i++) {
          if (isset($trace[$i]['class'])) {
            return $trace[$i]['class'];
          }
        }
        return false;
    }
    
    public static function initParam() {
        // init
        if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json' ) {
            self::$paramPost = new qItems(json_decode(file_get_contents('php://input'), true));
        }
        else {
            self::$paramPost = new qItems(self::unprepare($_POST)); 
        }
        self::$paramGet = new qItems($_GET);
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            self::$method = 'post';
        } 
        else {
            self::$method = 'get';
        }

    }
}

