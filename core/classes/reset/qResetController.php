<?php
/**
 * Kontroler resetu bazy danych
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class qResetController extends qControllerAction {

    public function action() {

        // kontrola metody
        $argsControl = $this->getArgs();
        if (count($argsControl) == 0 || $argsControl[0] === 'index') {
            $action = 'page';
        }
        else {
            $action = $this->getArg(0);
        }

        // obsługa metod
        if ($action === 'page') {
            qLayout::title('Resetowanie bazy SQL');
            $block = new qTemplate();
            qLayout::set('content', $block->render('reset/page'));
        }
        elseif ($action === 'sql') {
            $json = new qAjaxJson();

            $commit = $this->getArg(1, 'none');
            if ('commit' === $commit) {
                $connect = qDb::connect();
                $sql = "TRUNCATE `group`";
                $connect->query($sql);
                $sql = "TRUNCATE `product`";
                $connect->query($sql);
                $sql = "TRUNCATE `unit`";
                $connect->query($sql);
                $json->rewrite('/group/list');
                $path = qConfig::get('path.base');

                if ($path !== DIRECTORY_SEPARATOR) {
                    $path .= DIRECTORY_SEPARATOR.'extra'.DIRECTORY_SEPARATOR.'Alteris';
                    $path .= DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR;

                }
                $sql = file_get_contents($path.'insert001.sql');
                if ($sql) {
                    $connect->query($sql);
                }
                $sql = file_get_contents($path.'insert002.sql');
                if ($sql) {
                    $connect->query($sql);
                }
                $sql = file_get_contents($path.'insert003.sql');
                if ($sql) {
                    $connect->query($sql);
                }
            }
            else if ('uncommit' === $commit) {
                $json->closeOverlay();
            }
            else {
                // alter
                $choice = new qChoiceOverlay();
                $choice->setQuestion('Czy zresetować bazę danych ?');
                $choice->danger();
                $choice->addItemDanger('Tak', 'reset/sql/commit');
                $choice->addItem('Nie', 'reset/sql/uncommit');
                $json->openOverlay($choice->html(), array('transition' => 'fade', 'closeButton' => 'false'));
            }

            qContentJson::setJson($json);
        }
        else {
            $this->page404();
        }
        return true;
    }

}

