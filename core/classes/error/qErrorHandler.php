<?php

/**
 * Obsługa błędu
 */
class qErrorHandler {
    public static $errors = array();
    public static $is = false;

    public static function fatal_handler()
    {
        $errfile = 'unknown file';
        $errstr = 'shutdown';
        $errno = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if (null !== $error) {
            $errno = $error['type'];
            $errfile = $error['file'];
            $errline = $error['line'];
            $errstr = $error['message'];
        }

        qLog::write('FATAL ERROR', $errno, $errfile, $errline, $errstr);
    }

    public static function isError()
    {
        return self::$is;
    }

    public static function error_handler($no, $str, $file, $line)
    {
        self::$is = true;
        if (2048 != $no) {
            $filename = basename($file);
            $filename = substr($filename, 0, strpos($filename, '.php'));

            if ('BlockFileParse' == $filename) {
                $trace = debug_backtrace();

                $html = ($trace[0]['args'][4]['tplRender']);
                $html = explode("\n", $html);
                $htmlLineStart = ($line - 1) - 10;
                $htmlLineStop = ($line - 1) + 11;
                $lines = [];
                $xFileName = $html[0];
                $tpls = [];

                foreach ($html as $i => $txt) {
                    if ($i == $line) {
                        break;
                    }

                    if (0 === strpos($txt, '<!-- include begin: ')) {
                        $tpls[] = substr($txt, 20, -4);
                    }
                }

                $file = end($tpls);

                for ($i = $htmlLineStart; $i < $htmlLineStop; ++$i) {
                    if (isset($html[$i])) {
                        $lines[] = $html[$i];
                    }
                }

                foreach ($trace as $item) {
                    if (isset($item['object']) && $item['object'] instanceof BlockFileParse) {
                        $filename = $item['object']->file;
                    }
                }

                qLog::screen('TPL ERROR', 'File: '.$xFileName, 'Number error: '.$no, 'Description: '.$str, "TPL source:\n".implode("\n", $lines).'');
            } else {
                qLog::screen('PHP ERROR', 'File: '.$file, 'Line: '.$line, 'Number error: '.$no, 'Description: '.$str);
            }
        }
    }
}

