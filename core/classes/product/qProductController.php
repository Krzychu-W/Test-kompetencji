<?php
/**
 * Kontroler materiałów
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class qProductController extends qControllerAction {

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
            qLayout::title('Lista materiałów');
            $submenu = new qSubmenu();
            $submenu->add('Dodaj nowe materiał', qHref::link('product/new'));
            qLayout::set('submenu', $submenu->render());
            $pTable = new \Alteris\Product\Table();
            $block = new qTemplate();
            $sql  = "SELECT A.*, B.name AS unit_name\n";
            $sql .= "  FROM `product` AS A\n";
            $sql .= "  LEFT JOIN `unit` AS B ON B.id = A.`unit_id`\n";
            $sql .= " ORDER BY A.`name`";
            $block->items = \qDb::connect()->select($sql)->rows();
            qLayout::set('content', $block->render('product/list'));
        }
        else if ($action === 'edit') {
            // nadanie tytułu strony
            qLayout::title('Edycja materiału');
            $id = qCtrl::arg(0, 0);
            if ($id) {
                $pTable = new \Alteris\Product\Table();
                $record = $pTable->getRecord($id);
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
            qLayout::title('Nowy materiał');
            $pTable = new \Alteris\Product\Table();
            $record = $pTable->newRecord();
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
                    $pTable = new \Alteris\Product\Table();
                    if ($pTable->delete($acronym)) {
                        $json->closeOverlay();
                        $json->rewrite('/product/list');
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
                    $choice->setQuestion('Czy usunąć product ?');
                    $choice->danger();
                    $choice->addItemDanger('Tak', 'product/delete/'.$acronym.'/commit');
                    $choice->addItem('Nie', 'product/delete/'.$acronym.'/uncommit');
                    $json->openOverlay($choice->html(), array('transition' => 'fade', 'closeButton' => 'false'));
                }
            }
            else {
                $json->openOverlay('Usunięcie nie powiodło się', array('transition' => 'fade', 'closeButton' => 'false'));
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
     * @return \Alteris\Product\Form
     */
    protected function edit(object $record):\Alteris\Product\Form
    {
        $form = new \Alteris\Product\Form($record);
        if (qCtrl::isPost()) {
            $form->renderPost(qCtrl::itemArray('product'));
            if ($form->validate()) {
                $record->setValues($form->values());
                $id = $record->save();
                if ($id) {
                    qMessage::info('Zapis zakończył się sukcesem');
                    qCtrl::location('product/list');
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

