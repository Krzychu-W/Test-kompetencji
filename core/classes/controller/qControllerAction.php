<?php

abstract class qControllerAction {
    
    protected $checkAccess = false;

    protected $_defaultAction = 'index';
    
    protected $from = [];
    
    protected $args = [];


    public function setFrom($from) {
        $this->from = $from;
    }
    
    public function getFrom() {
        return $this->from;
    }
    
    public function setArgs($args) {
        $this->args = $args;
    }
    
    public function getArgs() {
        return $this->args;
    }
    
    public function getArg($nr, $default = null) {
        if (isset($this->args[$nr])) {
            return $this->args[$nr]; 
        }
        return $default;
    }
    

    protected function setCheckAccess($check)
    {
        $this->checkAccess = (true === $check);
    }

    public function hasCheckAccess()
    {
        return $this->checkAccess;
    }

    /**
     * Lista dostępów
     * returm tablica z sumbolami dostępi.
     */
    public static function acl()
    {
        return array();
    }

    public function __get($aKey)
    {
        return '';
    }

    public function __set($aKey, $aValue)
    {
    }

    public static function info()
    {
        return array();
    }

    public function redir($path = false)
    {
        if (false !== $path) {
            qCtrl::init($path);
        }
        qCtrl::redir(true);

        return;
    }

    public function goBack()
    {
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit;
    }

    public function location($internalLink, $params = [])
    {
        $link = qHref::url($internalLink);
        header('Location: '.$link);
        exit;
    }

    public function header($link)
    {
        header('Location: '.$link);
        exit;
    }

    public function back()
    {
        header('Location: '.qConfig::getSys('back', qCtrl::baseUrl()));
        exit;
    }

    public function headerPost($link, $params = array())
    {
        $str = '';
        foreach ($params as $key => $value) {
            if ('' != $str) {
                $str .= '&';
            }
            $str .= $key.'='.$value;
        }
        header("method: POST\r\n");
        //header("Host: localhost\r\n");
        header("Content-Type: application/x-www-form-urlencoded\r\n");
        header('Content-Length: '.strlen($str)."\r\n");
        header($str."\r\n\r\n");
        header("Connection: close\r\n\r\n");
        header('Location: '.$link."\r\n");
        exit;
    }

    public function redir301($qend, $params = array())
    {
        $link = qHref::url($qend);
        header('Location: '.$link);
        exit;
    }

    public function error($number, $array = array())
    {
        qCtrl::init('index/error/'.$number);
        qCtrl::setItems($array);
        qCtrl::redir(true);
    }

    public function sendAjaxXml($ajaxXml)
    {
        header('Content-Type: text/xml');
        echo $ajaxXml->get();
        exit;
    }

    public function sendCss($content)
    {
        header('Content-Type: text/css');
        echo $content;
        exit;
    }

    public function sendJs($content)
    {
        header('Content-Type: application/x-javascript');
        echo $content;
        exit;
    }

    public function sendFile($file_path, $download = false)
    {
        $mime = qMimeType::get($file_path);
        if ($download) {
            header("Content-disposition: attachment; filename={$download}");
        }
        header('Content-Type: '.$mime);
        $file = @fopen($file_path, 'rb');
        if ($file) {
            while (!feof($file)) {
                echo fread($file, 1024 * 8);
                flush();
                if (0 != connection_status()) {
                    @fclose($file);
                }
            }
            @fclose($file);
        }
        exit;
    }

    public function sendContent($content)
    {
        $file = '';
        $line = 0;
        header('Content-Type: text/html');
        echo $content;
        exit;
    }

    public function access()
    {
        return true;
    }

    public function format($name = false)
    {
        if ($name) {
            return qContent::format($name);
        } else {
            return qContent::format();
        }
    }

    public function setFormat($name) {
        qContent::setFormat($name);
    }

    public function isJson()
    {
        return $this->format(qContent::FORMAT_JSON);
    }

    public function isXml()
    {
        return $this->format(qContent::FORMAT_XML);
    }

    public function isHtml()
    {
        return $this->format(qContent::FORMAT_HTML);
    }

    public function isLayout()
    {
        return $this->format(qContent::FORMAT_LAYOUT);
    }

    public function isContent()
    {
        return $this->format(qContent::FORMAT_CONTENT);
    }

    public function Action()
    {
        $sub = qCtrl::arg(0, false);
        if (!$sub) {
            $sub = $this->_defaultAction;
        }
        qCtrl::subAction($sub);
        // przesunięcie Ctrl
        $arg = qCtrl::args();
        unset($arg[0]);
        $nArg = array();
        foreach ($arg as $value) {
            $nArg[] = $value;
        }
        qCtrl::setArgs($nArg);
        $action = $sub.'Action';
        if (method_exists($this, $action)) {
            return call_user_func_array([$this, $action], $nArg); // params as args
      //return $this->$action();
        } else {
            if (qConfig::get('page-404')) {
                $this->redir('index/page404');

                return;
            }

            return 'brak akcji: '.qCtrl::module().'/'.qCtrl::action().'/'.qCtrl::subAction();
        }
    }

    public function page404()
    {
        //if (Content::format(Content::FORMAT_JSON)) {
        //    $json = new AjaxJson();
        //    $json->errorMessage('Nie masz uprawnień');

        //    return $json;
        //}
        $obj = new qErrorNo404();
        $obj->render();
    }

    public function page403()
    {
        if (qContent::format(qContent::FORMAT_JSON)) {
            $json = new qAjaxJson();
            $json->errorMessage('Nie masz uprawnień');

            return $json;
        }

        qLayout::$layoutTpl = 'layout';
        qBreadcrumb::reset();
        $this->redir('index/page403');
    }
    
    public function getPathModule() {
        $path = dirname(__DIR__);
        if (isset($this->from[0])) {
            $path .= DS.$this->from[0];
        }
        return $path;
    }
    
}
