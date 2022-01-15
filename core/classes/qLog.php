<?php
/**
 * Logowanie do pliku
 * 
 * @author Krzysztof Wałek
 *
 */
class qLog {
    
    /** @var string Folder do zapisu */
    public $_folderlog;
    /** @var string Tryb logera */
    public $_tryb;
    /** @var array Zawartosc do wyświetlenia na ekran */
    public $_screen;
    
    const ADDRESS_ERROR = 'error@investmag.eu';
    const ADDRESS_DEVELOPER = 'developer@investmag.eu';

    /** @var object Logger */
    public static $instance;

    /** Inicjacja logera */
    public static function init()
    {
        if (!self::$instance) {
            self::$instance = new qLog(qConfig::get('path.log'));
        }
    }

    /**
     * Zwraca instancje Logger.
     *
     * @return object Logger
     */
    public static function getInstance() {
        return self::$instance;
    }

    /**
     * Funkcja ustawia wyświetlanie Xdebug, Podanie bez parametrów ustawia domyślne wartość, Ustawienie bez limitu: -1.
     *
     * @param integer $depth
     * @param integer $children
     * @param integer $data
     */
    public static function setXdebug($depth = 3, $children = 128, $data = 512) {
        ini_set('xdebug.var_display_max_depth', $depth);
        ini_set('xdebug.var_display_max_children', $children);
        ini_set('xdebug.var_display_max_data', $data);
    }

    /**
     * Zapisuje dowolna ilosc zmiennych do wpliku.
     *
     * @param mixed $txt Zmienne do zapisania
     */
    public static function write($txt) {
        $params = func_get_args();
        array_unshift($params, 'qend');
        self::$instance->logArray($params);
    }

    public static function save($fileName, $txt) {
        self::$instance->saveFile($fileName, $txt);
    }

    /**
     * Wypisuje dowolna ilosc zmiennych na ekran.
     *
     * @param mixed $txt Zmienne do wyswietlenia
     */
    public static function screen($txt) {
        $params = func_get_args();
        array_unshift($params, 'screen');
        self::$instance->logArray($params);
    }

    /**
     * Wypisuje dowolna ilosc zmiennych na do logu error.
     *
     * @param mixed $txt Zmienne do wyswietlenia
     */
    public static function error($txt) {
        $params = func_get_args();
        array_unshift($params, 'error');
        self::$instance->logArray($params);
    }

    /**
     * Wypisuje dowolna ilosc zmiennych na do logu error i wysyła na mail errors@clouder.com.pl.
     *
     * @param mixed $txt Zmienne do wyswietlenia
     */
    public static function fatal($txt) {
        $params = func_get_args();
        array_unshift($params, 'fatal');
        self::$instance->logArray($params);
    }

    /**
     * wysyła dowolna ilosc zmiennych na na mail developer@clouder.com.pl.
     *
     * @param mixed $txt Zmienne do wyswietlenia
     */
    public static function developer($subject, $txt) {
        $params = func_get_args();
        array_unshift($params, 'developer');
        self::$instance->logArray($params);
    }

    /**
     * wysyła dowolna ilosc zmiennych na na mail podany jako pierwszy parametr.
     *
     * @param string $mail
     * @param string $subject
     * @param mixed  $txt     - dowolna ilość
     */
    public static function mail($mail, $subject, $txt) {
        $params = func_get_args();
        array_unshift($params, 'mail');
        self::$instance->logArray($params);
    }

    /**
     * Return screen log array.
     *
     * @author Rafał Łempa
     *
     * @return array Screen logs
     */
    public static function getScreen()
    {
        if (count(self::$instance->_screen) > 0) {
            return self::$instance->_screen;
        }

        return false;
    }

    /**
     * Pozwala oznaczyc funkcje jako przestarzala w logach.
     */
    public static function obsolete() {
        $paramsnew = array();
        $paramsnew[] = 'qend';
        $paramsnew[] = 'FUNKCJA PRZESTARZALA';
        self::$instance->logArray($paramsnew);
    }

