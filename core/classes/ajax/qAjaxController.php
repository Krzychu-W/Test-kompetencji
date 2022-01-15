<?php

class qAjaxController extends qControllerAction {
    
    public function action() {
        $action = $this->getArg(0);
        if ($action === 'param') {
            $args = $this->getArgs();
            $newArgs = array_slice($args , 1);
            $control = new qController();
            $control->setFormat(qContent::FORMAT_JSON);
            $control->action($newArgs);
            if (qContentJson::hasJson()) {
                qContentJson::output();
            }
            qLog::write('Błąd ajax`a', 'Action: ', $action, $_SERVER, $result);
            $this->alert('Błąd:'.(string) $result);
        }
        else if ($action === 'stringify') {
            $args = $this->getArgs();
            $newArgs = array_slice($args , 1);
            if (qCtrl::isPost()) {
            $params = qCtrl::post();
            }
            else {
                $params = qCtrl::get();
            }

            if (isset($params[params])) {
                $newParam = (array)json_decode($params[params]);
                qCtrl::setItems($newParam);
            }
            $control = new qController();
            $control->setFormat(qContent::FORMAT_JSON);
            $control->action($newArgs);
            if (qContentJson::hasJson()) {
                
                qContentJson::output();
            }
            qLog::write('Błąd ajax`a', 'Action: ', $action, $_SERVER, $result);
            $this->alert('Błąd:'.(string) $result);


            exit;
        }
        else if ($action === 'html') {
            // DO ZROBIENIA
            // przekodowanie argumentów
            qCtrl::module('index');
            qCtrl::action('index');
            $newArgs = array();
            foreach (qCtrl::args() as $key => $arg) {
                if (0 == $key) {
                    qCtrl::module($arg);
                } elseif (1 == $key) {
                    qCtrl::action($arg);
                } else {
                    $newArgs[] = $arg;
                }
            }
            qCtrl::setArgs($newArgs);
            $action = qCtrl::actionMethod();
            $result = 'Jakiś błąd';
            if (false === $action) {
                $result = 'Błędny adres';
            } else {
                $obj = $action->obj;
                $obj->setFormat(qContent::FORMAT_HTML);
                $act = $action->method;
                $result = $obj->$act();
            }

            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: text/html');
            echo $result;
            exit;
        }
        else if ($action === 'logger') {
            $params = qCtrl::items();
            $info = 'Błąd ajaxa dla adresu: '.$params['url'].', metoda: '.$params['method'];
            if (isset($params['parameters'])) {
                qLog::write($info, 'parametry:', $params['parameters'], 'error: '.$params['result']);
            }
            else {
                qLog::write($info, 'error: '.$params['result']);
            }
            exit;
        }
        $this->alert('Błąd akcji:'.$action);
    }
    
    protected function alert($message) {
        $err = new qAjaxJson();
        $err->alert($message);
        qContentJson::setJson($err);
        qContentJson::output();
    }
    
}

