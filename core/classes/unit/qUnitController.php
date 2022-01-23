<?php
/**
 * Kontroler jednostek miar
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class qUnitController extends qControllerAction {

    public function action() {

        // kontrola metody
        $argsControl = $this->getArgs();
        if (count($argsControl) == 0 || $argsControl[0] === 'index') {
            $action = 'list';
        }
        else {
            $action = $this->getArg(0);
        }

        // obsługa metod
        if ($action === 'list') {
            // nadanie tytułu strony
            qLayout::title('Lista jednostek miar');
            $submenu = new qSubmenu();
            $submenu->add('Dodaj nową jednostkę', qHref::link('unit/new'));
            qLayout::set('submenu', $submenu->render());
            $uTable = new \Alteris\Unit\Table();
            $block = new qTemplate();
            $sql = "SELECT *, (SELECT count(*) FROM `product` AS B WHERE B.`unit_id` = A.id) as count FROM `unit` AS A ORDER BY `name`";
            $block->items = \qDb::connect()->select($sql)->rows();
            qLayout::set('content', $block->render('unit/list'));
        }
        else if ($action === 'edit') {
            // nadanie tytułu strony
            qLayout::title('Edycja jednostki miar');
            $id = qCtrl::arg(0, 0);
            if ($id) {
                $uTable = new \Alteris\Unit\Table();
                $record = $uTable->getRecord($id);
                $block = new qTemplate();
                $block->form = $this->edit($record);
                qLayout::set('content', $block->render('form/content'));
            }
            else {
                $this->page404();
            }
        }
        else if ($action === 'new') {
            // nadanie tytułu strony
            qLayout::title('Nowa jednostka miary');
            $uTable = new \Alteris\Unit\Table();
            $record = $uTable->newRecord();
            $block = new qTemplate();
            $block->form = $this->edit($record);
            qLayout::set('content', $block->render('form/content'));
        }
        else if ($action === 'delete') {
            $json = new qAjaxJson();
            $acronym = $this->getArg(1);
            if ($acronym) {
                $commit = $this->getArg(2, 'none');
                if ('commit' === $commit) {
                    $uTable = new \Alteris\Unit\Table();
                    if ($uTable->delete($acronym)) {
                        $json->closeOverlay();
                        $json->rewrite('/unit/list');
                        qMessage::info('Usunięcie zakończone sukcesem');
                    }
                    else {
                        $json->closeOverlay();
                        $json->openOverlay('Usunięcie nie powiodło się', array('transition' => 'fade', 'closeButton' => 'false'));
                    }
                }
                else if ('uncommit' === $commit) {
                    $json->closeOverlay();
                }
                else {
                    // alter
                    $choice = new qChoiceOverlay();
                    $choice->setQuestion('Czy usunąć jednostkę miar ?');
                    $choice->danger();
                    $choice->addItemDanger('Tak', 'unit/delete/'.$acronym.'/commit');
                    $choice->addItem('Nie', 'unit/delete/'.$acronym.'/uncommit');
                    $json->openOverlay($choice->html(), array('transition' => 'fade', 'closeButton' => 'false'));
                }
            }
            else {
                $json->openOverlay('Błędne parametry', array('transition' => 'fade', 'closeButton' => 'false'));
            }
            qContentJson::setJson($json);

        }
        else {
            $this->page404();
        }
        return true;
    }

    /**
     * Budowa formularza i zapis rekordu po walidacji
     *
     * @param object $record
     * @return \Alteris\Unit\Form
     */
    protected function edit(object $record):\Alteris\Unit\Form
    {
        $form = new \Alteris\Unit\Form($record);
        if (qCtrl::isPost()) {
            $form->renderPost(qCtrl::itemArray('unit'));
            if ($form->validate()) {
                $record->setValues($form->values());
                $id = $record->save();
                if ($id) {
                    qMessage::info('Zapis zakończył się sukcesem');
                    qCtrl::location('unit/list');
                }
                else {
                    qMessage::error('Zapis nie powiódł się');
                }
            }
            else {
                qMessage::error('Proszę wypełnić poprawnie wszystkie wymagane pola');
            }
        }
        return $form;
    }
}