    /**
     * Funkcja przestarzala.
     *
     * @todo Metoda do usuniecia (nie uzywana)
     *
     * @deprecated
     */
    public static function logAjax($txt) {
        $paramas = func_get_args();
        $paramsnew = array();
        $paramsnew[] = 'ajax';
        foreach ($paramas as $value) {
            $paramsnew[] = $value;
        }
        self::$instance->logArray($paramsnew);
    }
    

    /**
     * Konstruktor.
     *
     * @param string $aFolderLog Folder do zapisu
     * @param string $aTryb      Tryb działania logera
     */
    public function __construct($aFolderLog = '', $aTryb = '') {
        
        $this->_folderlog = $aFolderLog;
        if ('' != $this->_folderlog) {
            if (!is_dir($aFolderLog)) {
                @mkdir($aFolderLog, 0755, true);
            }
            $this->_folderlog .= '/';
        }
        $this->_tryb = $aTryb;
        $this->_screen = array();
    }

    /**
     * Zwraca tryb.
     *
     * @return string Tryb działania logera
     */
    public function tryb()
    {
        return $this->_tryb;
    }

    /**
     * Alias do logArray.
     *
     * @internal
     *
     * @param mixed $txt Zawartość
     */
    public function addLog($txt)
    {
        $tab = func_get_args();
        $this->logArray($tab);
    }

    /**
     * Parsuje dane do zapisu.
     *
     * @internal
     *
     * @param array $tab       Tablica z danymi
     * @param bool  $wywolanie Czy zostało wywołane (=false)
     */
    public function logArray($tab, $wywolanie = false) {
        if (false === $wywolanie) {
            $wywolanie = debug_backtrace();
        }
        $mail = false;
        $suffixSubject = '';
        $file = qConfig::get('volume');
        if (!$file) {
            $file = 'qend';
        }
        if (count($tab) > 0) {
            $xFile = $tab[0];
            if ('qend' == $xFile) {
                unset($tab[0]);
            } elseif ('error' == $xFile) {
                $file = 'error';
                unset($tab[0]);
            } elseif ('ajax' == $xFile) {
                $file = 'ajax';
                unset($tab[0]);
            } elseif ('debug' == $xFile) {
                $file = 'qend';
                unset($tab[0]);
            } elseif ('screen' == $xFile) {
                $file = 'screen';
                unset($tab[0]);
            } elseif ('screen_plain' == $xFile) {
                $file = 'screen_plain';
                unset($tab[0]);
            } elseif ('fatal' == $xFile) {
                $file = 'error';
                $suffixSubject = 'Clouder Fatal Error: ';
                unset($tab[0]);
                $mail = trim(\qSetting::getUnd('log.fatal-mail', qLog::ADDRESS_ERROR));
            } elseif ('developer' == $xFile) {
                $file = false;
                unset($tab[0]);
                $mail = trim(\qSetting::getUnd('log.developer-mail', qLog::ADDRESS_DEVELOPER));
            } elseif ('mail' == $xFile) {
                $file = false;
                $mail = $tab[1];
                unset($tab[0]);
                unset($tab[1]);
            }
        }
        $str = '#--- '.date('Y-m-d H:i:s').' ----- '.$_SERVER['REMOTE_ADDR'].' ------------- '.random_int(100000, 999999).' -------------------'."\n";
        if (!('screen' == $file)) {
            $str .= qDebug::getInstance()->formatLog($wywolanie);
        }
        $html = str_replace("\n", '<br />', $str);
        $value = '';
        $subject = false;
        $i = 1;
        foreach ($tab as $val) {
            if ($mail && false === $subject && is_string($val) && strlen($val) <= 50) {
                $subject = $val;
                $value .= 'TEMAT:: '.$subject."\n";
            } else {
                if ($mail && false === $subject) {
                    $subject = '';
                }
                if (is_string($val)) {
                    $xHtml = $val;
                    if ($mail) {
                        $html .= '#'.$i.':'.$xHtml.'<br />';
                    }
                    $xValue = str_replace('=&gt;', '=>', $xHtml);
                } else {
                    
                    //ini_set('xdebug.overload_var_dump', 'off');
                    $xHtml = var_export($val, TRUE);
                    if ($mail) {
                        $html .= '#'.$i.':'.$xHtml.'<br />';
                    }
                    $xValue = $xHtml;
                }
                $value .= '#'.$i.':'.$xValue."\n";
                ++$i;
            }
        }
        if (false !== $file) {
            if ('screen' == $file) {
                $this->_screen[] = array('induction' => $str, 'value' => $value);
            } else {
                $this->addFile($this->_folderlog.$file.'_'.date('Y_m_d').'.log', $str.$value);
            }
        }
        if ($mail) {
            $subject = $suffixSubject.$subject.' ('.qConfig::getSys('domain').')';
            $mailer = new qMailer();
            $mailer->addAddress($mail);
            $mailer->setSubject($subject);
            $mailer->addBodyHtml($html);
            $mailer->send();
        }
    }

