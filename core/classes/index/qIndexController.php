<?php

class qIndexController extends qControllerAction {
    
    public function action() {
        $action = $this->getArg(0, 'index');
        if ($action === 'index') {
            $this->redir('group/list');
        }
        else if ($action === 'nomodule') {
            if (qConfig::get('page-404')) {
               $this->redir('index/page404');

               return;
            }
            qLayout::set('content', 'brak modułu: '.implode('/', qCtrl::args()));
        }
        else if ($action === 'error') {
            $args = qCtrl::args();
            $desctiption = 'Strony nie znaleziono';
            $nr = $this->getArg(1, '???');
            if ('404' == $nr) {
                $desctiption .= ', 404: '.implode('/', $args);
            } 
            else if ('410' == $nr) {
                $desctiption .= ', 410: '.implode('/', $args);
            } 
            else {
                $desctiption .= ': '.implode('/', $args);
            }
            qLayout::set('content', $desctiption);
        }
        else if ($action === 'cms') {
            $block = new qTemplate();
            qLayout::title('System zarządzania treścią');
            qLayout::set('content', $block->render('index/cms'));
        }
        else {
            $this->page404();
        }
        return true;
    }
    
    
}

