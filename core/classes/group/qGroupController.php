<?php
/**
 * Kontroler grup
 *
 * @author Krzysztof Wałek <krzysztof@struktury.net>
 */
class qGroupController extends qControllerAction {

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
            qLayout::title('Lista grup materiałów');
            $submenu = new qSubmenu();
            $submenu->add('Dodaj nową grupę', qHref::link('group/new'));
            qLayout::set('submenu', $submenu->render());
            //$gTable = new \Alteris\Group\Table();
            //$gTable->resetHierarchy();
            $block = new qTemplate();
            $sql  = "SELECT A.*, (SELECT count(*) FROM `product` AS B WHERE B.group_id=A.id) as prods\n";
            $sql .= ",(SELECT count(*)-1 FROM `group` AS C WHERE C.hierarchy LIKE CONCAT(A.hierarchy ,'%')) as sub\n";
            $sql .= "  FROM `group` AS A\n";
            $sql .= " ORDER BY A.`hierarchy`";
            $block->items = \qDb::connect()->select($sql)->rows();
            qLayout::set('content', $block->render('group/list'));
        }
        else if ($action === 'edit') {
            // nadanie tytułu strony
            qLayout::title('Edycja grupy');
            $id = qCtrl::arg(0, 0);
            if ($id) {
                $gTable = new \Alteris\Group\Table();
                $record = $gTable->getRecord($id);
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
            qLayout::title('Nowa grupa');
            $gTable = new \Alteris\Group\Table();
            $record = $gTable->newRecord();
            $record->parent_id = qCtrl::arg(0, 0);
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
                    $gTable = new \Alteris\Group\Table();
                    if ($gTable->delete($acronym)) {
                        $json->closeOverlay();
                        $json->rewrite('/group/list');
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
                    $choice->setQuestion('Czy usunąć grupę ?');
                    $choice->danger();
                    $choice->addItemDanger('Tak', 'group/delete/'.$acronym.'/commit');
                    $choice->addItem('Nie', 'group/delete/'.$acronym.'/uncommit');
                    $json->openOverlay($choice->html(), array('transition' => 'fade', 'closeButton' => 'false'));
                }
            }
            else {
                $json->openOverlay('Usunięcie nie powiodło się', array('transition' => 'fade', 'closeButton' => 'false'));
            }
            qContentJson::setJson($json);
        }
        else if ($action === 'tree') {
            $id = $this->getArg(1, 0);
            $table = new \Alteris\Group\Table();
            $record = $table->getRecord($id);
            $name = 'Wykonane dla grupy: '.$record->id.': '. $record->name;
            $tree = new \Alteris\Group\Tree($record);
            qLog::dump($name, $tree);
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
     * @return \Alteris\Group\Form
     */
    protected function edit(object $record):\Alteris\Group\Form
    {
        $form = new \Alteris\Group\Form($record);
        if (qCtrl::isPost()) {
            $form->renderPost(qCtrl::itemArray('group'));
            if ($form->validate()) {
                $record->setValues($form->values());
                $id = $record->save();
                if ($id) {
                    qMessage::info('Zapis zakończył się sukcesem');
                    qCtrl::location('group/list');
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

