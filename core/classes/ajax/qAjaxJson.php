<?php

class qAjaxJson
{
    private $items = array();

    public function __construct()
    {
    }

    public function addEval($javascript)
    {
        $this->items[] = array('type' => 'eval', 'javascript' => $javascript);
    }

    public function addLoad($url, $param = '', $method = 'POST')
    {
        $this->load($url, $param, $method);
    }

    public function load($url, $param = '', $method = 'POST', $async = true, $oncomplete = '', $loader = true)
    {
        $this->items[] = array(
            'type' => 'load',
            'url' => $url,
            'param' => $param,
            'method' => $method,
            'async' => $async,
            'oncomplete' => $oncomplete,
            'loader' => $loader,
          );
    }

    public function rewrite($url)
    {
        if ('/' != substr($url, 0, 1) && 'http://' != substr($url, 0, 7) && 'https://' != substr($url, 0, 8)) {
            $lang = qCtrl::lang();
            if($lang !== 'pl') {
                $url = $lang.'/' . $url;
            }
            $url = '/'.$url;
        }
        $this->items[] = array('type' => 'rewrite', 'url' => $url);
    }

    public function delRewrite()
    {
        foreach ($this->items as $key => $item) {
            if ('rewrite' == $item['type']) {
                unset($this->items[$key]);
            }
        }
    }

    public function reload()
    {
        $this->items[] = array('type' => 'reload');
    }

    public function addPost($url, $params)
    {
        $this->items[] = array('type' => 'post', 'url' => $url, 'params' => $params);
    }

    public function alert($text)
    {
        $this->items[] = array('type' => 'alert', 'text' => $text);
    }

    public function addItem($tab, $item)
    {
        $this->items[$tab][] = $item;
    }

    public function encode()
    {
        return json_encode($this->items);
    }

    public function html($selector, $html)
    {
        $this->items[] = array('type' => 'html', 'selector' => $selector, 'html' => $html);
    }

    public function val($selector, $value)
    {
        $this->items[] = array('type' => 'val', 'selector' => $selector, 'value' => $value);
    }

    public function replaceWith($selector, $html)
    {
        $this->items[] = array('type' => 'replaceWith', 'selector' => $selector, 'html' => $html);
    }

    public function append($selector, $html)
    {
        $this->items[] = array('type' => 'append', 'selector' => $selector, 'html' => $html);
    }

    public function after($selector, $html)
    {
        $this->items[] = array('type' => 'after', 'selector' => $selector, 'html' => $html);
    }

    public function setCss($selector, $aCss, $value = null)
    {
        if (is_string($aCss)) {
            $aCss = array($aCss => $value);
        }
        $this->items[] = array('type' => 'css', 'selector' => $selector, 'css' => $aCss);
    }

    public function addClass($selector, $class)
    {
        $this->items[] = array('type' => 'class', 'selector' => $selector, 'class' => $class, 'action' => 'add');
    }

    public function removeClass($selector, $class)
    {
        $this->items[] = array('type' => 'class', 'selector' => $selector, 'class' => $class, 'action' => 'remove');
    }

    public function remove($selector)
    {
        $this->items[] = array('type' => 'remove', 'selector' => $selector);
    }

    public function toggleClass($selector, $class)
    {
        $this->items[] = array('type' => 'class', 'selector' => $selector, 'class' => $class, 'action' => 'toggle');
    }

    public function setClass($selector, $class)
    {
        $this->items[] = array('type' => 'class', 'selector' => $selector, 'class' => $class, 'action' => 'set');
    }

    public function addAttr($selector, $attr, $value)
    {
        $this->items[] = array('type' => 'attr', 'selector' => $selector, 'attr' => $attr, 'value' => $value, 'action' => 'add');
    }

    public function removeAttr($selector, $attr)
    {
        $this->items[] = array('type' => 'attr', 'selector' => $selector, 'attr' => $attr, 'action' => 'remove');
    }

    public function infoBox($message, $class = 'succeed')
    {
        $this->items[] = array('type' => 'infoBox', 'html' => $message, 'class' => $class);
    }

    public function openOverlay($html, $class = false, $js = []) {
        if (is_array($class)) {
            $params = $class;
            $class = false;
            if (isset($params['class'])) {
                $class = $params['class'];
            }
        }
        $item = [
            'type' => 'overlayOpen',
            'html' => $html,
            'class' => $class,
        ];
        if (count($js) > 0) {
            $item['eval'] = $js;
        }
        $this->items[] = $item;
    }

    public function closeOverlay() {
        $this->items[] = array('type' => 'overlayClose');
    }

    public function errorMessage($txt, $button = 'OK')
    {
        $this->message($txt, 'colorbox-error-message', $button);
    }

    public function successMessage($txt, $button = 'OK')
    {
        $this->message($txt, 'colorbox-success-message', $button);
    }

    public function infoMessage($txt, $button = 'OK')
    {
        $this->message($txt, 'colorbox-info-message', $button);
    }

    public function messageBox($title, $txt)
    {
        $html = '<div style="min-width: 400px" class="form-colorbox"><h4>'.$title.'</h4><div class="content">'.$txt.'</div></div>';
        $this->addColorBox($html, array('minWidth' => '200px'));
    }

    public function message($txt, $class, $button)
    {
        $html = '<div class="message '.$class.'"><span class="icon"></span>';
        $html .= '<div class="txt">';
        $html .= '  <div><h4><p class="text-center">'.$txt.'</p></h4></div>';
        $html .= '</div>';
        $html .= '<div class="btns">';
        $html .= '  <a class="btn" onclick="$.colorbox.close();">'.$button.'</a>';
        $html .= '</div>';
        $html .= '</div>';
        $this->addColorBox($html, array('transition' => 'fade', 'closeButton' => false, 'maxWidth' => '95%'));
    }

    public function value($return)
    {
        if (is_array($return) || is_object($return)) {
            $return = json_encode($return);
        }
        $this->items[] = array('type' => 'return', 'value' => $return);
    }

    public function items()
    {
        return $this->items;
    }

    public function formElement($selector, $url)
    {
        $this->items[] = array(
      'type' => 'formElement',
      'selector' => $selector,
      'url' => $url,
    );
    }

    private function _json_encode($val)
    {
        if ('@' == substr($val, 0, 1)) {
            return substr($val, 1, strlen($val) - 1);
        }
        if (is_string($val)) {
            return '"'.addslashes($val).'"';
        }
        if (is_numeric($val)) {
            return $val;
        }
        if (null === $val) {
            return 'null';
        }
        if (true === $val) {
            return 'true';
        }
        if (false === $val) {
            return 'false';
        }

        $assoc = false;
        $i = 0;
        foreach ($val as $k => $v) {
            if ($k !== $i++) {
                $assoc = true;
                break;
            }
        }
        $res = array();
        foreach ($val as $k => $v) {
            $v = $this->_json_encode($v);
            if ($assoc) {
                $k = '"'.addslashes($k).'"';
                $v = $k.':'.$v;
            }
            $res[] = $v;
        }
        $res = implode(',', $res);

        return ($assoc) ? '{'.$res.'}' : '['.$res.']';
    }
}
