<?php

class qUnitController extends qControllerAction {
    
    public function action() {
        
        $argsControl = $this->getArgs();
        if (count($argsControl) == 0 || $argsControl[0] === 'index') {
            $action = 'list';
        }
        else {
            $action = $this->getArg(0);
        }
        if ($action === 'list') {
            qLayout::title('Lista jednostek miar');
            $block = new qTemplate();
            qLayout::set('content', $block->render('unit/list'));
        }
        else {
            $this->page404();
        }
        return true;
    }
    
    
}

