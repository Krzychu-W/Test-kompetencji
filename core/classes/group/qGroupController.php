<?php

class qGroupController extends qControllerAction {
    
    public function action() {
        
        $argsControl = $this->getArgs();
        if (count($argsControl) == 0 || $argsControl[0] === 'index') {
            $action = 'list';
        }
        else {
            $action = $this->getArg(0);
        }
        if ($action === 'list') {
            qLayout::title('Lista grup materiałów');
            $block = new qTemplate();
            qLayout::set('content', $block->render('group/list'));

            $table = new Alteris\Unit\Table(10);
            $obj = $table->getRecord(10);
            $obj->name = 'Korzec33';
            $xx = $obj->save();
            qLog::dump($xx);
        }
        else {
            $this->page404();
        }
        return true;
    }
    
    
}