    /**
     * Parsuje dane do zapisu.
     *
     * @internal
     *
     * @param array $tab Tablica z danymi
     */
    public function logArrayFromObj($tab)
    {
        $str = '#--- '.date('Y-m-d H:i:s').' ----- '.$_SERVER['REMOTE_ADDR'].' ---------------------------------------'."\n";
        foreach ($tab as $val) {
            $str .= $this->toStr($val)."\n";
        }
        $this->addFile($this->_folderlog.'qend_'.date('Y_m_d').'.log', $str);
    }

    /**
     * Zapisuje dane w trybie debug.
     *
     * @internal
     *
     * @param mixed $txt Zawartość
     */
    public function addDebug($txt)
    {
        if ('debug' == $this->_tryb) {
            addLog($txt);
        }
    }

    /**
     * Zapisuje do pliku błąd.
     *
     * @internal
     *
     * @param mixed $txt Zawartość
     */
    public function addLogError($txt)
    {
        //$ip = $_SERVER['REMOTE_ADDR'];
        $this->addFile($this->_folderlog.'err_'.date('Y_m_d').'.log', $txt);
    }

    /**
     * Zapisuje plik na dysku.
     *
     * @internal
     *
     * @param string $file Ścieżka do pliku
     * @param string $txt  Zawartość
     */
    public function addFile($file, $txt)
    {
        $fp = fopen($file, 'a');    // uchwyt pliku, otwarcie do dopisania
        flock($fp, 2);              // blokada pliku do zapisu
        fwrite($fp, $txt);          // zapisanie danych do pliku
        flock($fp, 3);              // odblokowanie pliku
        fclose($fp);                // zamknięcie pliku
    }

    public function saveFile($file, $txt)
    {
        $fp = fopen($this->_folderlog.$file, 'w');    // uchwyt pliku, otwarcie do dopisania
        flock($fp, 2);              // blokada pliku do zapisu
        fwrite($fp, $txt);          // zapisanie danych do pliku
        flock($fp, 3);              // odblokowanie pliku
        fclose($fp);                // zamknięcie pliku
    }

    /**
     * Wyświetla alert.
     *
     * @internal
     *
     * @param string $msg Tekst do wyświetlenia
     */
    public function alert($msg)
    {
        echo "<script type=\"text/javascript\">alert('$msg');</script>";
    }
    
    public static function dump() {
        ob_end_clean();
        self::dumpBacktrace(debug_backtrace());
        foreach (func_get_args() as $ii => $item) {
            self::dumpParam($ii + 1, $item);
        }
        exit;
    }
    
    protected static function dumpBacktrace($backtrace) {
        echo '<code><pre>';
        echo qDebug::getInstance()->formatLog($backtrace);
        echo '</pre></code>';
    }
    
    protected static function dumpParam($i, $param) {
        echo "#{$i}";
        echo '<code><pre>';
        echo var_export($param).'<br>';
        echo '</pre></code>';
    }
}

qLog::init();
