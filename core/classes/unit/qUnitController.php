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

            $uTable = new \Alteris\Unit\Table();
            $block = new qTemplate();
            $block->items = $uTable->getAllRecords();
            qLayout::set('content', $block->render('unit/list'));
        }
        else {
            $this->page404();
        }
        return true;
    }


}

