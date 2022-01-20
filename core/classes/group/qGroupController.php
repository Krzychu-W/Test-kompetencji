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
            qLayout::title('Lista grup materiałów');
            $block = new qTemplate();
            qLayout::set('content', $block->render('group/list'));

            $table = new Alteris\Unit\Table();
            $obj = $table->getRecord(11);


            $obj->name = 'Korzec33';
            $xx = $obj->save();

        }
        else {
            $this->page404();
        }
        return true;
    }
    
    
}

