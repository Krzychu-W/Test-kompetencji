<?php

class qDebug
{
    protected $tryb;
    private $lines;

    protected static $instance;

    public function __construct($tryb = true)
    {
        if ($tryb) {
            $this->_tryb = true;
        } else {
            $this->tryb = false;
        }
        $this->lines = array();
    }

    public static function init($tryb = true)
    {
        self::$instance = new self($tryb);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function line($backtrace, $param = false)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance->add($backtrace, $param);
    }

    public static function getToHtml()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance->toHtml();
    }

    public static function getToLog()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance->toLog();
    }

    public function add($backtrace, $params = false)
    {
        $this->lines[] = $backtrace;
    }

    /**
     * @return mixed|string
     */
    public function toHtml()
    {
        $res = '';
        foreach ($this->lines as $backtrace) {
            $res .= $this->format($backtrace);
        }

        return str_replace("\n", '<br />', $res);
    }

    public function toLog()
    {
        $res = '';
        foreach ($this->lines as $backtrace) {
            $res .= $this->format($backtrace);
        }
    }

    public function format($arrayBacktrace)
    {
        $str = '';
        $count = count($arrayBacktrace);
        foreach ($arrayBacktrace as $key => $backtrace) {
            //$s  = 'Call '.$backtrace->object.$backtrace->class.$backtrace->type.$backtrace->function."\n";
            $s = 'Call '.$backtrace['class'].$backtrace['type'].$backtrace['function'].' from :'.$backtrace['file'].'('.$backtrace['line'].')'."\n";
            if (count($backtrace['args']) >= 0) {
                $s .= qLog::getInstance()->arrayToLog($backtrace['args'], '', 'parameters');
            }
            if ($count > 1) {
                $str .= '['.$key.']'.$s;
            } else {
                $str .= $s;
            }
        }

        return $str;
    }

    public function formatLog($arrayBacktrace)
    {
        $str = '';
        foreach ($arrayBacktrace as $key => $backtrace) {
            if (1 == $key) {
                $s = '#--- Log from '.$backtrace['file'].'('.$backtrace['line'].')'."\n";
                $str = $s.$str;
            } elseif ($key >= 2) {
                if (isset($backtrace['class'])) {
                    $s = '#--- '.$backtrace['class'].$backtrace['type'].$backtrace['function'];
                    if (isset($backtrace['args']) && count($backtrace['args']) >= 0) {
                        $s .= '(<parameters>)';
                    } else {
                        $s .= '()';
                    }
                    $file = '<no file>';
                    if (isset($backtrace['file'])) {
                        $file = $backtrace['file'];
                    }
                    $line = 0;
                    if (isset($backtrace['line'])) {
                        $line = $backtrace['line'];
                    }
                    $s .= ' from '.$file.'('.$line.')'."\n";
                } else {
                    try {
                        $s = '#--- '.serialize($backtrace).'()'."\n";
                    } catch (Exception $e) {
                        $s = '#--- '."\n";
                    }
                }
                $str = $s.$str;
            }
        }

        return $str;
    }

    public function debug_string_backtrace()
    {
        ob_start();
        debug_print_backtrace();
        $res = ob_get_contents();
        ob_end_clean();

        // Remove first item from backtrace as it's this function which
        // is redundant.
        $res = preg_replace('/^#0\s+'.__FUNCTION__."[^\n]*\n/", '', $res, 1);

        // Renumber backtrace items.
        $res = preg_replace('/^#(\d+)/me', '\'#\' . ($1 - 1)', $res);

        return $res;
    }
}
